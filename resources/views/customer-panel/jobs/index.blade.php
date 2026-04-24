@extends('customer-panel.layouts.app')
@section('title', 'My Jobs')

@section('content')
<div class="content-wrapper p-3">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h3 class="mb-1">My posted jobs</h3>
            <p class="text-muted mb-0">Create one clear request with multiple items, supplier preferences, and delivery details.</p>
        </div>
        <button id="openCreateJobModal" type="button" class="btn btn-primary rounded-4" data-bs-toggle="modal" data-bs-target="#postjobmodal">
            Post New Job
        </button>
    </div>

    @forelse ($jobs as $job)
        @php
            $itemNames = $job->jobItems->pluck('item_name')->take(3)->implode(', ');
            $itemCount = $job->jobItems->count();
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <div class="small text-muted">Job #{{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                            <span class="badge bg-{{ $job->status === 'open' ? 'success' : 'secondary' }}">
                                {{ ucfirst($job->status) }}
                            </span>
                            <span class="badge bg-light text-dark border">{{ $itemCount }} item{{ $itemCount === 1 ? '' : 's' }}</span>
                            <span class="badge bg-light text-dark border">{{ $job->delivery_in_uk ? 'UK delivery' : 'Outside UK' }}</span>
                            @if ($job->personalisation_required)
                                <span class="badge bg-warning text-dark">
                                    Personalisation {{ $job->personalisation_mode === 'different' ? 'different' : 'same' }}
                                </span>
                            @endif
                        </div>
                        <h4 class="mb-2">{{ $job->title }}</h4>
                        <div class="text-muted mb-2">{{ $job->category ?: 'General' }} | {{ $job->location ?: 'No location set' }}</div>
                        <p class="mb-0 text-muted">
                            {{ $job->notes ? \Illuminate\Support\Str::limit($job->notes, 180) : \Illuminate\Support\Str::limit($job->description, 180) }}
                        </p>
                        @if ($itemNames)
                            <div class="mt-3 small text-muted">
                                Items: <span class="fw-semibold text-dark">{{ $itemNames }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="text-lg-end">
                        <div class="d-flex justify-content-lg-end gap-2 mb-2">
                            <button
                                type="button"
                                class="btn btn-sm btn-light border rounded-circle edit-job-btn"
                                title="Edit"
                                data-edit-url="{{ route('customer-panel.jobs.edit', $job->id) }}"
                            >
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

                        <div class="small text-muted mt-2">
                            Needed by: {{ $job->needed_by?->format('d M Y h:i A') ?? 'Not set' }}
                        </div>
                        <div class="small text-muted">
                            Budget: {{ $job->budget ? 'GBP '.number_format((float) $job->budget, 2) : 'Not shared' }}
                        </div>
                        <div class="small text-muted">
                            Suppliers: {{ $job->supplier_target_type === 'count' ? 'Top '.$job->supplier_target_count : 'All registered suppliers' }}
                        </div>
                        <div class="fw-semibold mt-2">
                            {{ $job->quotes_count }} supplier quote{{ $job->quotes_count === 1 ? '' : 's' }}
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form id="jobForm" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <div>
                            {{-- <div class="small text-uppercase text-muted mb-1">Post a job</div> --}}
                            <h2 class="modal-title fs-4 mb-0" id="postjobmodalLabel">Post a Job</h2>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body pt-4">
                        <div class="row g-4">
                            {{-- <div class="col-12">
                                <div class="border rounded-4 p-3 bg-light">
                                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                                        <div>
                                            <div class="fw-semibold">A simple 4-step request</div>
                                            <div class="small text-muted">Add your items, set the delivery and supplier rules, then add any helpful notes.</div>
                                        </div>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <span class="badge rounded-pill bg-primary">1 Items</span>
                                            <span class="badge rounded-pill bg-secondary">2 Order details</span>
                                            <span class="badge rounded-pill bg-info text-dark">3 Preferences</span>
                                            <span class="badge rounded-pill bg-dark">4 Notes</span>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="col-12">
                                {{-- <div class="border rounded-4 p-4"> --}}
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            {{-- <div class="badge bg-primary rounded-pill mb-2">Step 1</div> --}}
                                            <h5 class="mb-1">What are you looking for?</h5>
                                            <p class="text-muted mb-0">Add one or more items to the same job request.</p>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary rounded-4" id="addJobItemBtn">
                                            Add another item
                                        </button>
                                    </div>

                                    <div id="jobItemsContainer" class="vstack gap-3"></div>
                                {{-- </div> --}}
                            </div>
                        </div>
                        <div class="border rounded-4 p-4 mt-4">
                            <div class="row g-4">
    
                                <div class="col-lg-4">
                                    <label class="form-label mb-1">Job headline</label>
                                    <input type="text" id="job_title" name="title" value="{{ old('title') }}" class="text-dark form-control rounded-4" placeholder="Auto-generated from your first item">
                                    <div class="small text-muted mt-1">If left blank, we will create a title from the first item.</div>
                                </div>
    
                                <div class="col-lg-4">
                                    <label class="form-label mb-1">Suggested category</label>
                                    <select name="category" id="job_category" class="form-select rounded-4 text-dark">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->name }}">{{ ucfirst($cat->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
    
                                <div class="col-lg-4">
                                    <label class="form-label mb-1">Organisation name</label>
                                    <input type="text" id="job_organisation_name" name="organisation_name" value="{{ old('organisation_name', auth()->user()->customerProfile?->school_name) }}" class="form-control rounded-4">
                                </div>
    
                                <div class="col-lg-6">
                                    <label class="form-label mb-1">Location</label>
                                    <input type="text" id="job_location" name="location" value="{{ old('location', auth()->user()->customerProfile?->county) }}" class="form-control rounded-4">
                                </div>
    
                                <div class="col-lg-6">
                                    <label class="form-label mb-1">Please supply a date you need to receive this order by</label>
                                    <input type="text" id="needed_by" autocomplete="off" placeholder="Select date and time" name="needed_by" value="{{ old('needed_by') }}" class="form-control rounded-4">
                                </div>
    
                                <div class="col-lg-6">
                                    <label class="form-label mb-1">How many suppliers do you wish to send this job to?</label>
                                    <select name="supplier_target_type" id="supplier_target_type" class="form-select rounded-4 text-dark">
                                        <option value="all">Select all registered suppliers</option>
                                        <option value="count">Enter a number</option>
                                    </select>
                                </div>
    
                                <div class="col-lg-6" id="supplier_target_count_wrap">
                                    <label class="form-label mb-1">Supplier count</label>
                                    <input type="number" min="1" step="1" id="supplier_target_count" name="supplier_target_count" value="{{ old('supplier_target_count') }}" class="form-control rounded-4" placeholder="Example: 10">
                                </div>
    
                                <div class="col-lg-6">
                                    <label class="form-label mb-1">Is the delivery in the UK?</label>
                                    <select name="delivery_in_uk" id="delivery_in_uk" class="form-select rounded-4 text-dark">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
    
                                <div class="col-lg-6">
                                    <label class="form-label mb-1">Do you require personalisation on these items?</label>
                                    <select name="personalisation_required" id="personalisation_required" class="form-select rounded-4 text-dark">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
    
                                <div class="col-12" id="personalisation_mode_wrap">
                                    <div class="border rounded-4 p-3 bg-light">
                                        <label class="form-label mb-1">If yes, is the personalisation all the same or different?</label>
                                        <select name="personalisation_mode" id="personalisation_mode" class="form-select rounded-4 text-dark">
                                            <option value="same">All the same</option>
                                            <option value="different">Different</option>
                                        </select>
                                    </div>
                                </div>
    
                            </div>
                            
                        </div>
                        <div class="row g-4 mt-2">
                            <div class="col-12">
                                <div class="border rounded-4 p-4">
                                    {{-- <div class="badge bg-dark rounded-pill mb-2">Step 4</div> --}}
                                    <h5 class="mb-1">Budget and notes</h5>
                                    <p class="text-muted mb-3">Leave the budget blank if you do not have one yet.</p>

                                    <div class="row g-3">
                                        <div class="col-lg-4">
                                            <label class="form-label mb-1">Budget</label>
                                            <input type="number" min="0" step="0.01" id="job_budget" name="budget" value="{{ old('budget') }}" class="form-control rounded-4" placeholder="Not Specified">
                                        </div>
                                        <div class="col-lg-8">
                                            <label class="form-label mb-1">Notes for the supplier</label>
                                            <textarea id="job_notes" name="notes" rows="5" class="form-control rounded-4" placeholder="Please add any notes below that might be helpful to the supplier. Be as detailed as possible.">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>                               

                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary px-4 py-2 rounded-4" data-bs-dismiss="modal">Close</button>
                        <button id="jobSubmitBtn" type="submit" class="btn btn-primary px-4 py-2 rounded-4">Post Job</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<template id="jobItemTemplate">
    <div class="card border rounded-4 job-item-card" data-job-item>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <div class="badge bg-light text-dark border rounded-pill mb-2">Item <span class="job-item-number">1</span></div>
                    <h6 class="mb-0">Item details</h6>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-4 js-remove-item">Remove</button>
            </div>

            <input type="hidden" class="js-item-id">

            <div class="row g-3">
                <div class="col-lg-8">
                    <label class="form-label mb-1">What are you looking for?</label>
                    <input type="text" class="form-control rounded-4 js-item-name" placeholder="Trophies, medals, glass awards">
                </div>
                <div class="col-lg-4">
                    <label class="form-label mb-1">How many do you require?</label>
                    <input type="number" min="1" step="1" class="form-control rounded-4 js-item-quantity" placeholder="Example: 100">
                </div>
                <div class="col-12">
                    <label class="form-label mb-1">SKU / item codes</label>
                    <textarea rows="2" class="form-control rounded-4 js-item-sku" placeholder="Add one code per line or separate with commas."></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label mb-1">Upload images</label>
                    <input type="file" class="form-control rounded-4 js-item-images" accept="image/*" multiple>
                    <div class="small text-muted mt-1">Upload one or more images, or share a link below instead.</div>
                    <div class="js-existing-images mt-2 d-flex flex-wrap gap-2"></div>
                </div>
                <div class="col-12">
                    <label class="form-label mb-1">Link to the item you have seen somewhere else</label>
                    <input type="url" class="form-control rounded-4 js-item-link" placeholder="https://...">
                </div>
                <div class="col-12">
                    <label class="form-label mb-1">If the supplier does not supply this item, quote on something similar?</label>
                    <select class="form-select rounded-4 js-item-similar">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>
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
    const itemsContainer = document.getElementById('jobItemsContainer');
    const itemTemplate = document.getElementById('jobItemTemplate');
    const addItemBtn = document.getElementById('addJobItemBtn');
    const personalisationRequired = document.getElementById('personalisation_required');
    const personalisationModeWrap = document.getElementById('personalisation_mode_wrap');
    const supplierTargetType = document.getElementById('supplier_target_type');
    const supplierTargetCountWrap = document.getElementById('supplier_target_count_wrap');
    let neededByPicker = null;

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

    function fieldNameFromKey(key) {
        const parts = key.split('.');
        let result = parts[0] || '';
        for (let i = 1; i < parts.length; i += 1) {
            result += '[' + parts[i] + ']';
        }
        return result;
    }

    function toggleSupplierCount() {
        supplierTargetCountWrap.classList.toggle('d-none', supplierTargetType.value !== 'count');
    }

    function togglePersonalisationMode() {
        personalisationModeWrap.classList.toggle('d-none', personalisationRequired.value !== '1');
    }

    function updateItemNumbers() {
        const rows = itemsContainer.querySelectorAll('[data-job-item]');
        rows.forEach((row, index) => {
            row.querySelector('.job-item-number').textContent = index + 1;
            const removeBtn = row.querySelector('.js-remove-item');
            removeBtn.disabled = rows.length === 1;
        });
    }

    function renderExistingImages(container, existingPaths) {
        const previewWrap = document.createElement('div');
        previewWrap.className = 'd-flex flex-wrap gap-2 mt-2';

        (existingPaths || []).forEach((path) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'd-inline-flex align-items-center gap-2 border rounded-3 p-2 bg-white';
            wrapper.innerHTML = '<img src="/storage/' + path + '" alt="Existing image" style="width:54px;height:54px;object-fit:cover;border-radius:8px;">' +
                '<div class="small text-muted">Existing image</div>';
            previewWrap.appendChild(wrapper);
        });

        container.appendChild(previewWrap);
    }

    function createItemRow(data = {}) {
        const fragment = itemTemplate.content.cloneNode(true);
        const row = fragment.querySelector('[data-job-item]');
        const itemId = row.querySelector('.js-item-id');
        const itemName = row.querySelector('.js-item-name');
        const quantity = row.querySelector('.js-item-quantity');
        const sku = row.querySelector('.js-item-sku');
        const images = row.querySelector('.js-item-images');
        const link = row.querySelector('.js-item-link');
        const similar = row.querySelector('.js-item-similar');
        const existingWrap = row.querySelector('.js-existing-images');

        itemId.value = data.id || '';
        itemName.value = data.item_name || '';
        quantity.value = data.quantity || 1;
        sku.value = data.sku_codes || '';
        link.value = data.item_link || '';
        similar.value = data.allow_similar_quote ? '1' : '0';

        if (data.image_paths && data.image_paths.length) {
            data.image_paths.forEach((path) => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = '';
                hidden.value = path;
                hidden.className = 'js-existing-image-path';
                existingWrap.appendChild(hidden);
            });
            renderExistingImages(existingWrap, data.image_paths);
        }

        images.name = 'items[' + itemsContainer.children.length + '][images][]';

        row.querySelector('.js-remove-item').addEventListener('click', function () {
            if (itemsContainer.children.length > 1) {
                row.remove();
                refreshItemInputNames();
                updateItemNumbers();
            }
        });

        itemsContainer.appendChild(fragment);
        refreshItemInputNames();
        updateItemNumbers();
    }

    function refreshItemInputNames() {
        const rows = itemsContainer.querySelectorAll('[data-job-item]');
        rows.forEach((row, index) => {
            row.querySelector('.js-item-id').name = 'items[' + index + '][id]';
            row.querySelector('.js-item-name').name = 'items[' + index + '][item_name]';
            row.querySelector('.js-item-quantity').name = 'items[' + index + '][quantity]';
            row.querySelector('.js-item-sku').name = 'items[' + index + '][sku_codes]';
            row.querySelector('.js-item-images').name = 'items[' + index + '][images][]';
            row.querySelector('.js-item-link').name = 'items[' + index + '][item_link]';
            row.querySelector('.js-item-similar').name = 'items[' + index + '][allow_similar_quote]';

            const existingWrap = row.querySelector('.js-existing-images');
            existingWrap.querySelectorAll('.js-existing-image-path').forEach((input) => {
                input.name = 'items[' + index + '][existing_image_paths][]';
            });
        });
    }

    function resetItems(data = []) {
        itemsContainer.innerHTML = '';
        if (!data.length) {
            createItemRow();
            return;
        }

        data.forEach((item) => createItemRow(item));
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
        document.getElementById('delivery_in_uk').value = '1';
        document.getElementById('personalisation_required').value = '0';
        document.getElementById('personalisation_mode').value = 'same';
        document.getElementById('supplier_target_type').value = 'all';
        document.getElementById('supplier_target_count').value = '';
        document.getElementById('job_title').value = '';
        document.getElementById('job_budget').value = '';
        document.getElementById('job_notes').value = '';
        toggleSupplierCount();
        togglePersonalisationMode();
        resetItems();

        if (neededByPicker) {
            neededByPicker.setDate(new Date(), true);
        }
    }

    function setEditMode(job) {
        modalTitle.textContent = 'Edit Job';
        submitBtn.textContent = 'Update Job';
        form.dataset.action = "{{ route('customer-panel.jobs.update', 'job') }}".replace('job', job.id);
        setMethodSpoof('PATCH');
        clearValidationErrors();

        document.getElementById('job_title').value = job.title || '';
        document.getElementById('job_category').value = job.category || '';
        document.getElementById('job_organisation_name').value = job.organisation_name || '';
        document.getElementById('job_location').value = job.location || '';
        document.getElementById('job_budget').value = job.budget || '';
        document.getElementById('delivery_in_uk').value = job.delivery_in_uk ? '1' : '0';
        document.getElementById('personalisation_required').value = job.personalisation_required ? '1' : '0';
        document.getElementById('personalisation_mode').value = job.personalisation_mode || 'same';
        document.getElementById('supplier_target_type').value = job.supplier_target_type || 'all';
        document.getElementById('supplier_target_count').value = job.supplier_target_count || '';
        document.getElementById('job_notes').value = job.notes || '';

        if (neededByPicker) {
            neededByPicker.setDate(job.needed_by || '', true, 'Y-m-d H:i');
        } else {
            document.getElementById('needed_by').value = job.needed_by || '';
        }

        toggleSupplierCount();
        togglePersonalisationMode();
        resetItems(job.items || []);
    }

    function applyValidationErrors(errors) {
        Object.keys(errors).forEach((field) => {
            const input = form.querySelector('[name="' + fieldNameFromKey(field) + '"]');
            if (input) {
                input.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.innerText = errors[field][0];
                input.parentNode.appendChild(feedback);
            }
            toastr.error(errors[field][0]);
        });
    }

    const neededByInput = document.getElementById('needed_by');
    if (neededByInput && typeof flatpickr !== 'undefined') {
        neededByPicker = flatpickr('#needed_by', {
            enableTime: true,
            time_24hr: true,
            dateFormat: 'Y-m-d H:i',
            minDate: 'today',
            disableMobile: true,
            defaultDate: neededByInput.value || new Date()
        });
    }

    addItemBtn.addEventListener('click', function () {
        createItemRow();
    });

    personalisationRequired.addEventListener('change', togglePersonalisationMode);
    supplierTargetType.addEventListener('change', toggleSupplierCount);

    createBtn.addEventListener('click', setCreateMode);
    jobModalElement.addEventListener('hidden.bs.modal', setCreateMode);

    document.querySelectorAll('.edit-job-btn').forEach((button) => {
        button.addEventListener('click', async function () {
            clearValidationErrors();

            try {
                const response = await fetch(this.dataset.editUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                const payload = await response.json();

                if (!response.ok || !payload.success) {
                    toastr.error(payload.message || 'Unable to load job details.');
                    return;
                }

                setEditMode(payload.job);
                jobModal.show();
            } catch (error) {
                toastr.error('Unable to load job details.');
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
                    applyValidationErrors(payload.errors);
                } else {
                    toastr.error(payload.message || 'Something went wrong!');
                }

                Swal.close();
                return;
            }

            jobModal.hide();
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
