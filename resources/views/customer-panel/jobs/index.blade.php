@extends('customer-panel.layouts.app')
@section('title', 'My Jobs')

@section('content')
<div class="content-wrapper p-3">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h3 class="mb-1">My posted jobs</h3>
            <p class="text-muted mb-0">Track every request and see how many suppliers have replied.</p>
        </div>
        <button id="openCreateJobModal" type="button" class="btn btn-primary rounded-4" data-bs-toggle="modal" data-bs-target="#postjobmodal">Post New Job</button>
    </div>

    <div class="row">
        @forelse ($jobs as $job)
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 job-card">
                    <div class="card-body p-4">

                        <!-- Badge -->
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <span class="badge bg-info text-white rounded-pill mb-3">
                                Job Details
                            </span>
                            <span class="rounded badge bg-{{ $job->status=='open' ? 'success' : 'secondary' }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h5 class="fw-bold mb-2">{{ $job->title }}</h5>

                        <p class="text-muted small mb-3">
                            Needed by:
                            {{ $job->needed_by?->diffForHumans() ?? 'No deadline set' }}
                        </p>

                        <!-- Info Box -->
                        <div class="  rounded-4 p-3 mb-3" style="background: #f7f9fc;">
                            <div class="small mb-2">
                                <span class="text-muted">Category:</span>
                                <strong>{{ ucfirst($job->categoryId?->name ?? 'General') }}</strong>
                            </div>

                            <div class="small mb-2">
                                <span class="text-muted">Location:</span>
                                <strong>{{ $job->location ?: 'Not specified' }}</strong>
                            </div>

                            <div class="small mb-2">
                                <span class="text-muted">Budget:</span>
                                <strong class="text-success">
                                    {{ $job->budget ? '£'.number_format($job->budget,2) : 'Not shared' }}
                                </strong>
                            </div> 
                        </div>

                        <!-- Description -->
                        <p class="small text-muted mb-3">
                            {{ \Illuminate\Support\Str::limit($job->description, 80) }}
                        </p>

                        <!-- Footer -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-semibold">
                                {{ $job->quotes_count }} quotes
                            </div>

                            <div class="d-flex gap-1">
                                <button class="btn btn-light btn-sm rounded-circle border view-job-btn"
                                    data-job-id="{{ $job->id }}">
                                    <i class="fa fa-eye"></i>
                                </button>

                                <button class="btn btn-light btn-sm rounded-circle border edit-job-btn"
                                    data-job-id="{{ $job->id }}">
                                    <i class="fa fa-pencil"></i>
                                </button>

                                <button class="btn btn-light btn-sm rounded-circle border text-danger delete-job-btn"
                                    data-delete-url="{{ route('customer-panel.jobs.destroy', $job->id) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light rounded-4">
                    No jobs posted yet.
                </div>
            </div>
        @endforelse
    </div>
    

    <div class="mt-4">
        {{ $jobs->links('pagination::bootstrap-5') }}
    </div>

    <div class="modal fade" id="postjobmodal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="postjobmodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered  ">
            <div class="modal-content">
                <form id="jobForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="postjobmodalLabel">Post a Job</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label mb-1">Job Title</label>
                                <input type="text" id="job_title" name="title" value="{{ old('title') }}" class="form-control" placeholder="Need football kits for school team">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label mb-1">Category</label>
                                <select name="category" id="job_category" class="form-select text-dark">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ ucfirst($cat->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label mb-1">Organisation Name</label>
                                <input type="text" id="job_organisation_name" name="organisation_name" value="{{ old('organisation_name', auth()->user()->customerProfile?->school_name) }}" class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label mb-1">Location</label>
                                <input type="text" id="job_location" name="location" value="{{ old('location', auth()->user()->customerProfile?->county) }}" class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label mb-1">Budget</label>
                                <input type="number" min="0" step="0.01" id="job_budget" name="budget" value="{{ old('budget') }}" class="form-control" placeholder="Not specified">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label mb-1">Needed By</label>
                                <input type="text" id="needed_by" autocomplete="off" placeholder="Select date" name="needed_by" value="{{ old('needed_by') }}" class="form-control">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label mb-1">Job Description</label>
                                <textarea id="job_description" name="description" rows="6" class="form-control" placeholder="Tell suppliers what you need, quantities, delivery expectations, colors, sizes, or any special notes.">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div id="dynamic-fields"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary px-5  rounded-4" data-bs-dismiss="modal">Close</button>
                        <button id="jobSubmitBtn" type="submit" class="btn btn-primary px-5  rounded-4">Post Job</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="jobDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4">
                <div class="modal-header border-0">
                    <div>
                        <div class="small text-muted" id="jobDetailsMeta">Job details</div>
                        <h5 class="modal-title mb-0" id="jobDetailsModalLabel">Job details</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="jobViewSection">
                        <div id="jobViewContent"></div>
                    </div>

                    <div id="jobEditSection" class="d-none">
                        <form id="jobEditForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div id="jobEditMethod"></div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Job Title</label>
                                    <input type="text" id="edit_job_title" name="title" class="form-control" placeholder="Need football kits for school team">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Category</label>
                                    <select name="category" id="edit_job_category" class="form-select text-dark">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ ucfirst($cat->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Organisation Name</label>
                                    <input type="text" id="edit_job_organisation_name" name="organisation_name" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Location</label>
                                    <input type="text" id="edit_job_location" name="location" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Budget</label>
                                    <input type="number" min="0" step="0.01" id="edit_job_budget" name="budget" class="form-control" placeholder="Not specified">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Needed By</label>
                                    <input type="text" id="edit_needed_by" autocomplete="off" placeholder="Select date" name="needed_by" class="form-control">
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label mb-1">Job Description</label>
                                    <textarea id="edit_job_description" name="description" rows="6" class="form-control" placeholder="Tell suppliers what you need, quantities, delivery expectations, colors, sizes, or any special notes."></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div id="edit-dynamic-fields"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-4" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-primary rounded-4" id="toggleJobEditBtn">Edit Job</button>
                    <button type="button" class="btn btn-secondary rounded-4 d-none" id="cancelJobEditBtn">Cancel Edit</button>
                    <button type="button" class="btn btn-primary rounded-4 d-none" id="updateJobBtn">Update Job</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let createItemCount = 0;
    let editItemCount = 0;
    let currentJobPayload = null;

    function buildUrl(template, value) {
        return template.replace('__JOB_ID__', value).replace('__CATEGORY_ID__', value);
    }

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
                    ensureAddMoreButton(targetSelector, targetSelector === '#dynamic-fields' ? 'addMoreFieldBtn' : 'editAddMoreFieldBtn', buttonLabel);
                }

                if (targetSelector === '#dynamic-fields') {
                    createItemCount = itemIndex;
                } else {
                    editItemCount = itemIndex;
                }
            },
            error: function () {
                Swal.close();
                toastr.error('Unable to load dynamic fields.');
            }
        });
    }

    function decodeValue(value) {
        if (Array.isArray(value)) {
            return value;
        }

        if (value === null || value === undefined || value === '') {
            return [];
        }

        if (typeof value === 'string') {
            try {
                const parsed = JSON.parse(value);
                return Array.isArray(parsed) ? parsed : [parsed];
            } catch (error) {
                return [value];
            }
        }

        return [value];
    }

    function setModalMode(mode) {
        const viewSection = document.getElementById('jobViewSection');
        const editSection = document.getElementById('jobEditSection');
        const toggleBtn = document.getElementById('toggleJobEditBtn');
        const cancelBtn = document.getElementById('cancelJobEditBtn');
        const updateBtn = document.getElementById('updateJobBtn');

        if (mode === 'edit') {
            viewSection.classList.add('d-none');
            editSection.classList.remove('d-none');
            toggleBtn.classList.add('d-none');
            cancelBtn.classList.remove('d-none');
            updateBtn.classList.remove('d-none');
            return;
        }

        editSection.classList.add('d-none');
        viewSection.classList.remove('d-none');
        toggleBtn.classList.remove('d-none');
        cancelBtn.classList.add('d-none');
        updateBtn.classList.add('d-none');
    }

    function fillEditForm(job) {
        const form = document.getElementById('jobEditForm');
        form.dataset.action = "{{ route('customer-panel.jobs.update', ['job' => '__JOB_ID__']) }}".replace('__JOB_ID__', job.id);

        document.getElementById('edit_job_title').value = job.title || '';
        document.getElementById('edit_job_category').value = job.category || '';
        document.getElementById('edit_job_organisation_name').value = job.organisation_name || '';
        document.getElementById('edit_job_location').value = job.location || '';
        document.getElementById('edit_job_budget').value = job.budget || '';
        document.getElementById('edit_needed_by').value = job.needed_by || '';
        document.getElementById('edit_job_description').value = job.description || '';

        if (typeof flatpickr !== 'undefined') {
            const editNeededByInput = document.getElementById('edit_needed_by');
            if (editNeededByInput && editNeededByInput._flatpickr) {
                editNeededByInput._flatpickr.destroy();
            }

            flatpickr('#edit_needed_by', {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                time_24hr: false,
                minDate: 'today',
                disableMobile: true,
                defaultDate: job.needed_by || null
            });
        }

        $('#edit_job_category').trigger('change.select2');
    }

    async function loadJobPayload(jobId, mode = 'view') {
        Swal.fire({
            title: 'Loading job details...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        try {
            const response = await fetch("{{ route('customer-panel.jobs.edit', ['job' => '__JOB_ID__']) }}".replace('__JOB_ID__', jobId), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Unable to load job details.');
            }

            currentJobPayload = payload;

            document.getElementById('jobDetailsModalLabel').textContent = payload.job.title || 'Job details';
            document.getElementById('jobDetailsMeta').textContent = 'Job #' + String(payload.job.id).padStart(4, '0');
            document.getElementById('jobViewContent').innerHTML = payload.view_html || '';

            fillEditForm(payload.job);
            document.getElementById('edit-dynamic-fields').innerHTML = payload.edit_fields_html || '';
            editItemCount = payload.item_count || 1;

            setModalMode(mode);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('jobDetailsModal')).show();
        } finally {
            Swal.close();
        }
    }

    function renderCreateMode() {
        const form = document.getElementById('jobForm');
        const modalTitle = document.getElementById('postjobmodalLabel');
        const submitBtn = document.getElementById('jobSubmitBtn');
        const defaultValues = {
            organisation_name: @json(old('organisation_name', auth()->user()->customerProfile?->school_name)),
            location: @json(old('location', auth()->user()->customerProfile?->county))
        };

        modalTitle.textContent = 'Post a Job';
        submitBtn.textContent = 'Post Job';
        form.dataset.action = "{{ route('customer-panel.jobs.store') }}";
        form.reset();
        clearValidationErrors(form);
        document.getElementById('job_organisation_name').value = defaultValues.organisation_name || '';
        document.getElementById('job_location').value = defaultValues.location || '';
        document.getElementById('dynamic-fields').innerHTML = '';
        $('.add-more-item').remove();
        createItemCount = 0;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const createModalElement = document.getElementById('postjobmodal');
        const createModal = bootstrap.Modal.getOrCreateInstance(createModalElement);
        const createForm = document.getElementById('jobForm');
        const createBtn = document.getElementById('openCreateJobModal');
        const createSubmitBtn = document.getElementById('jobSubmitBtn');
        const csrfToken = "{{ csrf_token() }}";
        const storeUrl = "{{ route('customer-panel.jobs.store') }}";
        const jobDetailsModalElement = document.getElementById('jobDetailsModal');
        const jobDetailsModal = bootstrap.Modal.getOrCreateInstance(jobDetailsModalElement);
        const jobEditForm = document.getElementById('jobEditForm');
        const editCategory = document.getElementById('edit_job_category');
        const toggleEditBtn = document.getElementById('toggleJobEditBtn');
        const cancelEditBtn = document.getElementById('cancelJobEditBtn');
        const updateBtn = document.getElementById('updateJobBtn');
        const createDefaultValues = {
            organisation_name: @json(old('organisation_name', auth()->user()->customerProfile?->school_name)),
            location: @json(old('location', auth()->user()->customerProfile?->county))
        };

        function clearFormErrors(form) {
            clearValidationErrors(form);
        }

        function setMethodSpoof(form, method) {
            let methodInput = form.querySelector('input[name="_method"]');

            if (!methodInput && method) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }

            if (methodInput) {
                if (method) {
                    methodInput.value = method;
                } else {
                    methodInput.remove();
                }
            }
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

        function setCreateMode() {
            document.getElementById('postjobmodalLabel').textContent = 'Post a Job';
            createSubmitBtn.textContent = 'Post Job';
            createForm.dataset.action = storeUrl;
            setMethodSpoof(createForm, null);
            createForm.reset();
            clearFormErrors(createForm);
            $('#job_category').val('').trigger('change.select2');
            document.getElementById('job_organisation_name').value = createDefaultValues.organisation_name || '';
            document.getElementById('job_location').value = createDefaultValues.location || '';
            document.getElementById('dynamic-fields').innerHTML = '';
            $('.add-more-item').remove();
            createItemCount = 0;
        }

        function setEditMode() {
            setModalMode('edit');
        }

        function setViewMode() {
            setModalMode('view');
        }

        function refreshCreateDynamicFields(categoryId, isAddMore = false) {
            const itemIndex = isAddMore ? createItemCount + 1 : 1;
            loadCategoryFields(categoryId, '#dynamic-fields', itemIndex, isAddMore, 'Add More Item');
        }

        function refreshEditDynamicFields(categoryId, isAddMore = false) {
            const itemIndex = isAddMore ? editItemCount + 1 : 1;
            loadCategoryFields(categoryId, '#edit-dynamic-fields', itemIndex, isAddMore, 'Add More Item');
        }

        const createNeededByInput = document.getElementById('needed_by');
        if (createNeededByInput && typeof flatpickr !== 'undefined') {
            flatpickr('#needed_by', {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                time_24hr: false,
                minDate: 'today',
                disableMobile: true,
                defaultDate: createNeededByInput.value || null
            });
        }

        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#job_category').select2({
                dropdownParent: $('#postjobmodal'),
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
        }

        createBtn.addEventListener('click', setCreateMode);
        createModalElement.addEventListener('hidden.bs.modal', setCreateMode);

        $('#job_category').on('change', function () {
            const categoryId = $(this).val();

            if (categoryId) {
                refreshCreateDynamicFields(categoryId, false);
            } else {
                $('#dynamic-fields').html('');
                $('.add-more-item').remove();
                createItemCount = 0;
            }
        });

        $(document).on('click', '#addMoreFieldBtn', function () {
            const categoryId = $('#job_category').val();

            if (categoryId) {
                refreshCreateDynamicFields(categoryId, true);
            }
        });

        $(document).on('click', '.view-job-btn', function () {
            loadJobPayload($(this).data('job-id'), 'view').catch((error) => {
                toastr.error(error.message || 'Unable to load job details.');
            });
        });

        $(document).on('click', '.edit-job-btn', function () {
            loadJobPayload($(this).data('job-id'), 'edit').catch((error) => {
                toastr.error(error.message || 'Unable to load job details.');
            });
        });

        toggleEditBtn.addEventListener('click', function () {
            if (!currentJobPayload) {
                return;
            }

            setEditMode();
        });

        cancelEditBtn.addEventListener('click', function () {
            if (!currentJobPayload) {
                jobDetailsModal.hide();
                return;
            }

            setViewMode();
        });

        editCategory.addEventListener('change', function () {
            const categoryId = this.value;

            if (categoryId) {
                refreshEditDynamicFields(categoryId, false);
            } else {
                $('#edit-dynamic-fields').html('');
                $('.add-more-item').remove();
                editItemCount = 0;
            }
        });

        $(document).on('click', '#editAddMoreFieldBtn', function () {
            const categoryId = $('#edit_job_category').val();

            if (categoryId) {
                refreshEditDynamicFields(categoryId, true);
            }
        });

        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#edit_job_category').select2({
                dropdownParent: $('#jobDetailsModal'),
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
        }

        createForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearValidationErrors(createForm);

            Swal.fire({
                title: 'Please wait...',
                text: 'Submitting your job',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            try {
                const response = await fetch(createForm.dataset.action || storeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(createForm),
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    if (response.status === 422 && payload.errors) {
                        showValidationErrors(createForm, payload.errors);
                    } else {
                        toastr.error(payload.message || 'Something went wrong!');
                    }

                    Swal.close();
                    return;
                }

                createModal.hide();
                setCreateMode();
                Swal.close();

                await Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: payload.message || 'Job saved successfully.',
                    showConfirmButton: false,
                    timer: 1500
                });

                window.location.reload();
            } catch (error) {
                Swal.close();
                toastr.error('Something went wrong!');
            }
        });

        updateBtn.addEventListener('click', async function () {
            clearValidationErrors(jobEditForm);

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
                setMethodSpoof(jobEditForm, 'PATCH');

                const response = await fetch(jobEditForm.dataset.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(jobEditForm),
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    if (response.status === 422 && payload.errors) {
                        showValidationErrors(jobEditForm, payload.errors);
                    } else {
                        toastr.error(payload.message || 'Something went wrong!');
                    }

                    Swal.close();
                    return;
                }

                Swal.close();
                jobDetailsModal.hide();

                await Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: payload.message || 'Job updated successfully.',
                    showConfirmButton: false,
                    timer: 1500
                });

                window.location.reload();
            } catch (error) {
                Swal.close();
                toastr.error('Something went wrong!');
            }
        });

        jobDetailsModalElement.addEventListener('hidden.bs.modal', function () {
            currentJobPayload = null;
            setViewMode();
        });

        document.querySelectorAll('.delete-job-btn').forEach((button) => {
            button.addEventListener('click', async function () {
                const result = await Swal.fire({
                    title: 'Delete this job?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                });

                if (!result.isConfirmed) {
                    return;
                }

                Swal.fire({
                    title: 'Please wait...',
                    text: 'Deleting job',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                const deleteFormData = new FormData();
                deleteFormData.append('_token', csrfToken);
                deleteFormData.append('_method', 'DELETE');

                try {
                    const response = await fetch(this.dataset.deleteUrl, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: deleteFormData,
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        Swal.close();
                        toastr.error(payload.message || 'Unable to delete job.');
                        return;
                    }

                    Swal.close();
                    await Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: payload.message || 'Job deleted successfully.',
                    });

                    window.location.reload();
                } catch (error) {
                    Swal.close();
                    toastr.error('Unable to delete job.');
                }
            });
        });

        setCreateMode();
    });
</script>
@endpush

 
