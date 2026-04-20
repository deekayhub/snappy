@extends('layouts.app')

@section('title', 'Post a Job')

@section('section')
    <section class="py-5" style="background: linear-gradient(180deg, #f6fbff 0%, #ffffff 100%);">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-5">
                    <div class="h-100 p-4 p-lg-5 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #0f3c68 0%, #5fa8d3 100%);">
                        <span class="badge bg-white text-primary fw-semibold mb-3">Customer Portal</span>
                        <h1 class="text-white fw-bold">Post your job once and let suppliers come to you.</h1>
                        <p class="text-white-50 mb-4">Share the basics of what you need and your admin team can review every posted request from the back office.</p>
                        <div class="bg-white bg-opacity-10 rounded-4 p-4 text-white">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <i class="bi bi-check-circle-fill fs-4"></i>
                                <div>Only logged-in customer accounts can submit jobs.</div>
                            </div>
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <i class="bi bi-database-check fs-4"></i>
                                <div>Every post is stored in the database for admin review.</div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-layout-text-window-reverse fs-4"></i>
                                <div>Keep the form simple so customers can publish quickly.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="bg-white rounded-4 shadow-sm p-4 p-lg-5">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
                            <div>
                                <h2 class="fw-bold secondary-color mb-1">Post a Job</h2>
                                <p class="text-muted mb-0">Fill out the details below and we will save the request for the admin panel.</p>
                            </div>
                            <span class="badge text-bg-light border">Logged in as {{ auth()->user()->name }}</span>
                        </div>

                        <form method="POST" action="{{ route('customer.jobs.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Job Title</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Need football kits for school team">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Category</label>
                                    {{-- <input type="text" name="category" value="{{ old('category') }}" class="form-control @error('category') is-invalid @enderror" placeholder="Sportswear, signage, trophies"> --}}
                                    <select name="category" id="" class="form-select">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Organisation Name</label>
                                    <input type="text" name="organisation_name" value="{{ old('organisation_name', auth()->user()->customerProfile?->school_name) }}" class="form-control @error('organisation_name') is-invalid @enderror">
                                    @error('organisation_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Location</label>
                                    <input type="text" name="location" value="{{ old('location', auth()->user()->customerProfile?->county) }}" class="form-control @error('location') is-invalid @enderror">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Budget</label>
                                    <input type="number" min="0" step="0.01" name="budget" value="{{ old('budget') }}" class="form-control @error('budget') is-invalid @enderror" placeholder="1500">
                                    @error('budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label mb-1">Needed By</label>
                                    <input type="datetime-local" id="needed_by" name="needed_by" value="{{ old('needed_by') }}" class="form-control @error('needed_by') is-invalid @enderror">
                                    @error('needed_by')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label mb-1">Job Description</label>
                                    <textarea name="description" rows="6" class="form-control @error('description') is-invalid @enderror" placeholder="Tell suppliers what you need, quantities, delivery expectations, colors, sizes, or any special notes.">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-4">Post Job</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const neededByInput = document.getElementById('needed_by');
            if (neededByInput) {
                const now = new Date();
                const pad = (value) => String(value).padStart(2, '0');
                const minDateTime = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
                neededByInput.setAttribute('min', minDateTime);
            }
        });
    </script>
    
@endpush
