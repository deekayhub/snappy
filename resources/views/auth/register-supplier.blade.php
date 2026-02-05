<x-guest-layout>
    <div class="row my-4">
        <div class="col-md-8 mx-auto py-3 border rounded">
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


            <form method="POST" action="{{ route('register.supplier') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input name="name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Company Name</label>
                        <input name="company_name" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Phone</label>
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
                        <label>Website</label>
                        <input type="url" name="website" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Review Site Link</label>
                        <input type="url" name="review_link" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Social Media Link</label>
                        <input type="url" name="social_link" class="form-control">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <div class="col-md-12 text-end">
                        <button class="btn btn-success px-5 py-2">
                            Register as Supplier
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</x-guest-layout>
