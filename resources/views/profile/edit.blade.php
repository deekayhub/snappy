
@extends('layouts.app')
@section('title', 'Profile')

@section('section')
<div class="container">
    <div class="row justify-content-center my-4">
        <div class="col-lg-8">

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">My Profile</h5>
                </div>

                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
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
                    {{-- @dump($user->toArray()) --}}

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf

                        <div class="row my-3">
                            <!-- Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <input name="name" class="form-control"
                                    value="{{ old('name', $user->name) }}" required>
                            </div>

                            <!-- Email (Read Only) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input class="form-control" value="{{ $user->email }}" disabled>
                                <small class="text-muted">Email cannot be changed</small>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input name="phone" class="form-control"
                                    value="{{ old('phone', $user->phone) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Organisation
                                </label>
                                <select name="organisation" id="" value="{{ old('organisation', $user->organisation) }}" class="form-select" required>
                                    <option value="">Select Organisation</option>
                                    <option value="club" {{ old('organisation', $user->organisation) === 'club' ? 'selected' : '' }}>Club</option>
                                    <option value="team" {{ old('organisation', $user->organisation) === 'team' ? 'selected' : '' }}>Team</option>
                                    <option value="organisation" {{ old('organisation', $user->organisation) === 'organisation' ? 'selected' : '' }}>Organisation</option>
                                    <option value="school" {{ old('organisation', $user->organisation) === 'school' ? 'selected' : '' }}>School</option>
                                </select>
                            </div>
                            @if($user->role === 'supplier')

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Company Name</label>
                                    <input name="company_name" class="form-control"
                                        value="{{ old('company_name', $user->company_name) }}" required>
                                </div>



                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" required>{{ old('address', $user->address) }}</textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Website</label>
                                    <input name="website" class="form-control"
                                        value="{{ old('website', $user->website) }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Review Link</label>
                                    <input name="review_link" class="form-control"
                                        value="{{ old('review_link', $user->review_link) }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Social Media Link</label>
                                    <input name="social_link" class="form-control"
                                        value="{{ old('social_link', $user->social_link) }}">
                                </div>

                            @endif
                        </div>

                        <div class="col-md-12 text-end">
                            <button class="btn btn-primary py-2 px-4">
                                Update Profile
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

