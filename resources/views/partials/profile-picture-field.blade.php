@php
    $profilePictureLabel = $profilePictureLabel ?? 'Profile picture';
    $profilePictureHelp = $profilePictureHelp ?? 'Allowed: JPG, JPEG, PNG. Max size 10 MB.';
    $currentProfilePicture = $currentProfilePicture ?? null;
@endphp

<div class="col-12">
    <label class="form-label fw-semibold">{{ $profilePictureLabel }}</label>
    <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center" data-profile-picture-uploader>
        <div class="rounded-4 border bg-light overflow-hidden d-flex align-items-center justify-content-center flex-shrink-0" style="width: 120px; height: 120px;">
            <img
                data-profile-picture-preview
                src="{{ $currentProfilePicture ? asset($currentProfilePicture) : '' }}"
                alt="Profile picture preview"
                class="{{ $currentProfilePicture ? '' : 'd-none' }}"
                style="width: 100%; height: 100%; object-fit: cover;"
            >
            <span data-profile-picture-placeholder class="text-muted small text-center px-3 {{ $currentProfilePicture ? 'd-none' : '' }}">
                No picture selected
            </span>
        </div>

        <div class="flex-grow-1">
            <input
                type="file"
                name="profile_picture"
                class="form-control rounded-3"
                accept=".jpg,.jpeg,.png,.heic,.heif,image/jpeg,image/png,image/heic,image/heif"
                data-profile-picture-input
            >
            <div class="form-text">{{ $profilePictureHelp }}</div>
            <div class="text-danger small mt-2 d-none" data-profile-picture-error></div>
            @error('profile_picture')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
