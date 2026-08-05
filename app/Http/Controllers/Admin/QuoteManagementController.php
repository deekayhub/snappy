<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerJob;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class QuoteManagementController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $quotes = Quote::query()
                ->with(['job.user', 'job.categoryId', 'supplier.supplierProfile'])
                ->latest();

            if ($request->filled('status')) {
                $quotes->where('quotes.status', $request->string('status')->toString());
            }

            if ($request->filled('job_status')) {
                $jobStatus = $request->string('job_status')->toString();

                $quotes->whereHas('job', function ($query) use ($jobStatus) {
                    if ($jobStatus === 'open') {
                        $query->where('customer_jobs.status', 'open');
                    } else {
                        $query->where('customer_jobs.status', '!=', 'open');
                    }
                });
            }

            return DataTables::eloquent($quotes)
                ->addColumn('job_title', fn (Quote $quote) =>
                    '<div class="fw-semibold">'.e($quote->job?->title ?: 'Job removed').'</div>'
                    .'<div class="small text-muted">Job No. '.str_pad((string) $quote->customer_job_id, 4, '0', STR_PAD_LEFT).'</div>')
                ->addColumn('supplier', function (Quote $quote) {
                    $name = $quote->supplier?->supplierProfile?->company_name ?: ($quote->supplier?->name ?? '-');
                    $email = $quote->supplier?->email ?: '';

                    return '<div class="fw-semibold">'.e($name).'</div>'
                        .'<div class="small text-muted">'.e($email).'</div>';
                })
                ->addColumn('customer', fn (Quote $quote) =>
                    '<div class="fw-semibold">'.e($quote->job?->user?->name ?: '-').'</div>'
                    .'<div class="small text-muted">'.e($quote->job?->user?->email ?: '-').'</div>')
                ->editColumn('total_price', function (Quote $quote) {
                    $html = '<div class="fw-bold">£ '.number_format((float) $quote->total_price, 2).'</div>';

                    $parts = [];
                    if ((float) $quote->delivery_cost > 0) {
                        $parts[] = 'Delivery £'.number_format((float) $quote->delivery_cost, 2);
                    }
                    if ((float) $quote->discount_offered > 0) {
                        $parts[] = 'Discount −£'.number_format((float) $quote->discount_offered, 2);
                    }

                    if (! empty($parts)) {
                        $html .= '<div class="small text-muted">'.implode(' · ', $parts).'</div>';
                    }

                    return $html;
                })
                ->addColumn('job_status', fn (Quote $quote) =>
                    '<span class="quote-job-badge '.($quote->job?->status === 'open' ? 'open' : '').'">'.e(ucfirst($quote->job?->status ?? '—')).'</span>')
                ->editColumn('status', function (Quote $quote) {
                    $badge = '<span class="quote-status-badge '.e($quote->status).'">'.e(ucfirst($quote->status)).'</span>';

                    if (in_array($quote->status, ['accepted', 'completed'], true)) {
                        $badge .= '<span class="quote-winner-badge ms-1"><i class="mdi mdi-trophy-outline"></i>Winner</span>';
                    }

                    return $badge;
                })
                ->editColumn('sent_at', fn (Quote $quote) => $quote->sent_at?->format('d M Y h:i A') ?: '-')
                ->filterColumn('job_title', function ($query, $keyword) {
                    $query->where('customer_jobs.title', 'like', "%{$keyword}%");
                })
                ->rawColumns(['job_title', 'supplier', 'customer', 'total_price', 'job_status', 'status'])
                ->make(true);
        }

        $stats = [
            'total_quotes' => Quote::count(),
            'submitted_quotes' => Quote::where('status', 'submitted')->count(),
            'accepted_quotes' => Quote::where('status', 'accepted')->count(),
            'jobs_with_quotes' => CustomerJob::has('quotes')->count(),
        ];

        return view('admin.quotes.index', compact('stats'));
    }
}
