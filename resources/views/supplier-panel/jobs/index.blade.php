@extends('supplier-panel.layouts.app')
@section('title', 'Job Board')

@php
    $endingSoonThresholdSeconds = 86400;
    $usage = auth()->user()->subscriptionUsage();

    $jobMeta = function ($job) use ($endingSoonThresholdSeconds) {
        $neededBy = $job->needed_by;

        if (!$neededBy || $job->status !== 'open') {
            return ['Ended Jobs', 'danger', 'Ended jobs'];
        }

        if ($neededBy->isPast()) {
            return ['Ended Jobs', 'danger', 'Ended jobs'];
        }

        if ($neededBy->diffInSeconds(now(), false) <= $endingSoonThresholdSeconds) {
            return ['Ending Soon', 'warning', 'Job ending time left'];
        }

        return ['Active Jobs', 'success', 'Active jobs'];
    };
@endphp

@section('content')
<div class="content-wrapper p-3">

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search jobs</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-4" placeholder="Search title, category, organisation, location">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select rounded-4 text-dark">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->name }}" @selected($category->name === request('category'))>
                                {{ ucfirst($category->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                 
                <div class="col-md-3">
                    <label class="form-label">Sort</label>
                    <select name="sort" class="form-select rounded-4 text-dark">
                        <option value="newest" @selected($sort === 'newest')>Newest to oldest</option>
                        <option value="oldest" @selected($sort === 'oldest')>Oldest to newest</option>
                        <option value="ending_soon" @selected($sort === 'ending_soon')>Ending soon</option>
                        <option value="budget_high" @selected($sort === 'budget_high')>Highest budget</option>
                        <option value="budget_low" @selected($sort === 'budget_low')>Lowest budget</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary rounded-4 w-100">Go</button>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-secondary rounded-4 w-100" onclick="removeParams()"><i class="fa fa-undo"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse ($jobs as $job)
            @php($meta = $jobMeta($job))
            @php($existingQuote = $job->quotes->first())
            <div class="col-lg-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top: 5px solid var(--bs-{{ $meta[1] }}) !important;">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <span id="status-{{ $job->id }}" class="rounded badge bg-{{ $meta[1] }}">
                                {{ $meta[0] }}
                            </span>
                            <span class="small text-muted fw-semibold">Job No. {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h4 class="mb-2">{{ $job->title }}</h4>
                         <p class="text-muted mb-3">Posted {{ $job->created_at->diffForHumans() ?? '' }}</p>
                       
                        <div class="alert alert -light rounded p-2 mb-3" style="background-color: #f7f9fc !important;">
                            <div class="small text-muted mb-2">Category: <strong class="text-capitalize">{{ $job->categoryId?->name ?? 'General' }}</strong></div>
                            <div class="small text-muted mb-2">Organisation: <strong class="text-capitalize">{{ $job->organisation_name ?: 'Not provided' }}</strong></div>
                            <div class="small text-muted mb-2">Budget: <strong class="text-capitalize">{{ $job->budget ? '£ '.number_format((float) $job->budget, 2) : 'N/A' }}</strong></div>
                            <div class="small text-muted mb-2">Location: <strong class="text-capitalize">{{ $job->location ?: 'Not provided' }}</strong></div>
                            <div class="small text-muted mb-3">Needed by: <strong class="text-capitalize">{{ $job->needed_by?->format('d M Y h:i A') ?? 'N/A' }}</strong></div>
                        </div>
                         <p class="text-muted mb-3">{{ \Illuminate\Support\Str::limit($job->description, 140) }}</p>
                        {{-- <div class="fw-semibold mb-3">{{ $job->budget ? '£ '.number_format((float) $job->budget, 2) : 'Budget not shared' }}</div> --}}
                        <div class="alert alert-light border rounded-4 small mb-3">
                            <div class="d-flex justify-content-between flex-wrap align-items-center">
                                <span id="status-text-{{ $job->id }}" class="js-job-status-text">{{ $meta[2] }}</span>
                                @if ($job->needed_by && $job->status === 'open' && $job->needed_by->isFuture())
                                    <div 
                                        class="fw-semibold mt-1 js-job-countdown {{ $meta[0] === 'Ending Soon' ? 'text-danger' : 'text-muted d-none' }}"
                                        data-end-at="{{ $job->needed_by->toIso8601String() }}"
                                        data-ending-soon-threshold="{{ $endingSoonThresholdSeconds }}"
                                        data-status-badge-id="status-{{ $job->id }}"
                                        data-status-text-id="status-text-{{ $job->id }}"
                                    >
                                        Time left: --
                                    </div>
                                @endif

                            </div>
                        </div>
                        @if ($existingQuote)
                            <div class="small text-success mb-3">Your quote is already submitted for this job.</div>
                        @endif
                        <div class="mt-auto d-flex gap-2">
                            <button class="btn btn-outline-dark rounded-4 flex-fill" type="button" data-bs-toggle="modal" data-bs-target="#jobModal{{ $job->id }}">View details</button>
                            @if($usage['can_submit_quote'])
                                <button class="btn btn-primary rounded-4 flex-fill" type="button" data-bs-toggle="modal" data-bs-target="#quoteModal{{ $job->id }}">{{ $existingQuote ? 'Update Quote' : 'Send Quote' }}</button>
                            @else
                                <button class="btn btn-secondary rounded-4 flex-fill" type="button" disabled
                                    @if($usage['quotes_remaining_this_month'] <= 0 && $usage['quotes_remaining_this_year'] <= 0)
                                        title="Quote limit reached. Upgrade to submit more quotes."
                                    @else
                                        title="Upgrade to submit quotes."
                                    @endif
                                >
                                    <i class="mdi mdi-lock me-1"></i>Upgrade to Quote
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .modal-job-details {
                    --modal-radius: 20px;
                    --card-shadow: 0 4px 20px rgba(15,23,42,.06);
                }
                .modal-job-details .modal-content {
                    border-radius: var(--modal-radius) !important;
                    box-shadow: 0 25px 60px rgba(15,23,42,.12);
                }
                .modal-job-details .stat-card {
                    background: #fff;
                    border: 1px solid #e5e7eb;
                    border-radius: 16px;
                    padding: 18px 16px;
                    transition: transform .2s ease, box-shadow .2s ease;
                    box-shadow: 0 1px 3px rgba(15,23,42,.04);
                }
                .modal-job-details .stat-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(15,23,42,.08);
                }
                .modal-job-details .icon-circle {
                    width: 40px;
                    height: 40px;
                    border-radius: 12px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                }
                .modal-job-details .profile-avatar {
                    width: 56px;
                    height: 56px;
                    border-radius: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 700;
                    font-size: 22px;
                    color: #fff;
                    flex-shrink: 0;
                    background: linear-gradient(135deg, #2563eb, #7c3aed);
                }
                .modal-job-details .description-card {
                    background: #fff;
                    border: 1px solid #e5e7eb;
                    border-radius: 16px;
                    padding: 24px;
                }
                .modal-job-details .profile-card {
                    background: #fff;
                    border: 1px solid #e5e7eb;
                    border-radius: 16px;
                    padding: 24px;
                }
                .modal-job-details .org-badge {
                    background: #eff6ff;
                    color: #2563eb;
                    border-radius: 20px;
                    padding: 3px 12px;
                    font-size: 12px;
                    font-weight: 600;
                    display: inline-block;
                }
                .modal-job-details .btn-premium {
                    height: 48px;
                    border-radius: 12px;
                    font-size: 15px;
                    font-weight: 600;
                    padding: 0 24px;
                    transition: all .2s ease;
                }
                .modal-job-details .btn-premium-primary {
                    background: #2563eb;
                    border: none;
                    color: #fff;
                }
                .modal-job-details .btn-premium-primary:hover {
                    background: #1d4ed8;
                    transform: translateY(-1px);
                    box-shadow: 0 8px 25px rgba(37,99,235,.25);
                }
                .modal-job-details .btn-premium-outline {
                    background: transparent;
                    border: 1px solid #e5e7eb;
                    color: #374151;
                }
                .modal-job-details .btn-premium-outline:hover {
                    background: #f8fafc;
                    border-color: #d1d5db;
                }
                .modal-job-details .sticky-header {
                    position: sticky;
                    top: 0;
                    z-index: 10;
                    background: #fff;
                    border-radius: var(--modal-radius) var(--modal-radius) 0 0;
                }
                .modal-job-details .sticky-footer {
                    position: sticky;
                    bottom: 0;
                    z-index: 10;
                    background: #fff;
                    border-radius: 0 0 var(--modal-radius) var(--modal-radius);
                }
                @media (max-width: 991.98px) {
                    .modal-job-details .stat-card { padding: 14px 12px; }
                    .modal-job-details .profile-avatar { width: 48px; height: 48px; font-size: 18px; }
                }
                @media (max-width: 575.98px) {
                    .modal-dialog.modal-job-details { margin: 0; }
                    .modal-job-details .modal-content { border-radius: 0 !important; min-height: 100vh; }
                    .modal-job-details .sticky-footer .d-flex { flex-direction: column; }
                    .modal-job-details .sticky-footer .btn-premium { width: 100%; }
                }
            </style>
            <div class="modal fade modal-job-details" id="jobModal{{ $job->id }}" tabindex="-1" aria-hidden="true" aria-labelledby="jobModalLabel{{ $job->id }}">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-job-details" style="max-width: 1200px;">
                    <div class="modal-content border-0">
                        <div class="sticky-header px-4 pt-4 pb-0">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa fa-briefcase text-muted" style="font-size: 14px;"></i>
                                    <span style="font-size: 13px; font-weight: 600; color: #6b7280;">Job #{{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span id="status-{{ $job->id }}" class="badge" style="background: #22c55e; color: #fff; border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600;">{{ $meta[0] }}</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="opacity: .6;"></button>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-2" style="font-size: 28px; letter-spacing: -.02em; color: #111827;">{{ $job->title }}</h2>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3" style="font-size: 15px; color: #6b7280;">
                                <span class="text-capitalize">{{ $job->categoryId?->name ?? 'General' }}</span>
                                @if($job->organisation_name)
                                <span class="mx-1" style="color: #d1d5db;">•</span>
                                <span class="org-badge text-capitalize"><i class="fa fa-building me-1" style="font-size: 11px;"></i>{{ $job->organisation_name }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="modal-body px-4 py-3">
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6 col-lg-4">
                                    <div class="stat-card d-flex align-items-center gap-3">
                                        <div class="icon-circle" style="background: #eff6ff; color: #2563eb;">
                                            <i class="fa fa-gbp" style="font-size: 18px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 700; color: #111827;">{{ $job->budget ? '£'.number_format((float) $job->budget, 2) : 'N/A' }}</div>
                                            <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Budget</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="stat-card d-flex align-items-center gap-3">
                                        <div class="icon-circle" style="background: #fef3c7; color: #d97706;">
                                            <i class="fa fa-calendar-check-o" style="font-size: 18px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 700; color: #111827;">{{ $job->needed_by?->format('d M Y') ?? 'N/A' }}</div>
                                            <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Needed By</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="stat-card d-flex align-items-center gap-3">
                                        <div class="icon-circle" style="background: #fee2e2; color: #dc2626;">
                                            <i class="fa fa-map-marker" style="font-size: 18px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 700; color: #111827;" class="text-capitalize">{{ $job->location ?: 'N/A' }}</div>
                                            <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Location</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="stat-card d-flex align-items-center gap-3">
                                        <div class="icon-circle" style="background: #f3e8ff; color: #7c3aed;">
                                            <i class="fa fa-building" style="font-size: 18px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 700; color: #111827;" class="text-capitalize">{{ $job->organisation_name ?: 'N/A' }}</div>
                                            <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Organisation</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="stat-card d-flex align-items-center gap-3">
                                        <div class="icon-circle" style="background: #ccfbf1; color: #0d9488;">
                                            <i class="fa fa-calendar" style="font-size: 18px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 700; color: #111827;">{{ $job->created_at?->format('d M Y') ?? 'N/A' }}</div>
                                            <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Posted</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="stat-card d-flex align-items-center gap-3">
                                        <div class="icon-circle" style="background: #dbeafe; color: #2563eb;">
                                            <i class="fa fa-commenting" style="font-size: 18px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 700; color: #111827;">{{ $job->quotes->count() }}</div>
                                            <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Quote{{ $job->quotes->count() !== 1 ? 's' : '' }} Received</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="description-card mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <i class="fa fa-file-text" style="color: #2563eb; font-size: 16px;"></i>
                                    <span style="font-size: 18px; font-weight: 700; color: #111827;">Description</span>
                                </div>
                                <div class="job-description-wrap">
                                    <div class="job-description-text js-desc-text" style="font-size: 16px; line-height: 1.7; color: #374151; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $job->description ?? 'No description provided.' }}
                                    </div>
                                    @if(strlen($job->description ?? '') > 300)
                                    <button type="button" class="btn btn-link p-0 mt-2 js-desc-toggle" style="font-size: 14px; font-weight: 600; color: #2563eb; text-decoration: none;" onclick="var t = this.previousElementSibling; var c = t.style.webkitLineClamp; t.style.webkitLineClamp = c === 'none' ? '4' : 'none'; this.textContent = c === 'none' ? 'Read more →' : 'Show less ↑';">
                                        Read more →
                                    </button>
                                    @endif
                                </div>
                            </div>

                            @if($job->dynamicFieldValues->isNotEmpty())
                            <div class="description-card mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <i class="fa fa-list-alt" style="color: #7c3aed; font-size: 16px;"></i>
                                    <span style="font-size: 18px; font-weight: 700; color: #111827;">Additional Details</span>
                                </div>
                                @foreach ($job->dynamicFieldValues as $itemFields)
                                <div style="background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 12px;">
                                    <span style="background: #e5e7eb; color: #374151; border-radius: 20px; padding: 2px 10px; font-size: 11px; font-weight: 600; display: inline-block; margin-bottom: 12px;">Item #{{ $loop->index + 1 }}</span>
                                    <div class="row g-3">
                                        @foreach ($itemFields as $fieldsValue)
                                            @if(($fieldsValue['category_fields']['field_type'] ?? null) === 'file')
                                                <div class="col-md-4">
                                                    <div style="font-size: 12px; color: #6b7280; font-weight: 600; margin-bottom: 4px;">{{ $fieldsValue['category_fields']['field_label'] }}</div>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @forelse ((array) ($fieldsValue['parsed_value'] ?? $fieldsValue['field_value'] ?? []) as $filePath)
                                                            <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 4px; max-width: 80px;">
                                                                @if(in_array(strtolower(pathinfo($filePath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']))
                                                                    <img src="{{ asset($filePath) }}" alt="{{ $fieldsValue['category_fields']['field_label'] }}" style="max-height: 60px; max-width: 60px; object-fit: cover; border-radius: 4px;">
                                                                @else
                                                                    <a href="{{ asset($filePath) }}" target="_blank" rel="noopener" style="font-size: 11px; color: #2563eb;">{{ \Illuminate\Support\Str::afterLast($filePath, '/') }}</a>
                                                                @endif
                                                            </div>
                                                        @empty
                                                            <span style="font-size: 13px; color: #94a3b8;">No file uploaded</span>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-4">
                                                    <div style="font-size: 12px; color: #6b7280; font-weight: 600; margin-bottom: 2px;">{{ $fieldsValue['category_fields']['field_label'] }}</div>
                                                    <div style="font-weight: 600; color: #111827;">
                                                        {{ is_array($fieldsValue['parsed_value'] ?? null)
                                                            ? implode(', ', array_map('strval', $fieldsValue['parsed_value']))
                                                            : (string) ($fieldsValue['parsed_value'] ?? $fieldsValue['field_value'] ?? '') }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if($job->user)
                            <div class="profile-card">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <i class="fa fa-user" style="color: #7c3aed; font-size: 16px;"></i>
                                    <span style="font-size: 18px; font-weight: 700; color: #111827;">Posted By</span>
                                </div>
                                <div class="d-flex flex-column flex-sm-row align-items-start gap-3">
                                    <div class="profile-avatar">{{ strtoupper(substr($job->user->name, 0, 2)) }}</div>
                                    <div class="flex-grow-1" style="width: 100%;">
                                        <div style="font-size: 18px; font-weight: 700; color: #111827;">{{ $job->user->name }}</div>
                                        @if($job->user->customerProfile && $job->user->customerProfile->school_name)
                                        <div style="font-size: 14px; color: #6b7280; margin-bottom: 2px;">School Administrator</div>
                                        <div class="org-badge mb-3"><i class="fa fa-graduation-cap me-1" style="font-size: 11px;"></i>{{ $job->user->customerProfile->school_name }}</div>
                                        @endif
                                        <div class="row g-2 mt-2" style="font-size: 14px; color: #6b7280;">
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center gap-2"><i class="fa fa-envelope" style="color: #94a3b8; width: 16px;"></i>{{ $job->user->email }}</div>
                                            </div>
                                            @if($job->user->phone)
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center gap-2"><i class="fa fa-phone" style="color: #94a3b8; width: 16px;"></i>{{ $job->user->phone }}</div>
                                            </div>
                                            @endif
                                            @if($job->user->customerProfile)
                                                @if($job->user->customerProfile->county)
                                                <div class="col-sm-6">
                                                    <div class="d-flex align-items-center gap-2"><i class="fa fa-globe" style="color: #94a3b8; width: 16px;"></i><span class="text-capitalize">{{ $job->user->customerProfile->county }}</span></div>
                                                </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="sticky-footer px-4 pb-4 pt-3">
                            <hr class="my-0 mb-3" style="border-color: #e5e7eb; opacity: 1;">
                            <div class="d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-premium btn-premium-outline" data-bs-dismiss="modal">Cancel</button>
                                @if($usage['can_submit_quote'])
                                <button type="button" class="btn btn-premium btn-premium-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#quoteModal{{ $job->id }}">
                                    {{ $existingQuote ? 'Update Quote' : 'Send Quote' }}
                                    <i class="fa fa-arrow-right" style="font-size: 14px;"></i>
                                </button>
                                @else
                                <button type="button" class="btn btn-premium btn-premium-primary d-flex align-items-center gap-2" disabled
                                    @if($usage['quotes_remaining_this_month'] <= 0 && $usage['quotes_remaining_this_year'] <= 0)
                                        title="Quote limit reached. Upgrade to submit more quotes."
                                    @else
                                        title="Upgrade to submit quotes."
                                    @endif
                                >
                                    <i class="mdi mdi-lock me-1"></i>Upgrade to Quote
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .modal-quote {
                    --modal-radius: 20px;
                }
                .modal-quote .modal-content {
                    border-radius: var(--modal-radius) !important;
                    box-shadow: 0 25px 60px rgba(15,23,42,.12);
                }
                .modal-quote .form-card {
                    background: #fff;
                    border: 1px solid #e5e7eb;
                    border-radius: 16px;
                    padding: 24px;
                    margin-bottom: 16px;
                    box-shadow: 0 1px 3px rgba(15,23,42,.04);
                }
                .modal-quote .form-card:hover {
                    box-shadow: 0 4px 15px rgba(15,23,42,.06);
                }
                .modal-quote .section-header {
                    font-size: 16px;
                    font-weight: 700;
                    color: #111827;
                    margin-bottom: 20px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                .modal-quote .input-56 {
                    height: 56px;
                    border-radius: 12px;
                    border: 1px solid #e5e7eb;
                    padding: 0 16px 0 44px;
                    font-size: 16px;
                    font-weight: 600;
                    color: #111827;
                    transition: all .2s ease;
                    background: #fff;
                    width: 100%;
                }
                .modal-quote .input-56:focus {
                    border-color: #2563eb;
                    box-shadow: 0 0 0 4px rgba(37,99,235,.12);
                    outline: none;
                }
                .modal-quote .input-56-prefix {
                    position: relative;
                }
                .modal-quote .input-56-prefix .prefix {
                    position: absolute;
                    left: 16px;
                    top: 50%;
                    transform: translateY(-50%);
                    font-size: 16px;
                    font-weight: 600;
                    color: #94a3b8;
                    z-index: 2;
                    pointer-events: none;
                }
                .modal-quote .input-56-icon {
                    position: relative;
                }
                .modal-quote .input-56-icon .icon {
                    position: absolute;
                    right: 16px;
                    top: 50%;
                    transform: translateY(-50%);
                    color: #94a3b8;
                    z-index: 2;
                    pointer-events: none;
                }
                .modal-quote .total-display {
                    background: #eff6ff;
                    border: 2px solid #2563eb;
                    border-radius: 12px;
                    padding: 16px 20px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }
                .modal-quote .total-display .total-value {
                    font-size: 24px;
                    font-weight: 800;
                    color: #2563eb;
                }
                .modal-quote .total-display .total-label {
                    font-size: 14px;
                    font-weight: 600;
                    color: #2563eb;
                }
                .modal-quote .form-textarea {
                    border-radius: 12px;
                    border: 1px solid #e5e7eb;
                    padding: 16px;
                    font-size: 15px;
                    color: #111827;
                    transition: all .2s ease;
                    width: 100%;
                    background: #fff;
                }
                .modal-quote .form-textarea:focus {
                    border-color: #2563eb;
                    box-shadow: 0 0 0 4px rgba(37,99,235,.12);
                    outline: none;
                }
                .modal-quote .char-counter {
                    font-size: 12px;
                    color: #94a3b8;
                    text-align: right;
                    margin-top: 6px;
                }
                .modal-quote .summary-card {
                    background: #fff;
                    border: 1px solid #e5e7eb;
                    border-radius: 16px;
                    padding: 24px;
                    box-shadow: 0 4px 20px rgba(15,23,42,.06);
                    position: sticky;
                    top: 24px;
                }
                .modal-quote .summary-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 8px 0;
                    font-size: 14px;
                    color: #6b7280;
                }
                .modal-quote .summary-row .value {
                    font-weight: 600;
                    color: #111827;
                }
                .modal-quote .summary-total {
                    background: #eff6ff;
                    border-radius: 12px;
                    padding: 14px 16px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-top: 8px;
                }
                .modal-quote .summary-total .label {
                    font-size: 14px;
                    font-weight: 700;
                    color: #2563eb;
                }
                .modal-quote .summary-total .value {
                    font-size: 22px;
                    font-weight: 800;
                    color: #2563eb;
                }
                .modal-quote .sticky-header {
                    position: sticky;
                    top: 0;
                    z-index: 10;
                    background: #fff;
                    border-radius: var(--modal-radius) var(--modal-radius) 0 0;
                }
                .modal-quote .sticky-footer {
                    position: sticky;
                    bottom: 0;
                    z-index: 10;
                    background: #fff;
                    border-radius: 0 0 var(--modal-radius) var(--modal-radius);
                }
                .modal-quote .btn-premium {
                    height: 48px;
                    border-radius: 12px;
                    font-size: 15px;
                    font-weight: 600;
                    padding: 0 28px;
                    transition: all .2s ease;
                }
                .modal-quote .btn-premium-primary {
                    background: #2563eb;
                    border: none;
                    color: #fff;
                }
                .modal-quote .btn-premium-primary:hover {
                    background: #1d4ed8;
                    transform: translateY(-1px);
                    box-shadow: 0 8px 25px rgba(37,99,235,.25);
                }
                .modal-quote .btn-premium-outline {
                    background: transparent;
                    border: 1px solid #e5e7eb;
                    color: #374151;
                }
                .modal-quote .btn-premium-outline:hover {
                    background: #f8fafc;
                    border-color: #d1d5db;
                }
                @media (max-width: 991.98px) {
                    .modal-quote .summary-card { position: static; margin-top: 16px; }
                }
                @media (max-width: 575.98px) {
                    .modal-dialog.modal-quote { margin: 0; }
                    .modal-quote .modal-content { border-radius: 0 !important; min-height: 100vh; }
                    .modal-quote .sticky-footer .d-flex { flex-direction: column; }
                    .modal-quote .sticky-footer .btn-premium { width: 100%; }
                    .modal-quote .form-card { padding: 16px; }
                }
            </style>
            <div class="modal fade job-modal modal-quote" id="quoteModal{{ $job->id }}" tabindex="-1" aria-hidden="true" aria-labelledby="quoteModalLabel{{ $job->id }}">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-quote" style="max-width: 1200px;">
                    <div class="modal-content border-0">
                        <div class="sticky-header px-4 pt-4 pb-0">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="fa fa-file-text-o" style="color: #6b7280; font-size: 14px;"></i>
                                        <span style="font-size: 13px; font-weight: 600; color: #6b7280;">Quote for Job #{{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                    <h2 class="fw-bold mb-0" style="font-size: 28px; letter-spacing: -.02em; color: #111827;">{{ $job->title }}</h2>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    @hasFeature('professional_quote')
                                    <span style="background: #eff6ff; color: #2563eb; border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa fa-shield" style="font-size: 11px;"></i>Professional Quote
                                    </span>
                                    @else
                                    <span style="background: #f3f4f6; color: #6b7280; border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa fa-file-text-o" style="font-size: 11px;"></i>Basic Quote
                                    </span>
                                    @endhasFeature
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="opacity: .6;"></button>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-3 py-2" style="font-size: 13px; color: #6b7280;">
                                @if($job->user)
                                <span><i class="fa fa-user me-1" style="color: #94a3b8;"></i>{{ $job->user->name }}</span>
                                @endif
                                <span><i class="fa fa-gbp me-1" style="color: #94a3b8;"></i>Budget: {{ $job->budget ? '£'.number_format((float) $job->budget, 2) : 'N/A' }}</span>
                                <span><i class="fa fa-clock-o me-1" style="color: #94a3b8;"></i>Needed by: {{ $job->needed_by?->format('d M Y') ?? 'N/A' }}</span>
                            </div>
                            <hr class="my-0" style="border-color: #e5e7eb; opacity: 1;">
                        </div>

                        <form method="POST" action="{{ route('supplier-panel.quotes.store', $job) }}" style="display: flex; flex-direction: column; overflow: hidden;">
                            @csrf
                            <div class="modal-body px-4 py-4">
                                <div class="row g-4">
                                    <div class="col-lg-7">
                                        <div class="form-card">
                                            <div class="section-header"><i class="fa fa-gbp" style="color: #2563eb;"></i>Pricing Details</div>
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block;">Price for job <span style="color: #dc2626;">*</span></label>
                                                    <div class="input-56-prefix">
                                                        <span class="prefix">£</span>
                                                        <input type="number" name="price_for_job" step="0.01" min="0" class="input-56" value="{{ old('price_for_job', optional($existingQuote)->price_for_job) }}" required placeholder="0.00">
                                                    </div>
                                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Your total charge for this job</div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block;">Discount offered</label>
                                                    <div class="input-56-prefix">
                                                        <span class="prefix">£</span>
                                                        <input type="number" name="discount_offered" step="0.01" min="0" class="input-56" value="{{ old('discount_offered', optional($existingQuote)->discount_offered) }}" placeholder="0.00">
                                                    </div>
                                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Optional discount to apply</div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block;">Delivery cost</label>
                                                    <div class="input-56-prefix">
                                                        <span class="prefix">£</span>
                                                        <input type="number" name="delivery_cost" step="0.01" min="0" class="input-56" value="{{ old('delivery_cost', optional($existingQuote)->delivery_cost) }}" placeholder="0.00">
                                                    </div>
                                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Shipping, travel or installation fees</div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block;">Total</label>
                                                    <div class="total-display">
                                                        <span class="total-label"><i class="fa fa-calculator me-1"></i>Final Total</span>
                                                        <span class="total-value js-total-display">£0.00</span>
                                                        <input type="hidden" name="total" step="0.01" min="0" value="{{ old('total', optional($existingQuote)->total_price ?? '0') }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @hasFeature('professional_quote')
                                        <div class="form-card">
                                            <div class="section-header"><i class="fa fa-calendar" style="color: #d97706;"></i>Delivery Details</div>
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block;">Estimated completion date</label>
                                                    <div class="input-56-icon">
                                                        <input type="date" name="estimated_completion_date" class="input-56" style="padding: 0 44px 0 16px;" value="{{ old('estimated_completion_date', optional($existingQuote)->estimated_completion_date?->format('Y-m-d')) }}">
                                                        <span class="icon"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">When will the work be completed?</div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block;">Warranty / Guarantee (months)</label>
                                                    <div class="input-56-icon">
                                                        <input type="number" name="warranty_months" min="0" class="input-56" style="padding: 0 44px 0 16px;" value="{{ old('warranty_months', optional($existingQuote)->warranty_months) }}" placeholder="e.g. 12">
                                                        <span class="icon"><i class="fa fa-shield"></i></span>
                                                    </div>
                                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Length of warranty period in months</div>
                                                </div>
                                            </div>
                                        </div>
                                        @endhasFeature

                                        <div class="form-card">
                                            <div class="section-header"><i class="fa fa-pencil-square-o" style="color: #7c3aed;"></i>Notes</div>
                                            <textarea name="notes" rows="4" class="form-textarea js-notes-textarea" style="min-height: 140px; resize: vertical;" placeholder="Include delivery details, installation, packaging, after-sales support or any additional information." oninput="var c=this.nextElementSibling; if(c)c.textContent=this.value.length + ' / 2000';">{{ old('notes', optional($existingQuote)->notes) }}</textarea>
                                            <div class="char-counter js-notes-counter">{{ strlen(old('notes', optional($existingQuote)->notes ?? '')) }} / 2000</div>
                                        </div>

                                        @hasFeature('professional_quote')
                                        <div class="form-card">
                                            <div class="section-header"><i class="fa fa-shield" style="color: #dc2626;"></i>Terms &amp; Conditions</div>
                                            <textarea name="terms" rows="4" class="form-textarea js-terms-textarea" style="min-height: 160px; resize: vertical;" placeholder="Specify payment terms, exclusions, validity period, warranty conditions or other contractual information." oninput="var c=this.nextElementSibling; if(c)c.textContent=this.value.length + ' / 3000';">{{ old('terms', optional($existingQuote)->terms) }}</textarea>
                                            <div class="char-counter js-terms-counter">{{ strlen(old('terms', optional($existingQuote)->terms ?? '')) }} / 3000</div>
                                            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Clearly define terms to protect both parties.</div>
                                        </div>
                                        @endhasFeature
                                    </div>

                                    <div class="col-lg-5">
                                        <div class="summary-card" style="position: sticky; top: 24px;">
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <i class="fa fa-file-text" style="color: #2563eb; font-size: 16px;"></i>
                                                <span style="font-size: 16px; font-weight: 700; color: #111827;">Quote Summary</span>
                                            </div>
                                            <div style="font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 4px;">{{ $job->title }}</div>
                                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 16px;">{{ $job->categoryId?->name ?? 'General' }}</div>

                                            <hr style="border-color: #e5e7eb; opacity: 1; margin: 12px 0;">

                                            <div class="summary-row">
                                                <span>Price for job</span>
                                                <span class="value js-summary-price">£0.00</span>
                                            </div>
                                            <div class="summary-row">
                                                <span>Discount</span>
                                                <span class="value js-summary-discount" style="color: #dc2626;">-£0.00</span>
                                            </div>
                                            <div class="summary-row">
                                                <span>Delivery cost</span>
                                                <span class="value js-summary-delivery">£0.00</span>
                                            </div>

                                            <hr style="border-color: #e5e7eb; opacity: 1; margin: 12px 0;">

                                            <div class="summary-total">
                                                <span class="label"><i class="fa fa-calculator me-1"></i>Final Total</span>
                                                <span class="value js-summary-total">£0.00</span>
                                            </div>

                                            @hasFeature('professional_quote')
                                            <hr style="border-color: #e5e7eb; opacity: 1; margin: 16px 0 12px;">
                                            <div class="summary-row">
                                                <span>Completion date</span>
                                                <span class="value js-summary-date">—</span>
                                            </div>
                                            <div class="summary-row">
                                                <span>Warranty</span>
                                                <span class="value js-summary-warranty">—</span>
                                            </div>
                                            @endhasFeature

                                            <hr style="border-color: #e5e7eb; opacity: 1; margin: 16px 0 12px;">

                                            @hasFeature('professional_quote')
                                            <span style="background: #eff6ff; color: #2563eb; border-radius: 20px; padding: 3px 12px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa fa-shield" style="font-size: 10px;"></i>Professional Quote
                                            </span>
                                            @else
                                            <span style="background: #f3f4f6; color: #6b7280; border-radius: 20px; padding: 3px 12px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa fa-file-text-o" style="font-size: 10px;"></i>Basic Quote
                                            </span>
                                            @endhasFeature

                                            <div style="font-size: 12px; color: #94a3b8; margin-top: 12px;">
                                                <i class="fa fa-refresh me-1"></i>Auto-updates as you type
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="sticky-footer px-4 pb-4 pt-3">
                                <hr class="my-0 mb-3" style="border-color: #e5e7eb; opacity: 1;">
                                <div class="d-flex justify-content-end gap-3">
                                    <button type="button" class="btn btn-premium btn-premium-outline" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-premium btn-premium-primary d-flex align-items-center gap-2" id="submitQuoteBtn{{ $job->id }}">
                                        <i class="fa fa-paper-plane-o" style="font-size: 14px;"></i>
                                        {{ $existingQuote ? 'Update Quote' : 'Submit Quote' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border rounded-4">No jobs matched your filters.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $jobs->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    function removeParams() {
        const url = new URL(window.location.href);
        url.searchParams.delete('search');
        url.searchParams.delete('category');
        url.searchParams.delete('sort');
        // window.history.replaceState({}, document.title, url.pathname + url.search);
        window.location.href = url.pathname + url.search;
    }

    document.addEventListener("DOMContentLoaded", function () {

        function fmt(n) { return '£' + (n || 0).toFixed(2); }

        document.querySelectorAll(".job-modal").forEach(function (modal) {

            var priceInput = modal.querySelector('input[name="price_for_job"]');
            var discountInput = modal.querySelector('input[name="discount_offered"]');
            var deliveryInput = modal.querySelector('input[name="delivery_cost"]');
            var totalInput = modal.querySelector('input[name="total"]');

            if (!priceInput || !discountInput || !deliveryInput || !totalInput) return;

            var totalDisplay = modal.querySelector('.js-total-display');
            var summaryPrice = modal.querySelector('.js-summary-price');
            var summaryDiscount = modal.querySelector('.js-summary-discount');
            var summaryDelivery = modal.querySelector('.js-summary-delivery');
            var summaryTotal = modal.querySelector('.js-summary-total');

            var dateInput = modal.querySelector('input[name="estimated_completion_date"]');
            var warrantyInput = modal.querySelector('input[name="warranty_months"]');
            var summaryDate = modal.querySelector('.js-summary-date');
            var summaryWarranty = modal.querySelector('.js-summary-warranty');

            function updateSummary() {
                var price = parseFloat(priceInput.value) || 0;
                var discount = parseFloat(discountInput.value) || 0;
                var delivery = parseFloat(deliveryInput.value) || 0;
                var total = Math.max(0, price - discount + delivery);
                var totalStr = total.toFixed(2);

                totalInput.value = totalStr;
                if (totalDisplay) totalDisplay.textContent = fmt(total);
                if (summaryPrice) summaryPrice.textContent = fmt(price);
                if (summaryDiscount) summaryDiscount.textContent = '-£' + discount.toFixed(2);
                if (summaryDelivery) summaryDelivery.textContent = fmt(delivery);
                if (summaryTotal) summaryTotal.textContent = fmt(total);

                if (summaryDate && dateInput) {
                    summaryDate.textContent = dateInput.value
                        ? new Date(dateInput.value + 'T12:00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
                        : '—';
                }
                if (summaryWarranty && warrantyInput) {
                    var w = parseInt(warrantyInput.value) || 0;
                    summaryWarranty.textContent = w ? w + ' month' + (w !== 1 ? 's' : '') : '—';
                }
            }

            updateSummary();

            priceInput.addEventListener("input", updateSummary);
            discountInput.addEventListener("input", updateSummary);
            deliveryInput.addEventListener("input", updateSummary);
            if (dateInput) dateInput.addEventListener("input", updateSummary);
            if (warrantyInput) warrantyInput.addEventListener("input", updateSummary);
        });

    });
   (function () {
        function formatTimeLeft(diffMs) {
            if (diffMs <= 0) return 'Ended';

            var totalSeconds = Math.floor(diffMs / 1000);
            var days = Math.floor(totalSeconds / 86400);
            var hours = Math.floor((totalSeconds % 86400) / 3600);
            var minutes = Math.floor((totalSeconds % 3600) / 60);
            var seconds = totalSeconds % 60;

            return days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's';
        }

        var timers = Array.from(document.querySelectorAll('.js-job-countdown'));
        if (!timers.length) return;

        function tick() {
            var now = new Date().getTime();

            timers.forEach(function (el) {
                var endAt = new Date(el.dataset.endAt).getTime();
                var endingSoonThreshold = parseInt(el.dataset.endingSoonThreshold, 10) || 86400;
                endingSoonThreshold = endingSoonThreshold * 1000;
                var badge = document.getElementById(el.dataset.statusBadgeId);
                var statusText = document.getElementById(el.dataset.statusTextId);
                var card = el.closest('.card');

                if (isNaN(endAt)) {
                    el.textContent = 'Time left: --';
                    return;
                }

                var diff = endAt - now;

                if (diff <= 0) {
                    el.classList.add('d-none');
                    el.classList.remove('text-danger');
                    el.classList.add('text-muted');
                    el.textContent = 'Time left: --';
                    if (statusText) statusText.textContent = 'Ended jobs';

                    if (badge) {
                        badge.textContent = 'Ended Jobs';
                        badge.classList.remove('bg-success', 'bg-warning', 'bg-secondary');
                        badge.classList.add('bg-danger');
                    }

                    return;
                }

                if (diff > endingSoonThreshold) {
                    el.classList.add('d-none');
                    el.classList.remove('text-danger');
                    el.classList.add('text-muted');
                    el.textContent = 'Time left: --';
                    if (statusText) statusText.textContent = 'Job active';

                    if (badge) {
                        badge.textContent = 'Active';
                        badge.classList.remove('bg-warning', 'bg-danger', 'bg-secondary');
                        badge.classList.add('bg-success');
                    }

                    if (card) {
                        card.style.setProperty('border-top', '5px solid var(--bs-success)', 'important');
                    }

                    return;
                }

                el.classList.remove('d-none');
                el.classList.remove('text-muted');
                el.classList.add('text-danger');
                if (statusText) statusText.textContent = 'Job ending soon';

                if (badge) {
                    badge.textContent = 'Ending Soon';
                    badge.classList.remove('bg-success', 'bg-danger', 'bg-secondary');
                    badge.classList.add('bg-warning');
                }

                el.textContent = 'Time left: ' + formatTimeLeft(diff);
            });
        }

        tick();
        setInterval(tick, 1000);
    })();
</script>
@endpush
