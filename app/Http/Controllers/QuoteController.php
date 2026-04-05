<?php

namespace App\Http\Controllers;

use App\Models\CustomerJob;
use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $validated = $request->validate([
            'delivery_cost' => ['nullable', 'numeric', 'min:0'],
            'discount_offered' => ['nullable', 'numeric', 'min:0'],
            'price_for_job' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $deliveryCost = (float) ($validated['delivery_cost'] ?? 0);
        $discountOffered = (float) ($validated['discount_offered'] ?? 0);
        $priceForJob = (float) $validated['price_for_job'];
        $totalPrice = max(0, $deliveryCost + $priceForJob - $discountOffered);

        Quote::updateOrCreate(
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
                'status' => 'submitted',
                'sent_at' => now(),
            ]
        );

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
            ->with(['quotes.supplier.supplierProfile'])
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
            'status' => ['required', 'in:accepted,rejected,submitted'],
        ]);

        $quote->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Quote status updated successfully.');
    }
}
