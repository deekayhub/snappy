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

    @forelse ($jobs as $job)
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div>
                        <div class="small text-muted mb-1">Job #{{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <h4 class="mb-1">{{ $job->title }}</h4>
                        <div class="text-muted mb-2">{{ $job->category ?: 'General' }} | {{ $job->location ?: 'No location set' }}</div>
                        <p class="mb-0 text-muted">{{ \Illuminate\Support\Str::limit($job->description, 200) }}</p>
                    </div>
                    <div class="text-lg-end">
                        <div class="d-flex justify-content-lg-end gap-2 mb-2">
                            <button
                                type="button"
                                class="btn btn-sm btn-light border rounded-circle edit-job-btn"
                                title="Edit"
                                data-bs-toggle="modal"
                                data-bs-target="#postjobmodal"
                                data-update-url="{{ route('customer-panel.jobs.update', $job->id) }}"
                                data-title="{{ $job->title }}"
                                data-category="{{ $job->category }}"
                                data-organisation_name="{{ $job->organisation_name }}"
                                data-location="{{ $job->location }}"
                                data-budget="{{ $job->budget }}"
                                data-needed_by="{{ $job->needed_by?->format('Y-m-d') }}"
                                data-description="{{ $job->description }}">
                                <i class="fa fa-pencil"></i>
                            </button>

                            <button
                                type="button"
                                class="btn btn-sm btn-light border rounded-circle text-danger delete-job-btn"
                                title="Delete"
                                data-delete-url="{{ route('customer-panel.jobs.destroy', $job->id) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>

                        <span class="badge bg-{{ $job->status === 'open' ? 'success' : 'secondary' }}">
                            {{ ucfirst($job->status) }}
                        </span>
                        <div class="small text-muted mt-2">
                            Needed by: {{ $job->needed_by?->format('d M Y') ?? 'Not set' }}
                        </div>
                        <div class="small text-muted">
                            Budget: {{ $job->budget ? '£ '.number_format((float) $job->budget, 2) : 'Not shared' }}
                        </div>
                        <div class="fw-semibold mt-2">
                            {{ $job->quotes_count }} supplier quotes
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-light border rounded-4">No jobs posted yet.</div>
    @endforelse

    <div class="mt-4">
        {{ $jobs->links() }}
    </div>

    <div class="modal fade" id="postjobmodal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="postjobmodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="jobForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="postjobmodalLabel">Post a Job</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label mb-1">Job Title</label>
                                <input type="text" id="job_title" name="title" value="{{ old('title') }}" class="form-control" placeholder="Need football kits for school team">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label mb-1">Category</label>
                                <select name="category" id="job_category" class="form-select">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->name }}">{{ ucfirst($cat->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label mb-1">Organisation Name</label>
                                <input type="text" id="job_organisation_name" name="organisation_name" value="{{ old('organisation_name', auth()->user()->customerProfile?->school_name) }}" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label mb-1">Location</label>
                                <input type="text" id="job_location" name="location" value="{{ old('location', auth()->user()->customerProfile?->county) }}" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label mb-1">Budget</label>
                                <input type="number" min="0" step="0.01" id="job_budget" name="budget" value="{{ old('budget') }}" class="form-control" placeholder="Not specified">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label mb-1">Needed By</label>
                                <input type="text" id="needed_by" autocomplete="off" placeholder="Select date" name="needed_by" value="{{ old('needed_by') }}" class="form-control">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label mb-1">Job Description</label>
                                <textarea id="job_description" name="description" rows="6" class="form-control" placeholder="Tell suppliers what you need, quantities, delivery expectations, colors, sizes, or any special notes.">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary px-5 py-2 rounded-4" data-bs-dismiss="modal">Close</button>
                        <button id="jobSubmitBtn" type="submit" class="btn btn-primary px-5 py-2 rounded-4">
                            Post Job
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const jobModalElement = document.getElementById('postjobmodal');
    const jobModal = bootstrap.Modal.getOrCreateInstance(jobModalElement);
    const form = document.getElementById('jobForm');
    const submitBtn = document.getElementById('jobSubmitBtn');
    const modalTitle = document.getElementById('postjobmodalLabel');
    const createBtn = document.getElementById('openCreateJobModal');
    const storeUrl = "{{ route('customer-panel.jobs.store') }}";
    const csrfToken = "{{ csrf_token() }}";
    const defaultValues = {
        organisation_name: @json(old('organisation_name', auth()->user()->customerProfile?->school_name)),
        location: @json(old('location', auth()->user()->customerProfile?->county))
    };

    function clearValidationErrors() {
        form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach((el) => el.remove());
    }

    function setMethodSpoof(method) {
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

    function setCreateMode() {
        modalTitle.textContent = 'Post a Job';
        submitBtn.textContent = 'Post Job';
        form.dataset.action = storeUrl;
        setMethodSpoof(null);
        form.reset();
        clearValidationErrors();
        document.getElementById('job_organisation_name').value = defaultValues.organisation_name || '';
        document.getElementById('job_location').value = defaultValues.location || '';
    }

    function setEditMode(button) {
        modalTitle.textContent = 'Edit Job';
        submitBtn.textContent = 'Update Job';
        form.dataset.action = "{{ route('customer-panel.jobs.update', ['job' => '__JOB_ID__']) }}".replace('__JOB_ID__', job.id);
        setMethodSpoof('PATCH');
        clearValidationErrors();
        document.getElementById('job_title').value = button.dataset.title || '';
        document.getElementById('job_category').value = button.dataset.category || '';
        document.getElementById('job_organisation_name').value = button.dataset.organisation_name || '';
        document.getElementById('job_location').value = button.dataset.location || '';
        document.getElementById('job_budget').value = button.dataset.budget || '';
        document.getElementById('needed_by').value = button.dataset.needed_by || '';
        document.getElementById('job_description').value = button.dataset.description || '';
    }

    const neededByInput = document.getElementById('needed_by');
    if (neededByInput && typeof flatpickr !== 'undefined') {
        flatpickr('#needed_by', {
            dateFormat: 'Y-m-d',
            minDate: 'today',
            disableMobile: true,
            defaultDate: neededByInput.value || null
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
    jobModalElement.addEventListener('hidden.bs.modal', setCreateMode);

    document.querySelectorAll('.edit-job-btn').forEach((button) => {
        button.addEventListener('click', function () {
            setEditMode(this);
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#job_category').trigger('change.select2');
            }
        });
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearValidationErrors();

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
            const response = await fetch(form.dataset.action || storeUrl, {
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
                    let firstError = null;
                    Object.keys(payload.errors).forEach((field) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = document.createElement('div');
                            feedback.classList.add('invalid-feedback');
                            feedback.innerText = payload.errors[field][0];
                            input.parentNode.appendChild(feedback);
                            if (!firstError) {
                                firstError = input;
                            }
                        }
                        toastr.error(payload.errors[field][0]);
                    });

                    if (firstError) {
                        firstError.focus();
                    }
                } else {
                    toastr.error(payload.message || 'Something went wrong!');
                }

                Swal.close();
                return;
            }

            jobModal.hide();
            form.reset();
            setCreateMode();
            Swal.close();

            await Swal.fire({
                icon: 'success',
                title: 'Success',
                text: payload.message || 'Job saved successfully.',
                confirmButtonText: 'OK',
            });

            window.location.reload();
        } catch (error) {
            Swal.close();
            toastr.error('Something went wrong!');
        }
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
