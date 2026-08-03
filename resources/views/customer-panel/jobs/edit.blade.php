@extends('customer-panel.layouts.app')
@section('title', 'Edit Job')

@section('content')
<div class="content-wrapper p-3">
    

    <form id="jobEditForm" method="POST" enctype="multipart/form-data">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                <div>
                    
                    <h3 class="mt-2 mb-1">Edit job - {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</h3>
                    <p class="text-muted mb-0">Update the details of your request.</p>
                </div>
                <a href="{{ route('customer-panel.jobs.show', $job) }}" class="btn btn-light border rounded-4">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to job
                </a>
            </div>
            <div class="card-body p-4">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label mb-1">Job Title <span class="text-danger">*</span></label>
                        <input type="text" id="edit_job_title" name="title" value="{{ old('title', $job->title) }}" class="form-control" placeholder="Need football kits for school team">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label mb-1">Category</label>
                        <select name="category" id="edit_job_category" class="form-select text-dark">
                            <option value="">Select Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category', $job->category) == $cat->id)>{{ ucfirst($cat->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label mb-1">Organisation Name</label>
                        <input type="text" id="edit_job_organisation_name" name="organisation_name" value="{{ old('organisation_name', $job->organisation_name) }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label mb-1">Location</label>
                        <input type="text" id="edit_job_location" name="location" value="{{ old('location', $job->location) }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label mb-1">Budget</label>
                        <input type="number" min="0" step="0.01" id="edit_job_budget" name="budget" value="{{ old('budget', $job->budget) }}" class="form-control" placeholder="Not specified">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label mb-1">Needed By</label>
                        <input type="text" id="edit_needed_by" autocomplete="off" placeholder="Select date" name="needed_by" value="{{ old('needed_by', $job->needed_by?->format('Y-m-d H:i')) }}" class="form-control">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label mb-1">Job Description <span class="text-danger">*</span></label>
                        <textarea id="edit_job_description" name="description" rows="6" class="form-control" placeholder="Tell suppliers what you need, quantities, delivery expectations, colors, sizes, or any special notes.">{{ old('description', $job->description) }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div id="edit-dynamic-fields">
                            @php
                                $renderableItems = empty($groupedDynamicFieldValues) ? [[]] : $groupedDynamicFieldValues;
                                $itemIndex = 0;
                            @endphp
                            @foreach ($renderableItems as $itemValues)
                                @php $itemIndex++; @endphp
                                @include('customer-panel.jobs.dynamic-fields', [
                                    'fields' => $fields,
                                    'itemIndex' => $itemIndex,
                                    'itemValues' => $itemValues,
                                ])
                            @endforeach
                        </div>
                        @if ($fields->count())
                            <div class="add-more-item text-end mt-3">
                                <button type="button" class="btn btn-primary rounded-pill" id="editAddMoreFieldBtn">
                                    <i class="fa fa-plus"></i> Add More Item
                                </button>
                            </div>
                        @endif
                    </div>
                </div>                
            </div>
            <div class="card-footer d-flex justify-content-end gap-2 py-3">
                <a href="{{ route('customer-panel.jobs.show', $job) }}" class="btn btn-light border rounded-4 px-4">Cancel</a>
                <button id="updateJobBtn" type="submit" class="btn btn-primary rounded-4 px-5">Update Job</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let editItemCount = {{ $itemCount }};

    function clearValidationErrors(form) {
        form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach((el) => el.remove());
    }

    function ensureAddMoreButton(containerSelector, buttonId, label = 'Add More Item') {
        const buttonSelector = buttonId.startsWith('#') ? buttonId : `#${buttonId}`;

        if ($(buttonSelector).length > 0) {
            return;
        }

        $(containerSelector).after(`
            <div class="add-more-item text-end mt-3">
                <button type="button" class="btn btn-primary rounded-pill" id="${buttonSelector.replace('#', '')}">
                    <i class="fa fa-plus"></i> ${label}
                </button>
            </div>
        `);
    }

    function loadCategoryFields(categoryId, targetSelector, itemIndex, isAppend = false, buttonLabel = 'Add More Item') {
        if (!categoryId) {
            $(targetSelector).html('');
            $('.add-more-item').remove();
            return;
        }

        $.ajax({
            url: "{{ route('customer-panel.get.category.fields', ':id') }}".replace(':id', categoryId),
            type: 'GET',
            data: {
                item_index: itemIndex
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Loading fields...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            },
            success: function (response) {
                Swal.close();

                if (!isAppend) {
                    $(targetSelector).html('');
                    $('.add-more-item').remove();
                }

                if (response.html) {
                    $(targetSelector).append(response.html);
                    ensureAddMoreButton(targetSelector, 'editAddMoreFieldBtn', buttonLabel);
                }

                if (targetSelector === '#edit-dynamic-fields') {
                    editItemCount = itemIndex;
                }
            },
            error: function () {
                Swal.close();
                toastr.error('Unable to load dynamic fields.');
            }
        });
    }

    function findFieldElement(form, field) {
        let input = form.querySelector(`[name="${field}"]`);

        if (input) {
            return input;
        }

        const match = field.match(/^dynamic_fields\.(\d+)\.(\d+)(?:\.(\d+))?$/);

        if (match) {
            const selector = `[name="dynamic_fields[${match[1]}][${match[2]}]"], [name="dynamic_fields[${match[1]}][${match[2]}][]"]`;
            return form.querySelector(selector);
        }

        return null;
    }

    function showValidationErrors(form, errors) {
        let firstError = null;

        Object.keys(errors).forEach((field) => {
            const input = findFieldElement(form, field);
            const message = errors[field][0];

            if (input) {
                input.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.innerText = message;

                if (input.parentNode) {
                    input.parentNode.appendChild(feedback);
                }

                if (!firstError) {
                    firstError = input;
                }
            }

            toastr.error(message);
        });

        if (firstError) {
            firstError.focus();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('jobEditForm');
        const updateBtn = document.getElementById('updateJobBtn');
        const csrfToken = "{{ csrf_token() }}";
        const updateUrl = "{{ route('customer-panel.jobs.update', $job) }}";

        const editNeededByInput = document.getElementById('edit_needed_by');
        if (editNeededByInput && typeof flatpickr !== 'undefined') {
            flatpickr('#edit_needed_by', {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                time_24hr: false,
                minDate: 'today',
                disableMobile: true,
                defaultDate: editNeededByInput.value || null
            });
        }

        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#edit_job_category').select2({
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
        }

        $('#edit_job_category').on('change', function () {
            const categoryId = $(this).val();

            if (categoryId) {
                loadCategoryFields(categoryId, '#edit-dynamic-fields', 1, false, 'Add More Item');
            } else {
                $('#edit-dynamic-fields').html('');
                $('.add-more-item').remove();
                editItemCount = 0;
            }
        });

        $(document).on('click', '#editAddMoreFieldBtn', function () {
            const categoryId = $('#edit_job_category').val();

            if (categoryId) {
                loadCategoryFields(categoryId, '#edit-dynamic-fields', editItemCount + 1, true, 'Add More Item');
            }
        });

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearValidationErrors(form);

            Swal.fire({
                title: 'Please wait...',
                text: 'Updating your job',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            try {
                const response = await fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(form),
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    if (response.status === 422 && payload.errors) {
                        showValidationErrors(form, payload.errors);
                    } else {
                        toastr.error(payload.message || 'Something went wrong!');
                    }

                    Swal.close();
                    return;
                }

                updateBtn.disabled = true;
                Swal.close();

                await Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: payload.message || 'Job updated successfully.',
                    showConfirmButton: false,
                    timer: 1500
                });

                window.location.href = "{{ route('customer-panel.jobs.show', $job) }}";
            } catch (error) {
                Swal.close();
                toastr.error('Something went wrong!');
            }
        });
    });
</script>
@endpush
