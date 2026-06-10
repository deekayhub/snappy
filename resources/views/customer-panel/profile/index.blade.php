@extends('customer-panel.layouts.app')
@section('title', 'Customer Profile')

@php
    $supplierOrganisationIds = old('supplier_organisation', $user->organisationCategories->where('type', 'supplier')->pluck('id')->toArray());
    $currentProfilePicture = $user->profile_picture;
@endphp

@section('content')
<div class="content-wrapper p-3">
    {{-- <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-lg-5"> --}}
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                <div>
                    <h3 class="mb-1">Customer profile</h3>
                    <p class="text-muted mb-0">Edit the company details listed in your supplier requirements file.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="bg-white border rounded-5 shadow-sm p-4 p-lg-5 h-100">
                        {{-- <h3 class="mb-2">Profile details</h3>
                        <p class="text-secondary mb-4">Update the information connected to your customer account.</p> --}}

                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
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
                                    <select name="customer_organisation[]" class="form-select rounded-3 select2-single text-dark" required>
                                        <option value="">Select organisation</option>
                                        @foreach ($organisation as $item)
                                            @if ($item->type === 'customer')
                                                <option value="{{ $item->id }}"
                                                    {{ in_array($item->id, $user->organisationCategories->pluck('id')->toArray()) ? 'selected' : '' }}
                                                    >{{ strtoupper($item->name) }}</option>
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
                                @include('partials.profile-picture-field', [
                                    'currentProfilePicture' => $currentProfilePicture,
                                    'profilePictureLabel' => 'Profile picture',
                                ])
                                <div class="col-12 pt-2">
                                    <button class="btn btn-primary rounded-3 ">Save profile changes</button>
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
                                    <button class="btn btn-dark rounded-3 w-100">Update password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        {{-- </div>
    </div> --}}
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
@include('partials.profile-picture-script')
@endpush
@endsection
