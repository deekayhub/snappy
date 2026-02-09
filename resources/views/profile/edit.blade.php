
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
                            @if($user->role === 'supplier')
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Company Name</label>
                                    <input name="company_name" class="form-control"
                                        value="{{ old('company_name', $user->company_name) }}" required>
                                </div>
                            @endif
                            @if($user->role === 'customer')
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Organisation
                                </label>
                                <select name="organisation[]" id="" value="{{ old('organisation', $user->organisation) }}" class="form-select select2" required>
                                    <option value="">Select Organisation</option>
                                    @foreach ($organisation ?? [] as $item)
                                        @if($item->type === 'customer')
                                        <option value="{{ $item->id }}" {{ in_array($item->id, old('organisation', $user->organisations->pluck('id')->toArray())) ? 'selected' : '' }}>{{ strtoupper($item->name) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            @if($user->role === 'supplier')
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">
                                        Organisation
                                    </label>
                                    <select name="organisation[]" id="" value="{{ old('organisation', $user->organisation) }}" class="form-select select2" required multiple>
                                        <option value="">Select Organisation</option>
                                        @foreach ($organisation ?? [] as $item)
                                            @if($item->type === 'supplier')
                                            <option value="{{ $item->id }}" {{ in_array($item->id, old('organisation', $user->organisations->pluck('id')->toArray())) ? 'selected' : '' }}>{{ strtoupper($item->name) }}</option>
                                            @endif
                                        @endforeach
                                    </select>
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

            {{-- <div class="row">
                <div class="col-md-4 border-right shadow">
                    <div class="d-flex flex-column align-items-center text-center p-3 py-5"><img class="rounded-circle mt-5" width="150px" src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg"><span class="font-weight-bold">Edogaru</span><span class="text-black-50">edogaru@mail.com.my</span><span> </span></div>
                </div>
                <div class="col-md-8 border-right shadow">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Profile Settings</h4>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6"><label class="labels">Name</label><input type="text" class="form-control" placeholder="first name" value=""></div>
                            <div class="col-md-6"><label class="labels">Surname</label><input type="text" class="form-control" value="" placeholder="surname"></div>
                        
                            <div class="col-md-6"><label class="labels">Mobile Number</label><input type="text" class="form-control" placeholder="enter phone number" value=""></div>
                            <div class="col-md-6"><label class="labels">Address Line 1</label><input type="text" class="form-control" placeholder="enter address line 1" value=""></div>
                            <div class="col-md-6"><label class="labels">Address Line 2</label><input type="text" class="form-control" placeholder="enter address line 2" value=""></div>
                            <div class="col-md-6"><label class="labels">Postcode</label><input type="text" class="form-control" placeholder="enter address line 2" value=""></div>
                            <div class="col-md-6"><label class="labels">State</label><input type="text" class="form-control" placeholder="enter address line 2" value=""></div>
                            <div class="col-md-6"><label class="labels">Area</label><input type="text" class="form-control" placeholder="enter address line 2" value=""></div>
                            <div class="col-md-6"><label class="labels">Email ID</label><input type="text" class="form-control" placeholder="enter email id" value=""></div>
                            <div class="col-md-6"><label class="labels">Education</label><input type="text" class="form-control" placeholder="education" value=""></div>
                        
                            <div class="col-md-6"><label class="labels">Country</label><input type="text" class="form-control" placeholder="country" value=""></div>
                            <div class="col-md-6"><label class="labels">State/Region</label><input type="text" class="form-control" value="" placeholder="state"></div>
                        </div>
                        <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="button">Save Profile</button></div>
                    </div>
                </div>
                
            </div> --}}

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
@endsection

