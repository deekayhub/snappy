<?php

namespace App\Http\Controllers;

use App\Mail\JobQuoteSubmittedMail;
use App\Mail\QuoteAcceptedMail;
use App\Mail\QuoteCompletedMail;
use App\Mail\QuotePendingMail;
use App\Mail\QuoteRejectedMail;
use App\Models\CustomerJob;
use App\Models\Quote;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class QuoteController extends Controller
{
    public function store(Request $request, CustomerJob $job): RedirectResponse
    {
        if (! $request->user()?->hasRole('supplier')) {
            return redirect()
                ->route('home')
                ->with('error', 'Only supplier accounts can submit quotes.');
        }

        $user = $request->user();
        $usage = $user->subscriptionUsage();

        if (! $usage['can_submit_quote']) {
            return redirect()
                ->route('supplier-panel.subscription.index')
                ->with('error', 'You have reached your quote limit for this month or year. Please upgrade your plan to submit more quotes.');
        }

        $rules = [
            'delivery_cost' => ['nullable', 'numeric', 'min:0'],
            'discount_offered' => ['nullable', 'numeric', 'min:0'],
            'price_for_job' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];

        if ($request->user()->hasFeature('professional_quote')) {
            $rules['estimated_completion_date'] = ['nullable', 'date', 'after_or_equal:today'];
            $rules['warranty_months'] = ['nullable', 'integer', 'min:0'];
            $rules['terms'] = ['nullable', 'string', 'max:5000'];
        }

        $validated = $request->validate($rules);

        $deliveryCost = (float) ($validated['delivery_cost'] ?? 0);
        $discountOffered = (float) ($validated['discount_offered'] ?? 0);
        $priceForJob = (float) $validated['price_for_job'];
        $totalPrice = max(0, $deliveryCost + $priceForJob - $discountOffered);

        $quote = Quote::updateOrCreate(
            [
                'customer_job_id' => $job->id,
                'supplier_user_id' => $request->user()->id,
            ],
            [
                'delivery_cost' => $deliveryCost,
                'discount_offered' => $discountOffered,
                'price_for_job' => $priceForJob,
                'total_price' => $totalPrice,
                'notes' => $validated['notes'] ?? null,
                'estimated_completion_date' => $validated['estimated_completion_date'] ?? null,
                'warranty_months' => $validated['warranty_months'] ?? null,
                'terms' => $validated['terms'] ?? null,
                'status' => 'submitted',
                'sent_at' => now(),
            ]
        );

        try {
            Mail::to($job->user->email)
                ->send(new JobQuoteSubmittedMail($job, $quote));
        } catch (\Throwable $e) {
            Log::error('Failed to send quote email.', [
                'job_id' => $job->id,
                'quote_id' => $quote->id,
                'customer_email' => $job->user->email,
                'error' => $e->getMessage(),
            ]);
        }

        UserNotification::create([
            'user_id' => $job->user_id,
            'type' => 'new_quote',
            'message' => "New quote received for your job: {$job->title}",
            'action_url' => route('customer-panel.quotes'),
        ]);

        return redirect()
            ->route('supplier-panel.jobs')
            ->with('success', 'Quote submitted successfully.');
    }

    public function customerIndex(Request $request): View|RedirectResponse
    {
        if (! $request->user()?->hasRole('customer')) {
            return redirect()
                ->route('home')
                ->with('error', 'Only customer accounts can view quotes.');
        }

        $jobs = $request->user()
            ->customerJobs()
            ->with([
                'quotes.supplier' => fn ($supplierQuery) => $supplierQuery
                    ->select('id', 'name', 'email')
                    ->withAvg('ratedSupplierQuotes as supplier_average_rating', 'customer_rating')
                    ->withCount('ratedSupplierQuotes as supplier_ratings_count'),
                'quotes.supplier.supplierProfile:id,user_id,company_name',
            ])
            ->latest()
            ->get();

        return view('customer-quotes.index', compact('jobs'));
    }

    public function updateStatus(Request $request, Quote $quote): RedirectResponse
    {
        if (! $request->user()?->hasRole('customer') || $quote->job?->user_id !== $request->user()->id) {
            return redirect()
                ->route('home')
                ->with('error', 'You are not allowed to update this quote.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,rejected,submitted,completed'],
        ]);

        if ($validated['status'] === 'completed' && ! in_array($quote->status, ['accepted', 'completed'], true)) {
            return back()->with('error', 'Only accepted quotes can be marked as completed.');
        }

        $quote->update([
            'status' => $validated['status'],
        ]);

        try {
            $mailable = match ($validated['status']) {
                'accepted' => new QuoteAcceptedMail($quote),
                'completed' => new QuoteCompletedMail($quote),
                'rejected' => new QuoteRejectedMail($quote),
                'submitted' => new QuotePendingMail($quote),
            };

            Mail::to($quote->supplier->email)->send($mailable);
        } catch (\Throwable $e) {
            Log::error("Failed to send quote {$validated['status']} email.", [
                'quote_id' => $quote->id,
                'supplier_email' => $quote->supplier->email,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', 'Quote status updated successfully.');
    }

    public function rateSupplier(Request $request, Quote $quote): RedirectResponse
    {
        if (! $request->user()?->hasRole('customer') || $quote->job?->user_id !== $request->user()->id) {
            return redirect()
                ->route('home')
                ->with('error', 'You are not allowed to rate this supplier.');
        }

        if ($quote->status !== 'completed') {
            return back()->with('error', 'You can rate a supplier only after marking the quote as completed.');
        }

        $validated = $request->validate([
            'customer_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'customer_review' => ['nullable', 'string', 'max:1000'],
        ]);

        $quote->update([
            'customer_rating' => $validated['customer_rating'],
            'customer_review' => $validated['customer_review'] ?? null,
            'rated_at' => now(),
        ]);

        return back()->with('success', 'Supplier rating saved successfully.');
    }
}
