@extends('admin.layouts.app')
@section('title', 'System Settings')
@push('styles')
<style>
    .settings-card {
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 20px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
    }

    .settings-card .card-header-tabs {
        border-bottom: 1px solid #e2e8f0;
        padding: 0 1.5rem;
    }

    .settings-card .nav-tabs .nav-link {
        border: 0;
        border-bottom: 2px solid transparent;
        color: #64748b;
        font-weight: 600;
        padding: 1rem 1.25rem;
    }

    .settings-card .nav-tabs .nav-link.active {
        color: #1d4ed8;
        border-bottom-color: #1d4ed8;
        background: transparent;
    }

    .settings-card .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: .5rem;
    }

    .settings-card .form-control,
    .settings-card .form-select {
        min-height: 46px;
        border-radius: 12px;
        border-color: #dbe4f0;
        box-shadow: none;
    }

    .settings-logo-preview {
        width: 130px;
        height: 130px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        background: #f8fafc;
        overflow: hidden;
    }

    .settings-logo-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">System Settings</h3>
    </div>

    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card settings-card">
                <div class="card-body p-4">
                    <ul class="nav nav-tabs border-0 mb-4" id="settingsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="branding-tab" data-bs-toggle="tab" data-bs-target="#branding" type="button" role="tab">Branding</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">Contact</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">Notifications</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="footer-tab" data-bs-toggle="tab" data-bs-target="#footer" type="button" role="tab">Footer</button>
                        </li>
                    </ul>

                    <form id="settingsForm" enctype="multipart/form-data">
                        @csrf
                        <div class="tab-content" id="settingsTabContent">
                            <div class="tab-pane fade show active" id="branding" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Application Name</label>
                                        <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] ?? '' }}" placeholder="e.g. Snappy Quotes">
                                    </div>
                                </div>

                                @foreach([
                                    'site_logo' => 'Site Logo',
                                    'favicon' => 'Favicon',
                                    'footer_logo' => 'Footer Logo',
                                ] as $key => $label)
                                    @php
                                        $default = match ($key) {
                                            'site_logo' => 'assets/images/snappy-logo.png',
                                            'favicon' => 'assets/images/favicon.png',
                                            'footer_logo' => 'assets/images/footer-logo.png',
                                        };
                                        $current = $settings[$key] ?? null;
                                        $src = $current ? asset($current) : asset($default);
                                    @endphp
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">{{ $label }}</label>
                                        <div class="settings-logo-preview mb-3" id="{{ $key }}-preview">
                                            <img src="{{ $src }}" alt="{{ $label }}">
                                        </div>
                                        <input type="file" name="{{ $key }}" class="form-control settings-logo-input" data-preview="#{{ $key }}-preview" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                                    </div>
                                @endforeach
                            </div>

                            <div class="tab-pane fade" id="contact" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Help / Support Email</label>
                                        <input type="email" name="help_email" class="form-control" value="{{ $settings['help_email'] ?? '' }}" placeholder="e.g. support@snappyquotes.com">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Support Phone</label>
                                        <input type="text" name="support_phone" class="form-control" value="{{ $settings['support_phone'] ?? '' }}" placeholder="e.g. +1 234 567 890">
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label">Support Address</label>
                                        <textarea name="support_address" class="form-control" rows="2" placeholder="e.g. 123 Business Street, City, Country">{{ $settings['support_address'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="notifications" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Notification Email</label>
                                        <input type="email" name="notify_email" class="form-control" value="{{ $settings['notify_email'] ?? '' }}" placeholder="e.g. sales@snappyquotes.co.uk">
                                        <small class="text-muted">Receives new-user and new-subscription alerts.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="footer" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label">Copyright Text</label>
                                        <input type="text" name="copyright_text" class="form-control" value="{{ $settings['copyright_text'] ?? '' }}" placeholder="e.g. © 2026 Snappy Quotes Hub. All rights reserved.">
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">Facebook URL</label>
                                        <input type="url" name="facebook_url" class="form-control" value="{{ $settings['facebook_url'] ?? '' }}" placeholder="https://facebook.com/...">
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">Twitter / X URL</label>
                                        <input type="url" name="twitter_url" class="form-control" value="{{ $settings['twitter_url'] ?? '' }}" placeholder="https://twitter.com/...">
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">Instagram URL</label>
                                        <input type="url" name="instagram_url" class="form-control" value="{{ $settings['instagram_url'] ?? '' }}" placeholder="https://instagram.com/...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary rounded-4 px-4" id="saveSettingsBtn">
                                <i class="mdi mdi-content-save mr-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('.settings-logo-input').on('change', function() {
            var file = this.files[0];
            if (!file) return;

            var preview = $(this).data('preview');
            var reader = new FileReader();
            reader.onload = function(e) {
                $(preview).find('img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        });

        $('#settingsForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            Swal.fire({
                title: 'Saving...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('admin.settings.update') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: response.message || 'Settings saved successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    var message = 'Something went wrong.';
                    if (errors) {
                        message = Object.values(errors).flat().join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                    });
                }
            });
        });
    });
</script>
@endpush
