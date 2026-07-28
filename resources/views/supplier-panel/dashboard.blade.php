@extends('supplier-panel.layouts.app')
@section('title', 'Supplier Dashboard')

<?php
    $endingSoonThresholdSeconds = 86400;

    $statusMeta = function ($job) use ($endingSoonThresholdSeconds) {
        if ($job->status !== 'open' || ($job->needed_by && $job->needed_by->isPast())) {
            return ['Ended', 'danger'];
        }

        if ($job->needed_by) {
            $secondsLeft = now()->diffInSeconds($job->needed_by, false);

            if ($secondsLeft > 0 && $secondsLeft <= $endingSoonThresholdSeconds) {
                return ['Ending Soon', 'warning'];
            }
        }

        return ['Active', 'success'];
    };
?>

@section('content')
    <div class="content-wrapper p-3">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 24px; background: linear-gradient(135deg, #0f172a, #0f766e);">
            <div class="card-body text-white p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-primary px-3 py-2 mb-3 rounded">Supplier Hub</span>
                        @hasFeature('priority_support')
                        <span class="badge bg-warning text-dark px-3 py-2 mb-3 ms-2"><i class="mdi mdi-star"></i> Priority Support</span>
                        @endhasFeature
                        @hasFeature('recommended_badge')
                            @if($user->isRecommended())
                            <span class="badge bg-warning text-dark px-3 py-2 mb-3 ms-2"><i class="mdi mdi-check-circle"></i> Recommended Supplier</span>
                            @endif
                        @endhasFeature
                        <h2 class="text-white mb-2">View jobs, track activity, and manage your supplier presence.</h2>
                        <p class="mb-0 text-white-50">Built from your supplier requirements document: profile editing, job board visibility, past-quote space, reporting, and account activity.</p>
                    </div>
                    <div class="col-lg-4">
                        <div class="bg-white bg-opacity-10 rounded-4 p-3">
                            <div class="small text-white-50 mb-2">Quote activity</div>
                            <div class="h4 mb-1">{{ $stats['submitted_quotes'] }} quotes sent</div>
                            <div class="text-white-50 small">Your supplier account can now submit and update quotes directly from the job board.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Available Jobs</div><div class="display-6 fw-bold">{{ $stats['available_jobs'] }}</div></div></div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Active Jobs</div><div class="display-6 fw-bold text-success">{{ $stats['active_jobs'] }}</div></div></div>
            </div>
            {{-- <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Ending Soon</div><div class="display-6 fw-bold text-warning">{{ $stats['ending_soon'] }}</div></div></div>
            </div> --}}
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Ended</div><div class="display-6 fw-bold text-danger">{{ $stats['ended_jobs'] }}</div></div></div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><div class="text-muted small">Your Quotes</div><div class="display-6 fw-bold text-primary">{{ $stats['submitted_quotes'] }}</div></div></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="mb-1">Latest job board items</h4>
                                <p class="text-muted mb-0">Each job gets a running reference based on its job number.</p>
                            </div>
                            <a href="{{ route('supplier-panel.jobs') }}" class="btn btn-outline-primary rounded-4">View full board</a>
                        </div>
                        <div class="row g-3">
                            @forelse ($jobs as $job)
                                @php($meta = $statusMeta($job))
                                <div class="col-12">
                                    <div class="border rounded-4 p-3">
                                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="badge rounded bg-{{ $meta[1] }}">{{ $meta[0] }}</span>
                                                    <span class="badge rounded bg-light text-dark">Job No. {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</span>
                                                </div>
                                                <h5 class="mb-1">{{ $job->title }}</h5>
                                                <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit($job->description, 120) }}</p>
                                                <div class="small text-muted text-capitalize">
                                                    <span class="badge text-bg-primary rounded">{{ $job->categoryId?->name ?: 'General' }}</span>
                                                    • <span class="badge text-bg-primary rounded">{{ $job->location ?: 'Location not specified' }}</span>
                                                    </div>
                                            </div>
                                            <div class="text-lg-end">
                                                <div class="fw-semibold">{{ $job->budget ? '£ '.number_format((float) $job->budget, 2) : 'Budget on request' }}</div>
                                                <div class="small text-muted mb-3">Needed by {{ $job->needed_by?->format('d M Y H:i') ?? 'N/A' }}</div>
                                                <a href="{{ route('supplier-panel.jobs') }}" class="btn btn-primary rounded-4">Open quote form</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-light border rounded-4 mb-0">No jobs have been posted yet.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <?php
                    $profile = $user->supplierProfile;
                    $fields = [
                        ['label' => 'Company name', 'key' => 'company_name', 'icon' => 'fa-building'],
                        ['label' => 'Company logo', 'key' => 'company_logo', 'icon' => 'fa-image'],
                        ['label' => 'Address', 'key' => 'address', 'icon' => 'fa-map-marker'],
                        ['label' => 'Description', 'key' => 'company_description', 'icon' => 'fa-file-text'],
                        ['label' => 'Website', 'key' => 'website', 'icon' => 'fa-globe'],
                        ['label' => 'Review link', 'key' => 'review_link', 'icon' => 'fa-star'],
                        ['label' => 'Social link', 'key' => 'social_link', 'icon' => 'fa-link'],
                    ];
                    $completedFields = collect($fields)->filter(fn($f) => filled($profile?->{$f['key']}))->count();
                    $totalFields = count($fields);
                    $completionPct = $totalFields > 0 ? round(($completedFields / $totalFields) * 100) : 0;
                    $isComplete = $completionPct === 100;
                ?>
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="mb-0">Profile completion</h4>
                            <span class="badge bg-{{ $isComplete ? 'success' : ($completionPct >= 50 ? 'warning' : 'danger') }} rounded-pill px-3 py-2">{{ $completionPct }}%</span>
                        </div>
                        <div class="progress mb-3" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar bg-{{ $isComplete ? 'success' : ($completionPct >= 50 ? 'warning' : 'danger') }}"
                                 role="progressbar" style="width: {{ $completionPct }}%; border-radius: 4px;"
                                 aria-valuenow="{{ $completionPct }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <ul class="list-unstyled mb-3">
                            @foreach($fields as $field)
                                <?php $filled = filled($profile?->{$field['key']}); ?>
                                <li class="py-2 border-bottom d-flex align-items-center gap-2">
                                    <i class="fa {{ $field['icon'] }} text-muted" style="width: 16px;"></i>
                                    <span class="small">{{ $field['label'] }}</span>
                                    <span class="ms-auto small fw-semibold {{ $filled ? 'text-success' : 'text-danger' }}">
                                        @if($filled)
                                            <i class="fa fa-check-circle"></i> Done
                                        @else
                                            <i class="fa fa-times-circle"></i> Add
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        @if($supplierAverageRating)
                            <div class="d-flex align-items-center gap-2 mb-3 p-2 bg-light rounded-3">
                                <i class="fa fa-star text-warning"></i>
                                <span class="small fw-semibold">Supplier rating:</span>
                                <span class="text-warning">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa {{ $i <= round($supplierAverageRating) ? 'fa-star' : 'fa-star-o' }}"></i>
                                    @endfor
                                </span>
                                <span class="small text-muted">{{ $supplierAverageRating }}/5 ({{ $supplierRatingsCount }})</span>
                            </div>
                        @endif
                        <a href="{{ route('supplier-panel.profile') }}" class="btn btn-outline-primary rounded-4 w-100">
                            <i class="fa fa-edit me-1"></i> Edit profile
                        </a>
                    </div>
                </div>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="mb-0">Recent quotes</h4>
                            <span class="badge bg-primary rounded-pill px-3 py-2">{{ $stats['submitted_quotes'] }}</span>
                        </div>
                        @forelse ($recentQuotes as $quote)
                            <?php
                                $statusColors = ['submitted' => 'primary', 'accepted' => 'success', 'rejected' => 'danger', 'completed' => 'dark'];
                                $color = $statusColors[$quote->status] ?? 'secondary';
                            ?>
                            <div class="border rounded-4 p-3 mb-3" style="border-left: 4px solid var(--bs-{{ $color }}) !important;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small text-muted">{{ $quote->created_at?->format('d M Y') }}</span>
                                    <span class="badge bg-{{ $color }} text-uppercase">{{ $quote->status }}</span>
                                </div>
                                <div class="fw-semibold mb-1">{{ $quote->job?->title ?: 'Job removed' }}</div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fw-bold fs-5">£{{ number_format((float) $quote->total_price, 2) }}</span>
                                    @if($quote->status === 'submitted')
                                        <span class="small text-muted"><i class="fa fa-clock-o"></i> Awaiting response</span>
                                    @elseif($quote->status === 'accepted')
                                        <span class="small text-success"><i class="fa fa-check-circle"></i> Accepted</span>
                                    @elseif($quote->status === 'rejected')
                                        <span class="small text-danger"><i class="fa fa-times-circle"></i> Rejected</span>
                                    @elseif($quote->status === 'completed')
                                        <span class="small text-dark"><i class="fa fa-check-circle"></i> Completed</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fa fa-file-text-o text-muted" style="font-size: 32px;"></i>
                                <p class="text-muted mb-0 mt-2">You have not submitted any quotes yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
