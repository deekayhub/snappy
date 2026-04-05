@extends('supplier-panel.layouts.app')
@section('title', 'Supplier Profile')

@php
    $supplierOrganisationIds = old('supplier_organisation', $user->organisationCategories->where('type', 'supplier')->pluck('id')->toArray());
@endphp

@section('content')
<div class="content-wrapper p-3">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                <div>
                    <h3 class="mb-1">Supplier profile</h3>
                    <p class="text-muted mb-0">Edit the company details listed in your supplier requirements file.</p>
                </div>
                <span class="badge bg-light text-dark px-3 py-2 rounded-4">Free supplier account</span>
            </div>

            @if (session('success'))
                <div class="alert alert-success rounded-4">{{ session('success') }}</div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="alert alert-success rounded-4">Password updated successfully.</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger rounded-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-xl-8">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control  rounded-4" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control  rounded-4 bg-light" value="{{ $user->email }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone no.</label>
                                <input type="text" name="phone" class="form-control  rounded-4" value="{{ old('phone', $user->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company name</label>
                                <input type="text" name="company_name" class="form-control  rounded-4" value="{{ old('company_name', $user->supplierProfile?->company_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website address</label>
                                <input type="url" name="website" class="form-control  rounded-4" value="{{ old('website', $user->supplierProfile?->website) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Social link</label>
                                <input type="url" name="social_link" class="form-control  rounded-4" value="{{ old('social_link', $user->supplierProfile?->social_link) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Review site link</label>
                                <input type="url" name="review_link" class="form-control  rounded-4" value="{{ old('review_link', $user->supplierProfile?->review_link) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Which service do you provide?</label>
                                <select name="supplier_organisation[]" class="form-select form-select-lg rounded-4 supplier-organisations" multiple required>
                                    @foreach ($organisation as $item)
                                        <option value="{{ $item->id }}" {{ in_array($item->id, $supplierOrganisationIds) ? 'selected' : '' }}>{{ strtoupper($item->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Company address</label>
                                <textarea name="address" rows="4" class="form-control rounded-4" required>{{ old('address', $user->supplierProfile?->address) }}</textarea>
                            </div>
                            <div class="col-12 pt-2">
                                <button class="btn btn-primary rounded-4 px-4">Save supplier profile</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-xl-4">
                    <div class="border rounded-4 p-4 mb-4">
                        <h5 class="mb-2">Quote access</h5>
                        <p class="text-muted mb-0">Free suppliers can browse the job board. Quote submission is visually reserved but still disabled until subscription and quote tables are added.</p>
                    </div>
                    <div class="border rounded-4 p-4">
                        <h5 class="mb-3">Change password</h5>
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Current password</label>
                                <input type="password" name="current_password" class="form-control rounded-4" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New password</label>
                                <input type="password" name="password" class="form-control rounded-4" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm new password</label>
                                <input type="password" name="password_confirmation" class="form-control rounded-4" required>
                            </div>
                            <button class="btn btn-dark rounded-4 w-100">Update password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(function () {
        $('.supplier-organisations').select2({
            // theme: 'bootstrap-5',
            
            placeholder: 'Select supplier categories',
            width: '100%'
        });
    });
</script>
@endpush
@endsection
