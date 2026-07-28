<x-guest-layout>
    {{-- background: linear-gradient(156deg, rgba(254, 255, 254, 1) 58%, rgba(230, 238, 249, 1) 73%) --}}
    <div class="col-md-10 mx-auto">
        <div class="row m-0 rounded shadow">
            <div class="col-md-6 p-0 rounded"  style="background: #e4eefb47;">
                <div class="register-login rounded-start"></div>
            </div>
            <div class="col-md-6 py-3  rounded bg-white">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <h2 class="fw-bold text-center secondary-color">Register</h2>
                <form method="POST" action="{{ route('register.supplier') }}">
                    @csrf
                    <div class="row m-0">
                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">Name</label>
                            <input name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">Company Name</label>
                            <input name="company_name" value="{{ old('company_name') }}" class="form-control @error('company_name') is-invalid @enderror">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="off">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">Phone (Optional)</label>
                            <input name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label mb-0">
                                Organisation
                            </label>
                            <select name="organisation[]" id="" class="form-select select2 rounde-3 @error('organisation') is-invalid @enderror @error('organisation.*') is-invalid @enderror" multiple>
                                <option value="">Select Organisation</option>
                                @foreach ($organisation ?? [] as $item)
                                    <option value="{{ $item->id }}" @selected(collect(old('organisation', []))->contains((string) $item->id) || collect(old('organisation', []))->contains($item->id))>{{ ucwords($item->name) }}</option>
                                @endforeach
                            </select>
                            @error('organisation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('organisation.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">Website</label>
                            <input type="text" name="website" value="{{ old('website') }}" class="form-control @error('website') is-invalid @enderror">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">Review Site Link  (eg, Trustpilot etc)</label>
                            <input type="text" name="review_link" value="{{ old('review_link') }}" class="form-control @error('review_link') is-invalid @enderror">
                            @error('review_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">Social Media Link</label>
                            <input type="text" name="social_link" value="{{ old('social_link') }}" class="form-control @error('social_link') is-invalid @enderror">
                            @error('social_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">Address</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="off">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" autocomplete="off">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary px-5 py-2">
                                Register as Supplier
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select organisation",
                allowClear: true,
                width: '100%'
            });
        });
    </script>

</x-guest-layout>
