

<x-guest-layout>
    <div class="col-md-10 mx-auto">
        <div class="row m-0 rounded shadow">
            <div class="col-md-6 p-0 rounded">
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
                            <input name="name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone (Optional)</label>
                            <input name="phone" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Organisation
                            </label>
                            <select name="organisation" id="" class="form-select">
                                <option value="">Select Organisation</option>
                                <option value="club">Club</option>
                                <option value="team">Team</option>
                                <option value="organisation">Organisation</option>
                                <option value="school">School</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
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

</x-guest-layout>
