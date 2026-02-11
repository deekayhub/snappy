

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
                <form method="POST" action="{{ route('register.customer') }}">
                    @csrf

                    <div class="row m-0">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name</label>
                            <input name="name" class="form-control" placeholder="Name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone (Optional)</label>
                            <input name="phone" class="form-control" placeholder="Phone Number">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Organisation
                            </label>
                            <select name="organisation[]" id="" class="form-select select2">
                                <option value="">Select Organisation</option>
                                @foreach ($organisation ?? [] as $item)
                                    <option value="{{ $item->id }}">{{ strtoupper($item->name) }}</option>
                                @endforeach 
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">County (Optional)</label>
                            <input type="text" name="county" class="form-control" placeholder="County">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Club /School Name (Optional)</label>
                            <input type="text" name="school_name" class="form-control" placeholder="Club /School Name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
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
