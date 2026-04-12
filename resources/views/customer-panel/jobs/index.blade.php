@extends('customer-panel.layouts.app')
@section('title', 'My Jobs')

@section('content')
<div class="content-wrapper p-3">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h3 class="mb-1">My posted jobs</h3>
            <p class="text-muted mb-0">Track every request and see how many suppliers have replied.</p>
        </div>
        <button class="btn btn-primary rounded-4" data-bs-toggle="modal" data-bs-target="#postjobmodal">Post New Quote Request</button`>
    </div>

    @forelse ($jobs as $job)
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div>
                        <div class="small text-muted mb-1">Job #{{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <h4 class="mb-1">{{ $job->title }}</h4>
                        <div class="text-muted mb-2">{{ $job->category ?: 'General' }} | {{ $job->location ?: 'No location set' }}</div>
                        <p class="mb-0 text-muted">{{ \Illuminate\Support\Str::limit($job->description, 200) }}</p>
                    </div>
                    <div class="text-lg-end">
                        <span class="badge bg-{{ $job->status === 'open' ? 'success' : 'secondary' }}">{{ ucfirst($job->status) }}</span>
                        <div class="small text-muted mt-2">Needed by: {{ $job->needed_by?->format('d M Y') ?? 'Not set' }}</div>
                        <div class="small text-muted">Budget: {{ $job->budget ? '€ '.number_format((float) $job->budget, 2) : 'Not shared' }}</div>
                        <div class="fw-semibold mt-2">{{ $job->quotes_count }} supplier quotes</div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-light border rounded-4">No jobs posted yet.</div>
    @endforelse

    <div class="mt-4">
        {{ $jobs->links() }}
    </div>


    <!-- Modal -->
    <div class="modal fade" id="postjobmodal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="postjobmodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="{{ route('customer.jobs.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="postjobmodalLabel">Post a Job</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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
                                <input type="date" id="needed_by" name="needed_by" value="{{ old('needed_by') }}" class="form-control @error('needed_by') is-invalid @enderror">
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
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary px-5 py-2 rounded-4" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                        <button type="submit" class="btn btn-primary px-5 py-2 rounded-4">Post Job</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const neededByInput = document.getElementById('needed_by');
            if (neededByInput) {
                const today = new Date().toISOString().split('T')[0];
                neededByInput.setAttribute('min', today);
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->any())
                var myModal = new bootstrap.Modal(document.getElementById('postjobmodal'));
                myModal.show();
            @endif
        });
    </script>
    
@endpush