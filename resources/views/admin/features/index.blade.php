@extends('admin.layouts.app')
@section('title', 'Features')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Features</h3>
        <button class="btn btn-primary btn-sm" id="btnAddFeature">
            <i class="mdi mdi-plus me-1"></i> Add New Feature
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0" id="featuresTable">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th style="width:100px">Status</th>
                        <th style="width:100px">Order</th>
                        <th style="width:120px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($features as $feature)
                        <tr id="feature-row-{{ $feature->id }}">
                            <td>{{ $feature->id }}</td>
                            <td class="fw-semibold">{{ $feature->name }}</td>
                            <td><code>{{ $feature->slug }}</code></td>
                            <td class="text-muted small">{{ Str::limit($feature->description, 50) }}</td>
                            <td>
                                @if($feature->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $feature->sort_order }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-outline-primary btn-sm btn-edit-feature"
                                        data-id="{{ $feature->id }}"
                                        data-name="{{ $feature->name }}"
                                        data-slug="{{ $feature->slug }}"
                                        data-description="{{ $feature->description }}"
                                        data-is_active="{{ $feature->is_active ? '1' : '0' }}"
                                        data-sort_order="{{ $feature->sort_order }}">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm btn-delete-feature"
                                        data-id="{{ $feature->id }}"
                                        data-name="{{ $feature->name }}">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="mdi mdi-information-outline fs-3 d-block mb-2"></i>
                                No features found. Click "Add New Feature" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add / Edit Feature Modal --}}
<div class="modal fade" id="featureModal" tabindex="-1" aria-labelledby="featureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="featureModalLabel">Add New Feature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <input type="hidden" id="featureId">

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Feature Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" id="featureName" placeholder="e.g. Unlimited Quotes">
                        <div class="invalid-feedback">Name is required.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" id="featureSlug" placeholder="e.g. unlimited_quotes">
                        <div class="invalid-feedback">Slug is required.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea class="form-control rounded-3" id="featureDescription" rows="2"
                                  placeholder="Brief description of this feature..."></textarea>
                    </div>

                    <div class="col-6">
                        <label class="form-label fw-semibold small">Sort Order</label>
                        <input type="number" class="form-control rounded-3" id="featureSortOrder" placeholder="0" min="0">
                    </div>

                    <div class="col-6 d-flex align-items-end pb-1">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="featureIsActive" role="switch" checked>
                            <label class="form-check-label small" for="featureIsActive">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary rounded-3 px-4" id="btnSaveFeature">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="saveSpinner"></span>
                    <span id="saveBtnLabel">Save Feature</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteFeatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <span class="display-5 text-danger"><i class="mdi mdi-alert-circle-outline"></i></span>
                </div>
                <h6 class="fw-bold mb-1">Delete Feature?</h6>
                <p class="text-muted small mb-4">
                    You're about to delete <strong id="deleteFeatureName"></strong>. This cannot be undone.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger rounded-3 px-4" id="btnConfirmDeleteFeature">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="deleteSpinner"></span>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const featureModal   = new bootstrap.Modal('#featureModal');
    const deleteModal    = new bootstrap.Modal('#deleteFeatureModal');
    let deletingId       = null;

    function openFeatureModal(data = {}) {
        const isEdit = !!data.id;

        $('#featureModalLabel').text(isEdit ? 'Edit Feature' : 'Add New Feature');
        $('#saveBtnLabel').text(isEdit ? 'Update Feature' : 'Save Feature');

        $('#featureId').val(data.id ?? '');
        $('#featureName').val(data.name ?? '');
        $('#featureSlug').val(data.slug ?? '');
        $('#featureDescription').val(data.description ?? '');
        $('#featureSortOrder').val(data.sort_order ?? '');
        $('#featureIsActive').prop('checked', data.is_active === '1' || data.is_active === true);

        $('#featureModal .is-invalid').removeClass('is-invalid');
        featureModal.show();
    }

    $('#btnAddFeature').on('click', () => openFeatureModal());

    $(document).on('click', '.btn-edit-feature', function () {
        openFeatureModal($(this).data());
    });

    function validate() {
        let valid = true;
        ['#featureName', '#featureSlug'].forEach(sel => {
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

    $('#btnSaveFeature').on('click', function () {
        if (!validate()) return;

        const id     = $('#featureId').val();
        const isEdit = !!id;
        const url    = isEdit
            ? "{{ route('admin.features.update', ['feature' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', id)
            : "{{ route('admin.features.store') }}";
        const method = isEdit ? 'PUT' : 'POST';

        const payload = {
            name:        $('#featureName').val().trim(),
            slug:        $('#featureSlug').val().trim(),
            description: $('#featureDescription').val().trim(),
            sort_order:  $('#featureSortOrder').val() || 0,
            is_active:   $('#featureIsActive').is(':checked') ? 1 : 0,
        };

        $('#saveSpinner').removeClass('d-none');
        $('#btnSaveFeature').prop('disabled', true);

        $.ajax({
            url, method,
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success(response) {
                featureModal.hide();
                showToast(response.message || 'Feature saved.', 'success');
                setTimeout(() => window.location.reload(), 1500);
            },
            error(xhr) {
                const errors = xhr.responseJSON?.errors ?? {};
                handleServerErrors(errors);
                if (!Object.keys(errors).length) {
                    showToast('Something went wrong.', 'danger');
                }
            },
            complete() {
                $('#saveSpinner').addClass('d-none');
                $('#btnSaveFeature').prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.btn-delete-feature', function () {
        deletingId = $(this).data('id');
        $('#deleteFeatureName').text($(this).data('name'));
        deleteModal.show();
    });

    $('#btnConfirmDeleteFeature').on('click', function () {
        if (!deletingId) return;

        $('#deleteSpinner').removeClass('d-none');
        $('#btnConfirmDeleteFeature').prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.features.destroy', ['feature' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', deletingId),
            method: 'DELETE',
            success() {
                $(`#feature-row-${deletingId}`).fadeOut(300, function () { $(this).remove(); });
                deleteModal.hide();
                showToast('Feature deleted.', 'success');
                deletingId = null;
            },
            error() {
                showToast('Could not delete the feature.', 'danger');
            },
            complete() {
                $('#deleteSpinner').addClass('d-none');
                $('#btnConfirmDeleteFeature').prop('disabled', false);
            }
        });
    });

    function handleServerErrors(errors) {
        const map = {
            name:        '#featureName',
            slug:        '#featureSlug',
            description: '#featureDescription',
            sort_order:  '#featureSortOrder',
        };
        Object.entries(errors).forEach(([field, messages]) => {
            const $el = $(map[field]);
            if ($el.length) {
                $el.addClass('is-invalid');
                $el.siblings('.invalid-feedback').text(messages[0]);
            }
        });
    }

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

    $('#featureName').on('input', function () {
        if ($('#featureId').val()) return;
        const slug = $(this).val()
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_|_$/g, '');
        $('#featureSlug').val(slug);
    });
});
</script>
@endpush
@endsection
