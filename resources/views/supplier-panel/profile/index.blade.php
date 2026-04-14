@extends('supplier-panel.layouts.app')
@section('title', 'Supplier Profile')

@php
    $supplierOrganisationIds = old('supplier_organisation', $user->organisationCategories->where('type', 'supplier')->pluck('id')->toArray());
    $savedSocialLinks = old('social_links');
    if (! is_array($savedSocialLinks)) {
        $savedSocialLinks = $user->supplierProfile?->social_links;
    }
    if (! is_array($savedSocialLinks) || empty($savedSocialLinks)) {
        $savedSocialLinks = ! empty($user->supplierProfile?->social_link)
            ? [['platform' => 'other', 'url' => $user->supplierProfile?->social_link]]
            : [['platform' => 'facebook', 'url' => '']];
    }
@endphp

@section('content')
<div class="content-wrapper p-3">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                <div>
                    <h3 class="mb-1">Supplier profile</h3>
                    <p class="text-muted mb-0">Edit the company details listed in your supplier requirements file.</p>
                </div>
                <span class="badge bg-light text-dark px-3 py-2 rounded-3">Free supplier account</span>
            </div> 

            <div class="row g-4">
                <div class="col-xl-8">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control  rounded-3" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control  rounded-3 bg-light" value="{{ $user->email }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone no.</label>
                                <input type="text" name="phone" class="form-control  rounded-3" value="{{ old('phone', $user->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company name</label>
                                <input type="text" name="company_name" class="form-control  rounded-3" value="{{ old('company_name', $user->supplierProfile?->company_name) }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Company address</label>
                                <textarea name="address" rows="4" class="form-control rounded-3" required>{{ old('address', $user->supplierProfile?->address) }}</textarea>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Company description (optional)</label>
                                <textarea name="company_description" rows="4" class="form-control rounded-3" placeholder="Tell customers about your company, products, and strengths.">{{ old('company_description', $user->supplierProfile?->company_description) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website address</label>
                                <input type="url" name="website" class="form-control  rounded-3" value="{{ old('website', $user->supplierProfile?->website) }}">
                            </div>
                           
                            <div class="col-md-6">
                                <label class="form-label">Review site link</label>
                                <input type="url" name="review_link" class="form-control  rounded-3" value="{{ old('review_link', $user->supplierProfile?->review_link) }}">
                            </div>
                             <div class="col-md-12">
                                <label class="form-label">Company logo</label>
                                <input type="file" name="company_logo" class="form-control rounded-3" accept=".jpg,.jpeg,.png,.webp,image/*">
                                @if ($user->supplierProfile?->company_logo)
                                    <div class="mt-2">
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::url($user->supplierProfile->company_logo) }}"
                                            alt="Company logo"
                                            class="rounded-3 border"
                                            style="height: 72px; width: 72px; object-fit: cover;"
                                        >
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Which service do you provide?</label>
                                <select name="supplier_organisation[]" class="form-select form-select-lg rounded-3 supplier-organisations" multiple required>
                                    @foreach ($organisation as $item)
                                        <option value="{{ $item->id }}" {{ in_array($item->id, $supplierOrganisationIds) ? 'selected' : '' }}>{{ strtoupper($item->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Social links</label>
                                <div id="social-links-wrapper" class="d-flex flex-column gap-2">
                                    @foreach ($savedSocialLinks as $index => $socialLink)
                                        <div class="row g-2 align-items-center social-link-row" data-index="{{ $index }}">
                                            <div class="col-md-4">
                                                <select name="social_links[{{ $index }}][platform]" class="form-select rounded-3">
                                                    <option value="facebook" @selected(($socialLink['platform'] ?? '') === 'facebook')>Facebook</option>
                                                    <option value="instagram" @selected(($socialLink['platform'] ?? '') === 'instagram')>Instagram</option>
                                                    <option value="youtube" @selected(($socialLink['platform'] ?? '') === 'youtube')>YouTube</option>
                                                    <option value="linkedin" @selected(($socialLink['platform'] ?? '') === 'linkedin')>LinkedIn</option>
                                                    <option value="x" @selected(($socialLink['platform'] ?? '') === 'x')>X (Twitter)</option>
                                                    <option value="tiktok" @selected(($socialLink['platform'] ?? '') === 'tiktok')>TikTok</option>
                                                    <option value="other" @selected(($socialLink['platform'] ?? '') === 'other')>Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-7">
                                                <input
                                                    type="url"
                                                    name="social_links[{{ $index }}][url]"
                                                    class="form-control rounded-3"
                                                    placeholder="https://..."
                                                    value="{{ $socialLink['url'] ?? '' }}"
                                                >
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger border rounded-3 remove-social-link"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-dark rounded-3 mt-2" id="add-social-link">+ Add social link</button>
                            </div>
                            
                            
                            <div class="col-12 pt-2">
                                <button class="btn btn-primary rounded-3 px-4">Save supplier profile</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-xl-4">
                    <div class="border rounded-3 p-4 mb-4">
                        <h5 class="mb-2">Quote access</h5>
                        <p class="text-muted mb-0">Free suppliers can browse the job board. Quote submission is visually reserved but still disabled until subscription and quote tables are added.</p>
                    </div>
                    <div class="border rounded-3 p-4">
                        <h5 class="mb-3">Change password</h5>
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Current password</label>
                                <input type="password" name="current_password" class="form-control rounded-3" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New password</label>
                                <input type="password" name="password" class="form-control rounded-3" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm new password</label>
                                <input type="password" name="password_confirmation" class="form-control rounded-3" required>
                            </div>
                            <button class="btn btn-dark rounded-3 w-100">Update password</button>
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

        const wrapper = document.getElementById('social-links-wrapper');
        const addBtn = document.getElementById('add-social-link');
        if (!wrapper || !addBtn) {
            return;
        }

        function reindexRows() {
            const rows = wrapper.querySelectorAll('.social-link-row');
            rows.forEach((row, index) => {
                row.setAttribute('data-index', index);
                const platform = row.querySelector('select');
                const url = row.querySelector('input[type=\"url\"]');
                if (platform) platform.name = `social_links[${index}][platform]`;
                if (url) url.name = `social_links[${index}][url]`;
            });
        }

        function addRow() {
            const index = wrapper.querySelectorAll('.social-link-row').length;
            const row = document.createElement('div');
            row.className = 'row g-2 align-items-center social-link-row';
            row.setAttribute('data-index', index);
            row.innerHTML = `
                <div class="col-md-4">
                    <select name="social_links[${index}][platform]" class="form-select rounded-3">
                        <option value="facebook">Facebook</option>
                        <option value="instagram">Instagram</option>
                        <option value="youtube">YouTube</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="x">X (Twitter)</option>
                        <option value="tiktok">TikTok</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <input type="url" name="social_links[${index}][url]" class="form-control rounded-3" placeholder="https://...">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger border rounded-3 remove-social-link"><i class="fa fa-times"></i></button>
                </div>
            `;
            wrapper.appendChild(row);
            reindexRows();
        }

        addBtn.addEventListener('click', addRow);

        wrapper.addEventListener('click', function (event) {
            const removeBtn = event.target.closest('.remove-social-link');
            if (!removeBtn) {
                return;
            }
            const rows = wrapper.querySelectorAll('.social-link-row');
            if (rows.length <= 1) {
                const input = rows[0]?.querySelector('input[type=\"url\"]');
                if (input) input.value = '';
                return;
            }
            removeBtn.closest('.social-link-row')?.remove();
            reindexRows();
        });
    });
</script>
@endpush
@endsection
