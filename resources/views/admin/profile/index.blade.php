@extends('admin.layouts.app')
@section('title', 'Profile')

@php
    $isSupplier = $user->hasRole('supplier');
    $supplierOrganisationIds = old('supplier_organisation', $user->organisationCategories->where('type', 'supplier')->pluck('id')->toArray());
@endphp

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card border-0 shadow-sm" style="border-radius: 24px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="p-4 p-lg-5 text-white" style="background: linear-gradient(135deg, #123c74, #0f766e);">
                        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-inline-flex align-items-center justify-content-center fw-bold" style="width: 80px; height: 80px; border-radius: 22px; background: rgba(255,255,255,0.18); font-size: 2rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="badge rounded-pill text-bg-light px-3 py-2">{{ $isSupplier ? 'Supplier panel' : 'Admin panel' }}</span>
                                    <h1 class="h3 mt-3 mb-2 text-white">Account profile</h1>
                                    <p class="mb-0 text-white-50">Manage your account details and update your password from one place.</p>
                                </div>
                            </div>
                            <div class="row g-3 w-100" style="max-width: 460px;">
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-4 h-100" style="background: rgba(255,255,255,0.12);">
                                        <div class="small text-white-50">Email</div>
                                        <div class="fw-semibold">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-4 h-100" style="background: rgba(255,255,255,0.12);">
                                        <div class="small text-white-50">Phone</div>
                                        <div class="fw-semibold">{{ $user->phone ?: 'Not added yet' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 p-lg-5">
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
                            <div class="col-xl-7">
                                <div class="border rounded-4 p-4 h-100">
                                    <div class="mb-4">
                                        <h4 class="mb-2">Profile information</h4>
                                        <p class="text-muted mb-0">{{ $isSupplier ? 'Keep your supplier details up to date so your panel reflects the latest business information.' : 'Update your admin account details.' }}</p>
                                    </div>

                                    <form method="POST" action="{{ route('profile.update') }}">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Full name</label>
                                                <input type="text" name="name" class="form-control form-control-lg rounded-4" value="{{ old('name', $user->name) }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email address</label>
                                                <input type="text" class="form-control form-control-lg rounded-4 bg-light" value="{{ $user->email }}" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone number</label>
                                                <input type="text" name="phone" class="form-control form-control-lg rounded-4" value="{{ old('phone', $user->phone) }}">
                                            </div>

                                            @if ($isSupplier)
                                                <div class="col-md-6">
                                                    <label class="form-label">Company name</label>
                                                    <input type="text" name="company_name" class="form-control form-control-lg rounded-4" value="{{ old('company_name', optional($user->supplierProfile)->company_name) }}" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Supplier organisation</label>
                                                    <select name="supplier_organisation[]" class="form-select form-select-lg rounded-4 supplier-organisations" multiple required>
                                                        @foreach ($organisation as $item)
                                                            @if ($item->type === 'supplier')
                                                                <option value="{{ $item->id }}" {{ in_array($item->id, $supplierOrganisationIds) ? 'selected' : '' }}>{{ strtoupper($item->name) }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Address</label>
                                                    <textarea name="address" rows="4" class="form-control rounded-4" required>{{ old('address', optional($user->supplierProfile)->address) }}</textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Website</label>
                                                    <input type="url" name="website" class="form-control form-control-lg rounded-4" value="{{ old('website', optional($user->supplierProfile)->website) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Review link</label>
                                                    <input type="url" name="review_link" class="form-control form-control-lg rounded-4" value="{{ old('review_link', optional($user->supplierProfile)->review_link) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Social media link</label>
                                                    <input type="url" name="social_link" class="form-control form-control-lg rounded-4" value="{{ old('social_link', optional($user->supplierProfile)->social_link) }}">
                                                </div>
                                            @endif

                                            <div class="col-12 pt-2">
                                                <button type="submit" class="btn btn-primary btn-lg rounded-4 px-4">Save changes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="col-xl-5">
                                <div class="border rounded-4 p-4">
                                    <div class="mb-4">
                                        <h4 class="mb-2">Change password</h4>
                                        <p class="text-muted mb-0">Refresh your password regularly to keep your account secure.</p>
                                    </div>

                                    <form method="POST" action="{{ route('password.update') }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">Current password</label>
                                                <input type="password" name="current_password" class="form-control form-control-lg rounded-4" required>
                                                @error('current_password', 'updatePassword')
                                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">New password</label>
                                                <input type="password" name="password" class="form-control form-control-lg rounded-4" required>
                                                @error('password', 'updatePassword')
                                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Confirm new password</label>
                                                <input type="password" name="password_confirmation" class="form-control form-control-lg rounded-4" required>
                                            </div>
                                            <div class="col-12 pt-2">
                                                <button type="submit" class="btn btn-dark btn-lg rounded-4 w-100">Update password</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    @if ($isSupplier)
        <script>
            $(function () {
                $('.supplier-organisations').select2({
                    placeholder: 'Select organisation categories',
                    width: '100%'
                });
            });
        </script>
    @endif
@endpush
@endsection
