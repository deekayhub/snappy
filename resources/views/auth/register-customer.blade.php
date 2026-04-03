

<x-guest-layout>
    <div class="col-md-10 mx-auto">
        <div class="row m-0 rounded shadow">
            <div class="col-md-6 p-0 rounded" style="background: #e4eefb47;">
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
                <h2 class="fw-bold text-center secondary-color montserrat">Register</h2>
                <form method="POST" action="{{ route('register.customer') }}">
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
                            <label class="form-label mb-0">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
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

                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">
                                Organisation
                            </label>
                            <select name="organisation[]" id="" class="form-select select2 @error('organisation') is-invalid @enderror @error('organisation.*') is-invalid @enderror">
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
                            <label class="form-label mb-0">County (Optional)</label>
                            <input type="text" name="county" value="{{ old('county') }}" class="form-control @error('county') is-invalid @enderror">
                            @error('county')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label mb-0">Club /School Name (Optional)</label>
                            <input type="text" name="school_name" value="{{ old('school_name') }}" class="form-control @error('school_name') is-invalid @enderror">
                            @error('school_name')
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

                        <div class="col-12 text-end mt-2">
                            <button class="btn btn-primary px-5 py-2">
                                Register as Customer
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
