
@extends('layouts.app')
@section('title', 'My Profile')

@php
    $customerOrganisationIds = old('customer_organisation', $user->organisationCategories->where('type', 'customer')->pluck('id')->toArray());
@endphp

@section('section')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-12">
            <div class="p-4 p-lg-5 rounded-5 border shadow-sm" style="background: linear-gradient(155deg, rgba(15, 23, 42, 1) 34%, rgba(2, 132, 199, 1) 77%);">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge rounded-pill text-bg-light px-3 py-2">Customer account</span>
                        <h1 class="mt-3 mb-2 text-white">Manage your profile and password</h1>
                        <p class="text-white mb-0">Keep your contact details up to date and change your password from one modern account screen.</p>
                        <a href="{{ route('customer.quotes.index') }}" class="btn btn-outline-primary rounded-4 mt-3 px-4 py-2">Open quote inbox</a>
                    </div>
                    <div class="col-lg-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="bg-white border rounded-4 p-3 h-100">
                                    <div class="small text-secondary">County</div>
                                    <div class="fw-semibold">{{ $user->customerProfile?->county ?: 'Not set' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-white border rounded-4 p-3 h-100">
                                    <div class="small text-secondary">Organisation</div>
                                    <div class="fw-semibold">{{ count($customerOrganisationIds) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="col-12">
                <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="col-12">
                <div class="alert alert-success rounded-4 border-0 shadow-sm">Password updated successfully.</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="col-12">
                <div class="alert alert-danger rounded-4 border-0 shadow-sm">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="col-lg-7">
            <div class="bg-white border rounded-5 shadow-sm p-4 p-lg-5 h-100">
                <h3 class="mb-2">Profile details</h3>
                <p class="text-secondary mb-4">Update the information connected to your customer account.</p>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full name</label>
                            <input name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email address</label>
                            <input class="form-control rounded-3 bg-light" value="{{ $user->email }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone number</label>
                            <input name="phone" class="form-control rounded-3" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Organisation type</label>
                            <select name="customer_organisation[]" class="form-select rounded-3 select2-single" required>
                                <option value="">Select organisation</option>
                                @foreach ($organisation as $item)
                                    @if ($item->type === 'customer')
                                        <option value="{{ $item->id }}" {{ in_array($item->id, $customerOrganisationIds) ? 'selected' : '' }}>{{ strtoupper($item->name) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">County</label>
                            <input name="county" class="form-control rounded-3" value="{{ old('county', optional($user->customerProfile)->county) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Club / School name</label>
                            <input name="school_name" class="form-control rounded-3" value="{{ old('school_name', optional($user->customerProfile)->school_name) }}">
                        </div>
                        <div class="col-12 pt-2">
                            <button class="btn btn-primary rounded-3 py-2">Save profile changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="bg-white border rounded-5 shadow-sm p-4 p-lg-5 h-100">
                <h3 class="mb-2">Change password</h3>
                <p class="text-secondary mb-4">Use a fresh strong password to keep your account secure.</p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Current password</label>
                            <input type="password" name="current_password" class="form-control rounded-3" required>
                            @error('current_password', 'updatePassword')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">New password</label>
                            <input type="password" name="password" class="form-control rounded-3" required>
                            @error('password', 'updatePassword')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Confirm new password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12 pt-2">
                            <button class="btn btn-dark rounded-3 w-100 py-2">Update password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.select2-single').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select organisation',
            allowClear: false,
            width: '100%'
        });
    });
</script>
@endsection
