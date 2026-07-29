@extends('supplier-panel.layouts.app')
@section('title', 'Won Jobs')

@section('content')
<div class="content-wrapper p-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Won Jobs</h4>
        <a href="{{ route('supplier-panel.jobs', ['tab' => 'won']) }}" class="btn btn-outline-primary rounded-4">
            <i class="mdi mdi-briefcase-search me-1"></i>View on Job Board
        </a>
    </div>

    <div class="row g-4">
        @forelse ($wonQuotes as $quote)
            @php($job = $quote->job)
            <div class="col-lg-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top: 5px solid {{ $quote->status === 'completed' ? 'var(--bs-dark)' : 'var(--bs-success)' }} !important;">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <span class="badge bg-{{ $quote->status === 'completed' ? 'dark' : 'success' }} rounded-pill">
                                {{ ucfirst($quote->status) }}
                            </span>
                            <span class="small text-muted fw-semibold">Job No. {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <h4 class="mb-2">{{ $job->title }}</h4>
                        <p class="text-muted mb-3">Posted {{ $job->created_at->diffForHumans() }}</p>

                        <div class="alert alert-light rounded p-2 mb-3" style="background-color: #f7f9fc !important;">
                            <div class="small text-muted mb-2">Category: <strong class="text-capitalize">{{ $job->categoryId?->name ?? 'General' }}</strong></div>
                            <div class="small text-muted mb-2">Organisation: <strong class="text-capitalize">{{ $job->organisation_name ?: 'Not provided' }}</strong></div>
                            <div class="small text-muted mb-2">Your Quote: <strong class="text-success">£{{ number_format($quote->total_price, 2) }}</strong></div>
                            @if ($quote->estimated_completion_date)
                            <div class="small text-muted mb-2">Est. Completion: <strong>{{ $quote->estimated_completion_date->format('d M Y') }}</strong></div>
                            @endif
                            @if ($quote->warranty_months)
                            <div class="small text-muted mb-2">Warranty: <strong>{{ $quote->warranty_months }} month{{ $quote->warranty_months !== 1 ? 's' : '' }}</strong></div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <div class="fw-semibold small mb-1">Customer</div>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #7c3aed); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0;">
                                    {{ strtoupper(substr($job->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-size: 14px; font-weight: 600;">{{ $job->user->name }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">{{ $job->user->email }}</div>
                                </div>
                            </div>
                        </div>

                        @if ($quote->status === 'accepted')
                            <div class="alert alert-success rounded-4 small mb-3 py-2">
                                <i class="mdi mdi-check-circle me-1"></i>Customer accepted your quote.
                                @if ($quote->estimated_completion_date)
                                    Complete by {{ $quote->estimated_completion_date->format('d M Y') }}.
                                @endif
                            </div>
                        @elseif ($quote->status === 'completed')
                            @if ($quote->customer_rating)
                                <div class="alert alert-light rounded-4 small mb-3 py-2">
                                    <div class="fw-semibold mb-1">Rating: {{ $quote->customer_rating }}/5</div>
                                    @if ($quote->customer_review)
                                        <div>"{{ $quote->customer_review }}"</div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-secondary rounded-4 small mb-3 py-2">
                                    <i class="mdi mdi-check-all me-1"></i>Job marked as completed.
                                </div>
                            @endif
                        @endif

                        <div class="mt-auto d-flex gap-2">
                            <button class="btn btn-outline-dark rounded-4 flex-fill" type="button" data-bs-toggle="modal" data-bs-target="#wonJobModal{{ $quote->id }}">Full Details</button>
                        </div>
                    </div>
                </div>
            </div>

<div class="modal fade" id="wonJobModal{{ $quote->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1200px;">
                    <div class="modal-content border-0" style="border-radius: 20px;">

                        <div style="position: sticky; top: 0; z-index: 10; background: #fff; border-radius: 20px 20px 0 0;">
                            <div class="px-4 pt-4 pb-2">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa fa-briefcase text-muted" style="font-size: 14px;"></i>
                                        <span style="font-size: 13px; font-weight: 600; color: #6b7280;">Job #{{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-{{ $quote->status === 'completed' ? 'dark' : 'success' }}" style="border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600;">
                                            {{ ucfirst($quote->status) }}
                                        </span>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="opacity: .6;"></button>
                                    </div>
                                </div>
                                <h2 class="fw-bold mb-1" style="font-size: 28px; letter-spacing: -.02em; color: #111827;">{{ $job->title }}</h2>
                                <div class="d-flex flex-wrap align-items-center gap-2" style="font-size: 15px; color: #6b7280;">
                                    <span class="text-capitalize">{{ $job->categoryId?->name ?? 'General' }}</span>
                                    @if($job->organisation_name)
                                    <span class="mx-1" style="color: #d1d5db;">•</span>
                                    <span style="background: #eff6ff; color: #2563eb; border-radius: 20px; padding: 3px 12px; font-size: 12px; font-weight: 600; display: inline-block;">
                                        <i class="fa fa-building me-1" style="font-size: 11px;"></i>{{ $job->organisation_name }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <hr class="my-0" style="border-color: #e5e7eb; opacity: 1;">
                        </div>

                        <div class="modal-body px-4 py-4">

                            <div class="row g-3 mb-4">
                                <div class="col-sm-6 col-lg-3">
                                    <div class="d-flex align-items-center gap-3 p-3" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 1px 3px rgba(15,23,42,.04);">
                                        <div style="width: 40px; height: 40px; border-radius: 12px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fa fa-gbp" style="font-size: 18px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 700; color: #111827;">£{{ number_format($quote->total_price, 2) }}</div>
                                            <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Quote Amount</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <div class="d-flex align-items-center gap-3 p-3" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 1px 3px rgba(15,23,42,.04);">
                                        <div style="width: 40px; height: 40px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fa fa-calendar-check-o" style="font-size: 18px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 700; color: #111827;">{{ $quote->estimated_completion_date?->format('d M Y') ?? 'N/A' }}</div>
                                            <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Est. Completion</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <div class="d-flex align-items-center gap-3 p-3" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 1px 3px rgba(15,23,42,.04);">
                                        <div style="width: 40px; height: 40px; border-radius: 12px; background: #f3e8ff; color: #7c3aed; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fa fa-shield" style="font-size: 18px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 700; color: #111827;">{{ $quote->warranty_months ? $quote->warranty_months . ' month' . ($quote->warranty_months !== 1 ? 's' : '') : 'N/A' }}</div>
                                            <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Warranty</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <div class="d-flex align-items-center gap-3 p-3" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 1px 3px rgba(15,23,42,.04);">
                                        <div style="width: 40px; height: 40px; border-radius: 12px; background: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fa fa-check-circle" style="font-size: 18px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 700; color: #111827;">
                                                <span class="badge bg-{{ $quote->status === 'completed' ? 'dark' : 'success' }} rounded-pill">{{ ucfirst($quote->status) }}</span>
                                            </div>
                                            <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Status</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-lg-7">
                                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(15,23,42,.04);">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <i class="fa fa-calculator" style="color: #2563eb; font-size: 16px;"></i>
                                            <span style="font-size: 18px; font-weight: 700; color: #111827;">Quote Breakdown</span>
                                        </div>
                                        <table class="table" style="margin-bottom: 0;">
                                            <tbody>
                                                <tr>
                                                    <td style="padding: 10px 0; border-color: #f3f4f6; color: #6b7280;">Price for job</td>
                                                    <td style="padding: 10px 0; border-color: #f3f4f6; font-weight: 600; text-align: right;">£{{ number_format($quote->price_for_job, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 10px 0; border-color: #f3f4f6; color: #6b7280;">Discount</td>
                                                    <td style="padding: 10px 0; border-color: #f3f4f6; font-weight: 600; text-align: right; color: #dc2626;">-£{{ number_format($quote->discount_offered, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 10px 0; border-color: #f3f4f6; color: #6b7280;">Delivery cost</td>
                                                    <td style="padding: 10px 0; border-color: #f3f4f6; font-weight: 600; text-align: right;">£{{ number_format($quote->delivery_cost, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 14px 0 0; border-color: #e5e7eb; font-weight: 700; font-size: 16px;">Total</td>
                                                    <td style="padding: 14px 0 0; border-color: #e5e7eb; font-weight: 800; text-align: right; font-size: 22px; color: #2563eb;">£{{ number_format($quote->total_price, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    @if ($quote->notes)
                                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 24px; margin-top: 16px; box-shadow: 0 1px 3px rgba(15,23,42,.04);">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="fa fa-pencil-square-o" style="color: #7c3aed; font-size: 16px;"></i>
                                            <span style="font-size: 18px; font-weight: 700; color: #111827;">Notes</span>
                                        </div>
                                        <p style="font-size: 15px; line-height: 1.7; color: #374151; margin: 0;">{{ $quote->notes }}</p>
                                    </div>
                                    @endif

                                    @if ($quote->terms)
                                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 24px; margin-top: 16px; box-shadow: 0 1px 3px rgba(15,23,42,.04);">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="fa fa-shield" style="color: #dc2626; font-size: 16px;"></i>
                                            <span style="font-size: 18px; font-weight: 700; color: #111827;">Terms &amp; Conditions</span>
                                        </div>
                                        <p style="font-size: 15px; line-height: 1.7; color: #374151; margin: 0;">{{ $quote->terms }}</p>
                                    </div>
                                    @endif

                                    @if ($quote->status === 'completed' && $quote->customer_rating)
                                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 24px; margin-top: 16px; box-shadow: 0 1px 3px rgba(15,23,42,.04);">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="fa fa-star" style="color: #f59e0b; font-size: 16px;"></i>
                                            <span style="font-size: 18px; font-weight: 700; color: #111827;">Customer Rating</span>
                                        </div>
                                        <div style="font-size: 24px; color: #f59e0b; letter-spacing: 2px;">
                                            {{ str_repeat('★', $quote->customer_rating) }}{{ str_repeat('☆', 5 - $quote->customer_rating) }}
                                        </div>
                                        <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">{{ $quote->customer_rating }} / 5</div>
                                        @if ($quote->customer_review)
                                        <div style="font-size: 15px; line-height: 1.6; color: #374151; margin-top: 12px; padding: 12px 16px; background: #f8fafc; border-radius: 10px; font-style: italic;">
                                            "{{ $quote->customer_review }}"
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>

                                <div class="col-lg-5">
                                    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(15,23,42,.04); position: sticky; top: 24px;">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <i class="fa fa-user" style="color: #7c3aed; font-size: 16px;"></i>
                                            <span style="font-size: 18px; font-weight: 700; color: #111827;">Customer Details</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #2563eb, #7c3aed); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; flex-shrink: 0;">
                                                {{ strtoupper(substr($job->user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div style="font-size: 16px; font-weight: 700; color: #111827;">{{ $job->user->name }}</div>
                                                @if($job->user->customerProfile && $job->user->customerProfile->school_name)
                                                <div style="font-size: 13px; color: #6b7280;">
                                                    <span style="background: #eff6ff; color: #2563eb; border-radius: 20px; padding: 2px 10px; font-size: 11px; font-weight: 600;">
                                                        <i class="fa fa-graduation-cap me-1" style="font-size: 10px;"></i>{{ $job->user->customerProfile->school_name }}
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div style="font-size: 14px; color: #6b7280;">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="fa fa-envelope" style="color: #94a3b8; width: 16px;"></i>
                                                <a href="mailto:{{ $job->user->email }}" style="color: #2563eb; text-decoration: none;">{{ $job->user->email }}</a>
                                            </div>
                                            @if($job->user->phone)
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="fa fa-phone" style="color: #94a3b8; width: 16px;"></i>
                                                <a href="tel:{{ $job->user->phone }}" style="color: #2563eb; text-decoration: none;">{{ $job->user->phone }}</a>
                                            </div>
                                            @endif
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="fa fa-map-marker" style="color: #94a3b8; width: 16px;"></i>
                                                <span class="text-capitalize">{{ $job->location ?: 'Not provided' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fa fa-gbp" style="color: #94a3b8; width: 16px;"></i>
                                                <span>Budget: {{ $job->budget ? '£'.number_format((float) $job->budget, 2) : 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($quote->status === 'accepted')
                                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px; padding: 20px; margin-top: 16px;">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="fa fa-check-circle" style="color: #16a34a; font-size: 18px;"></i>
                                            <span style="font-size: 16px; font-weight: 700; color: #166534;">Job Accepted</span>
                                        </div>
                                        <p style="font-size: 14px; color: #15803d; margin: 0;">
                                            The customer has accepted your quote.
                                            @if ($quote->estimated_completion_date)
                                                Complete by <strong>{{ $quote->estimated_completion_date->format('d M Y') }}</strong>.
                                            @endif
                                        </p>
                                    </div>
                                    @elseif ($quote->status === 'completed' && !$quote->customer_rating)
                                    <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px; margin-top: 16px;">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="fa fa-check-circle" style="color: #6b7280; font-size: 18px;"></i>
                                            <span style="font-size: 16px; font-weight: 700; color: #374151;">Job Completed</span>
                                        </div>
                                        <p style="font-size: 14px; color: #6b7280; margin: 0;">
                                            This job has been marked as completed. Waiting for customer rating.
                                        </p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div style="position: sticky; bottom: 0; z-index: 10; background: #fff; border-radius: 0 0 20px 20px;">
                            <hr class="my-0" style="border-color: #e5e7eb; opacity: 1;">
                            <div class="px-4 py-3 d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-secondary rounded-4 px-4" data-bs-dismiss="modal" style="height: 48px; border-radius: 12px; font-size: 15px; font-weight: 600;">Close</button>
                                <a href="mailto:{{ $job->user->email }}" class="btn btn-primary rounded-4 d-flex align-items-center gap-2 px-4" style="height: 48px; border-radius: 12px; font-size: 15px; font-weight: 600;">
                                    <i class="fa fa-envelope" style="font-size: 14px;"></i> Contact Customer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border rounded-4 text-center py-5">
                    <i class="mdi mdi-trophy-outline" style="font-size: 48px; color: #d1d5db;"></i>
                    <h5 class="mt-3 text-muted">No won jobs yet</h5>
                    <p class="text-muted mb-0">When a customer accepts your quote, it will appear here.</p>
                    <a href="{{ route('supplier-panel.jobs') }}" class="btn btn-primary rounded-4 mt-3">Browse Jobs</a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $wonQuotes->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
