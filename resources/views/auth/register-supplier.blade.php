<x-guest-layout>
    <div class="col-md-10 mx-auto">
        <div class="row m-0 rounded shadow">
            <div class="col-md-6 p-0 rounded"  style="background: linear-gradient(156deg, rgba(254, 255, 254, 1) 58%, rgba(230, 238, 249, 1) 73%)">
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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h2 class="fw-bold text-center secondary-color">Register</h2>
                <form method="POST" action="{{ route('register.supplier') }}">
                    @csrf
                    <div class="row m-0">
                        <div class="col-md-6 mb-3">
                            <label>Name</label>
                            <input name="name" class="form-control" placeholder="Enter your name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Company Name</label>
                            <input name="company_name" placeholder="Company name" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email" autocomplete="off" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Phone</label>
                            <input name="phone" class="form-control" placeholder="Phone number">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                Organisation
                            </label>
                            <select name="organisation[]" id="" class="form-select select2" multiple>
                                <option value="">Select Organisation</option>
                                @foreach ($organisation ?? [] as $item)
                                    <option value="{{ $item->id }}">{{ strtoupper($item->name) }}</option>
                                @endforeach 
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Website</label>
                            <input type="url" name="website" class="form-control" placeholder="https://example.com">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Review Site Link  (eg, Trustpilot etc)</label>
                            <input type="url" name="review_link" class="form-control" placeholder="https://example.com/reviews">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Social Media Link</label>
                            <input type="url" name="social_link" class="form-control" placeholder="https://facebook.com/yourpage">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control" placeholder="Enter your address"></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter password" autocomplete="off" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
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
