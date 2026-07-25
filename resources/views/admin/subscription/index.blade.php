@extends('admin.layouts.app')
@section('title', 'Subscription Settings')
 

@section('content')
<div class="container-fluid">

    {{-- ───────────── Header ───────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Subscription Settings</h3>
        <button class="btn btn-primary btn-sm" id="btnAddPlan">
            <i class="mdi mdi-plus me-1"></i> Add New Plan
        </button>
    </div>

    {{-- ───────────── Plan Cards ───────────── --}}
    <div class="row g-4" id="plansRow">
        @foreach($plans as $plan)
        <div class="col-12 col-lg-4" data-plan-id="{{ $plan->id }}" id="plan-card-{{ $plan->id }}">
            <div class="card border-0 shadow-sm rounded-4 h-100 {{ $plan->is_popular ? 'border border-primary' : '' }}">
                <div class="card-body p-4 d-flex flex-column position-relative">

                    <div class="drag-handle" style="position:absolute;top:12px;right:12px;cursor:grab;z-index:2">
                        <i class="mdi mdi-drag-vertical text-muted" style="font-size:1.2rem"></i>
                    </div>
                    @if($plan->is_popular)
                        <span class="badge bg-primary align-self-start mb-3">POPULAR</span>
                    @endif

                    <h5 class="text-uppercase text-muted fw-bold">{{ $plan->name }}</h5>
                    <p class="text-muted small">{{ $plan->description }}</p>
                    <h2 class="fw-bold mb-0">{{ $plan->price_formatted }}</h2>

                    {{-- @if(! $plan->is_free)
                        <p class="text-muted small mb-3">/ {{ $plan->duration_label }}</p>
                    @endif --}}

                    <div class="mt-auto">
                        <ul class="list-unstyled small mb-3">
                            @foreach($plan->display_features as $feature)
                                <li class="mb-2">
                                    <i class="mdi mdi-check text-primary me-1"></i>{{ $feature }}
                                </li>
                            @endforeach
                        </ul>

                        {{-- Card actions --}}
                        <div class="d-flex gap-2">
                            <button
                                class="btn btn-outline-primary btn-sm flex-fill btn-edit-plan"
                                data-id="{{ $plan->id }}"
                                data-name="{{ $plan->name }}"
                                data-slug="{{ $plan->slug }}"
                                data-description="{{ $plan->description }}"
                                data-price="{{ $plan->price / 100 }}"
                                data-duration="{{ $plan->duration ?? '' }}"
                                data-is_free="{{ $plan->is_free ? '1' : '0' }}"
                                data-is_popular="{{ $plan->is_popular ? '1' : '0' }}"
                                data-sort_order="{{ $plan->sort_order ?? 0 }}"
                                data-features="{{ implode("\n", $plan->features ?? []) }}"
                                data-feature_ids="{{ json_encode($plan->featureModels->pluck('id')->toArray()) }}"
                            >
                                <i class="mdi mdi-pencil me-1"></i>Edit
                            </button>
                            <button
                                class="btn btn-danger btn-sm btn-delete-plan"
                                data-id="{{ $plan->id }}"
                                data-name="{{ $plan->name }}"
                            >
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>


{{-- ═══════════════════════════════════════════
     ADD / EDIT PLAN MODAL
═══════════════════════════════════════════ --}}
<div class="modal fade" id="planModal" tabindex="-1" aria-labelledby="planModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">

            {{-- Header --}}
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="planModalLabel">Add New Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body px-4 py-3">
                <input type="hidden" id="planId">

                <div class="row g-3">

                    {{-- Plan Name --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" id="planName" placeholder="e.g. Gold">
                        <div class="invalid-feedback">Plan name is required.</div>
                    </div>

                    {{-- Slug --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" id="planSlug" placeholder="e.g. gold">
                        <div class="invalid-feedback">Slug is required.</div>
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea class="form-control rounded-3" id="planDescription" rows="2"
                                  placeholder="Short description of this plan…"></textarea>
                    </div>

                    {{-- Price --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Price</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-3">£</span>
                            <input type="number" class="form-control rounded-end-3" id="planPrice"
                                   placeholder="0.00" min="0" step="0.01">
                        </div>
                    </div>

                    {{-- Duration --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold small">Duration</label>
                        <select class="form-select rounded-3 text-dark" id="planDuration">
                            <option value="">Select Duration</option>
                            <option value="monthly">Monthly</option>
                            <option value="3_months">3 Months</option>
                            <option value="6_months">6 Months</option>
                            <option value="yearly">Yearly</option>
                            <option value="lifetime">Lifetime</option>
                        </select>
                    </div>

                    {{-- Flags --}}
                    <div class="col-12 col-md-4 ">
                        <label class="form-label fw-semibold small">Mark Plan</label>
                        <div class="d-flex flex-co lumn justify-content-evenly gap-2 pb-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="planIsFree" role="switch">
                                <label class="form-check-label small" for="planIsFree">Free Plan</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="planIsPopular" role="switch">
                                <label class="form-check-label small" for="planIsPopular">Mark as Popular</label>
                            </div>
                        </div>
                    </div>

                    {{-- Feature Checkboxes --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Select Features</label>
                        <div class="border rounded-3 p-3 row" style="max-height:200px;overflow-y:auto;">
                            @forelse($features as $feature)
                                <div class="col-md-4 mb-3 form-switch">
                                    <input class="form-check-input feature-checkbox" type="checkbox"
                                           id="feature-{{ $feature->id }}" value="{{ $feature->id }}">
                                    <label class="form-check-label small" for="feature-{{ $feature->id }}">
                                        {{ $feature->name }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">
                                    No features available.
                                    <a href="{{ route('admin.features.index') }}">Create features</a> first.
                                </p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Free-text Features (legacy) --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold small">
                            Extra Features
                            <span class="text-muted fw-normal">(one per line, shown alongside selected features)</span>
                        </label>
                        <textarea class="form-control rounded-3 font-monospace" id="planFeatures" rows="3"
                                  placeholder="Unlimited access&#10;Priority support&#10;API access"></textarea>
                        <div class="form-text">These appear in addition to the checked features above.</div>
                    </div>

                </div>{{-- /row --}}
            </div>

            {{-- Footer --}}
            <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary rounded-3 px-4" id="btnSavePlan">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="saveSpinner"></span>
                    <span id="saveBtnLabel">Save Plan</span>
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════
     DELETE CONFIRMATION MODAL
═══════════════════════════════════════════ --}}
<div class="modal fade" id="deletePlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <span class="display-5 text-danger"><i class="mdi mdi-alert-circle-outline"></i></span>
                </div>
                <h6 class="fw-bold mb-1">Delete Plan?</h6>
                <p class="text-muted small mb-4">
                    You're about to delete <strong id="deletePlanName"></strong>. This cannot be undone.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger rounded-3 px-4" id="btnConfirmDelete">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="deleteSpinner"></span>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('styles')
<style>
.sortable-ghost {
    opacity: 0.4;
    background: #f0f0f0;
}
.sortable-chosen {
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}
</style>
@endpush

{{-- ═══════════════════════════════════════════
     JQUERY — MODAL & AJAX LOGIC
═══════════════════════════════════════════ --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.7/Sortable.min.js"></script>
<script>
$(function () {

    /* ── CSRF token for all jQuery AJAX calls ───────────────────────── */
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    /* ── Drag & Drop reorder ──────────────────────────────────────── */
    const reorderUrl = "{{ route('admin.subscription.reorder') }}";

    function initSortable() {
        Sortable.create(document.getElementById('plansRow'), {
            handle: '.drag-handle',
            animation: 200,
            ghostClass: 'sortable-ghost',
            onEnd: function () {
                const ids = [];
                document.querySelectorAll('#plansRow > [data-plan-id]').forEach(el => {
                    ids.push(parseInt(el.dataset.planId));
                });
                $.ajax({
                    url: reorderUrl,
                    method: 'POST',
                    data: JSON.stringify({ ids }),
                    contentType: 'application/json',
                });
            }
        });
    }
    initSortable();

    const planModal   = new bootstrap.Modal('#planModal');
    const deleteModal = new bootstrap.Modal('#deletePlanModal');
    let deletingId    = null;

    /* ─────────────────────────────────────────────────────────────────
       HELPER — reset & populate modal
    ───────────────────────────────────────────────────────────────── */
    function openPlanModal(data = {}) {
        const isEdit = !!data.id;

        $('#planModalLabel').text(isEdit ? 'Edit Plan' : 'Add New Plan');
        $('#saveBtnLabel').text(isEdit ? 'Update Plan' : 'Save Plan');

        $('#planId').val(data.id           ?? '');
        $('#planName').val(data.name       ?? '');
        $('#planSlug').val(data.slug       ?? '');
        $('#planDescription').val(data.description ?? '');
        $('#planPrice').val(data.price     ?? '');
        $('#planDuration').val(data.duration ?? '');
        $('#planIsFree').prop('checked',   !!data.is_free);
        $('#planIsPopular').prop('checked', !!data.is_popular);
        $('#planFeatures').val(data.features ?? '');

        // uncheck all feature checkboxes first
        $('.feature-checkbox').prop('checked', false);

        // check the ones that belong to this plan
        const featureIds = data.feature_ids || [];
        if (featureIds.length) {
            featureIds.forEach(function (id) {
                $(`#feature-${id}`).prop('checked', true);
            });
        }

        // clear previous validation states
        $('#planModal .is-invalid').removeClass('is-invalid');

        planModal.show();
    }

    /* ─────────────────────────────────────────────────────────────────
       OPEN — Add New
    ───────────────────────────────────────────────────────────────── */
    $('#btnAddPlan').on('click', () => openPlanModal());

    /* ─────────────────────────────────────────────────────────────────
       OPEN — Edit  (data-* attributes on every edit button)
    ───────────────────────────────────────────────────────────────── */
    $(document).on('click', '.btn-edit-plan', function () {
        openPlanModal($(this).data());
    });

    /* ─────────────────────────────────────────────────────────────────
       CLIENT-SIDE VALIDATION
    ───────────────────────────────────────────────────────────────── */
    function validate() {
        let valid = true;
        ['#planName', '#planSlug'].forEach(sel => {
            const $el = $(sel);
            if (!$el.val().trim()) {
                $el.addClass('is-invalid');
                valid = false;
            } else {
                $el.removeClass('is-invalid');
            }
        });
        return valid;
    }


    /* ─────────────────────────────────────────────────────────────────
       SAVE (Create or Update)
    ───────────────────────────────────────────────────────────────── */
    $('#btnSavePlan').on('click', function () {
        if (!validate()) return;

        const id      = $('#planId').val();
        const isEdit  = !!id;
        const url     = isEdit
            ? "{{ route('admin.subscription.update', ['plan' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', id)
            : "{{ route('admin.subscription.add') }}";
        const method  = isEdit ? 'PUT' : 'POST';

        const payload = {
            name:        $('#planName').val().trim(),
            slug:        $('#planSlug').val().trim(),
            description: $('#planDescription').val().trim(),
            price:       $('#planPrice').val(),
            duration:    $('#planDuration').val(),
            is_free:     $('#planIsFree').is(':checked') ? 1 : 0,
            is_popular:  $('#planIsPopular').is(':checked') ? 1 : 0,
            features:    $('#planFeatures').val()
                            .split('\n')
                            .map(f => f.trim())
                            .filter(Boolean),
            feature_ids: $('.feature-checkbox:checked').map(function () {
                return parseInt($(this).val());
            }).get(),
        };

        // loading state
        $('#saveSpinner').removeClass('d-none');
        $('#btnSavePlan').prop('disabled', true);

        $.ajax({
            url, method,
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success(response) {
                planModal.hide();

                if (isEdit) {
                    updateCard(id, response.plan ?? payload);
                    showToast('Plan updated successfully.', 'success');
                } else {
                    appendCard(response.plan);
                    showToast('Plan added successfully.', 'success');
                }

                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            },
            error(xhr) {
                const errors = xhr.responseJSON?.errors ?? {};
                handleServerErrors(errors);
                if (!Object.keys(errors).length) {
                    showToast('Something went wrong. Please try again.', 'danger');
                }
            },
            complete() {
                $('#saveSpinner').addClass('d-none');
                $('#btnSavePlan').prop('disabled', false);
            }
        });
    });

    /* ─────────────────────────────────────────────────────────────────
       OPEN — Delete confirmation
    ───────────────────────────────────────────────────────────────── */
    $(document).on('click', '.btn-delete-plan', function () {
        deletingId = $(this).data('id');
        $('#deletePlanName').text($(this).data('name'));
        deleteModal.show();
    });

    /* ─────────────────────────────────────────────────────────────────
       CONFIRM DELETE
    ───────────────────────────────────────────────────────────────── */
    $('#btnConfirmDelete').on('click', function () {
        if (!deletingId) return;

        $('#deleteSpinner').removeClass('d-none');
        $('#btnConfirmDelete').prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.subscription.destroy', ['plan' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', deletingId),
            method: 'DELETE',
            success() {
                $(`#plan-card-${deletingId}`).fadeOut(300, function () { $(this).remove(); });
                deleteModal.hide();
                showToast('Plan deleted.', 'success');
                deletingId = null;
            },
            error() {
                showToast('Could not delete the plan.', 'danger');
            },
            complete() {
                $('#deleteSpinner').addClass('d-none');
                $('#btnConfirmDelete').prop('disabled', false);
            }
        });
    });

    /* ─────────────────────────────────────────────────────────────────
       HELPERS — DOM update
    ───────────────────────────────────────────────────────────────── */

    /** Append a freshly created plan card (server returns the full plan object). */
    function appendCard(plan) {
        const html = buildCardHtml(plan);
        $('#plansRow').append(html);
    }

    /** Patch an existing card's text content without a full page reload. */
    function updateCard(id, plan) {
        const $card = $(`#plan-card-${id}`);
        $card.find('h5').text(plan.name);
        $card.find('p.text-muted.small').first().text(plan.description);
        $card.find('h2').text(plan.price_formatted ?? `£${parseFloat(plan.price / 100).toFixed(2)}`);

        // Rebuild feature list (merge relationship + legacy)
        const features = [
            ...(Array.isArray(plan.feature_names) ? plan.feature_names : []),
            ...(Array.isArray(plan.features) ? plan.features : []),
        ];
        const $ul = $card.find('ul.list-unstyled');
        $ul.empty();
        const seen = new Set();
        features.forEach(f => {
            if (seen.has(f)) return;
            seen.add(f);
            $ul.append(`<li class="mb-2"><i class="mdi mdi-check text-primary me-1"></i>${$('<span>').text(f).html()}</li>`);
        });

        // Sync data-* on edit button so the next edit pre-fills correctly
        $card.find('.btn-edit-plan')
            .data('name',        plan.name)
            .data('slug',        plan.slug)
            .data('description', plan.description)
            .data('price',       (plan.price / 100).toFixed(2))
            .data('duration',    plan.duration)
            .data('is_free',     plan.is_free ? '1' : '0')
            .data('is_popular',  plan.is_popular ? '1' : '0')
            .data('sort_order',  plan.sort_order ?? 0)
            .data('features',    features.join('\n'))
            .data('feature_ids', plan.feature_ids || []);
    }

    /** Build card HTML for a newly saved plan (used by appendCard). */
    function buildCardHtml(plan) {
        const featureNames = Array.isArray(plan.feature_names) ? plan.feature_names : [];
        const legacyFeatures = Array.isArray(plan.features) ? plan.features : [];
        const allFeatures = [...new Set([...featureNames, ...legacyFeatures])];
        const featureItems = allFeatures.map(f =>
            `<li class="mb-2"><i class="mdi mdi-check text-primary me-1"></i>${$('<span>').text(f).html()}</li>`
        ).join('');

        const popularBadge = plan.is_popular
            ? `<span class="badge bg-primary align-self-start mb-3">POPULAR</span>` : '';

        const durationHtml = plan.is_free
            ? '' : `<p class="text-muted small mb-3">/ ${plan.duration_label ?? plan.duration}</p>`;

        return `
        <div class="col-12 col-lg-4" data-plan-id="${plan.id}" id="plan-card-${plan.id}">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column position-relative">
                    <div class="drag-handle" style="position:absolute;top:12px;right:12px;cursor:grab;z-index:2">
                        <i class="mdi mdi-drag-vertical text-muted" style="font-size:1.2rem"></i>
                    </div>
                    ${popularBadge}
                    <h5 class="text-uppercase text-muted fw-bold">${$('<span>').text(plan.name).html()}</h5>
                    <p class="text-muted small">${$('<span>').text(plan.description).html()}</p>
                    <h2 class="fw-bold mb-0">${$('<span>').text(plan.price_formatted ?? '£' + parseFloat(plan.price / 100).toFixed(2)).html()}</h2>
                    ${durationHtml}
                    <div class="mt-auto">
                        <ul class="list-unstyled small mb-3">${featureItems}</ul>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm flex-fill btn-edit-plan"
                                data-id="${plan.id}"
                                data-name="${$('<span>').text(plan.name).html()}"
                                data-slug="${$('<span>').text(plan.slug).html()}"
                                data-description="${$('<span>').text(plan.description).html()}"
                                data-price="${(plan.price / 100).toFixed(2)}"
                                data-duration="${plan.duration ?? ''}"
                                data-is_free="${plan.is_free ? '1' : '0'}"
                                data-is_popular="${plan.is_popular ? '1' : '0'}"
                                data-sort_order="${plan.sort_order ?? 0}"
                                data-features="${$('<span>').text(features.join('\n')).html()}"
                                data-feature_ids='${JSON.stringify(plan.feature_ids || [])}'>
                                <i class="mdi mdi-pencil me-1"></i>Edit
                            </button>
                            <button class="btn btn-outline-danger btn-sm btn-delete-plan"
                                data-id="${plan.id}"
                                data-name="${$('<span>').text(plan.name).html()}">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    /* ─────────────────────────────────────────────────────────────────
       HELPER — server-side validation errors
    ───────────────────────────────────────────────────────────────── */
    function handleServerErrors(errors) {
        const map = {
            name:        '#planName',
            slug:        '#planSlug',
            description: '#planDescription',
            price:       '#planPrice',
            duration:    '#planDuration',
            features:    '#planFeatures',
        };
        Object.entries(errors).forEach(([field, messages]) => {
            const $el = $(map[field]);
            if ($el.length) {
                $el.addClass('is-invalid');
                $el.siblings('.invalid-feedback').text(messages[0]);
            }
        });
    }

    /* ─────────────────────────────────────────────────────────────────
       HELPER — Bootstrap toast notification
    ───────────────────────────────────────────────────────────────── */
    function showToast(message, type = 'success') {
        const id = `toast-${Date.now()}`;
        const icon = type === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline';
        const html = `
        <div id="${id}" class="toast align-items-center text-bg-${type} border-0 rounded-3 shadow-sm"
             role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3500">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="mdi ${icon} fs-5"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;

        let $container = $('#toastContainer');
        if (!$container.length) {
            $container = $('<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1100"></div>');
            $('body').append($container);
        }
        $container.append(html);
        const toastEl = new bootstrap.Toast(document.getElementById(id));
        toastEl.show();
        $(`#${id}`).on('hidden.bs.toast', function () { $(this).remove(); });
    }

    /* ─────────────────────────────────────────────────────────────────
       Auto-generate slug from name (Add mode only)
    ───────────────────────────────────────────────────────────────── */
    $('#planName').on('input', function () {
        if ($('#planId').val()) return; // don't overwrite on edit
        const slug = $(this).val()
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_|_$/g, '');
        $('#planSlug').val(slug);
    });

    /* ─────────────────────────────────────────────────────────────────
       Disable price/duration when Free Plan is toggled
    ───────────────────────────────────────────────────────────────── */
    $('#planIsFree').on('change', function () {
        const free = $(this).is(':checked');
        $('#planPrice, #planDuration').prop('disabled', free);
        if (free) { $('#planPrice').val('0'); $('#planDuration').val(''); }
    });

});
</script>
@endpush
@endsection