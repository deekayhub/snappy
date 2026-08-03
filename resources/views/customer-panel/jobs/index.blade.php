@extends('customer-panel.layouts.app')
@section('title', 'My Jobs')

@section('content')
<div class="content-wrapper p-3">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h3 class="mb-1">My posted jobs</h3>
            <p class="text-muted mb-0">Track every request and see how many suppliers have replied.</p>
        </div>
        <a href="{{ route('customer-panel.jobs.create') }}" class="btn btn-primary rounded-4">Post New Job</a>
    </div>

    <div class="row">
        @forelse ($jobs as $job)
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 job-card">
                    <div class="card-body p-4">

                        <!-- Badge -->
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <span class="badge bg-info text-white rounded-pill mb-3">
                                Job Details
                            </span>
                            <span class="rounded badge bg-{{ $job->status=='open' ? 'success' : 'secondary' }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h5 class="fw-bold mb-2">{{ $job->title }}</h5>

                        <p class="text-muted small mb-3">
                            Needed by:
                            {{ $job->needed_by?->diffForHumans() ?? 'No deadline set' }}
                        </p>

                        <!-- Info Box -->
                        <div class="  rounded-4 p-3 mb-3" style="background: #f7f9fc;">
                            <div class="small mb-2">
                                <span class="text-muted">Category:</span>
                                <strong>{{ ucfirst($job->categoryId?->name ?? 'General') }}</strong>
                            </div>

                            <div class="small mb-2">
                                <span class="text-muted">Location:</span>
                                <strong>{{ $job->location ?: 'Not specified' }}</strong>
                            </div>

                            <div class="small mb-2">
                                <span class="text-muted">Budget:</span>
                                <strong class="text-success">
                                    {{ $job->budget ? '£'.number_format($job->budget,2) : 'Not shared' }}
                                </strong>
                            </div> 
                        </div>

                        <!-- Description -->
                        <p class="small text-muted mb-3">
                            {{ \Illuminate\Support\Str::limit($job->description, 80) }}
                        </p>

                        <!-- Footer -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('customer-panel.quotes') }}#job-{{ $job->id }}" class="fw-semibold text-decoration-none">
                                {{ $job->quotes_count }} quotes
                            </a>

                            <div class="d-flex gap-1">
                                <a href="{{ route('customer-panel.jobs.show', $job) }}" class="btn btn-light btn-sm rounded-circle border" title="View">
                                    <i class="fa fa-eye"></i>
                                </a>

                                <a href="{{ route('customer-panel.jobs.edit', $job) }}" class="btn btn-light btn-sm rounded-circle border" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <button class="btn btn-light btn-sm rounded-circle border text-danger delete-job-btn"
                                    data-delete-url="{{ route('customer-panel.jobs.destroy', $job) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light rounded-4">
                    No jobs posted yet.
                </div>
            </div>
        @endforelse
    </div>
    

    <div class="mt-4">
        {{ $jobs->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        if (window.location.hash) {
            var target = window.location.hash;
            setTimeout(function () {
                var $el = $(target);
                if ($el.length) {
                    $('html, body').animate({
                        scrollTop: $el.offset().top - 100
                    }, 600);
                }
            }, 300);
        }
    });

    document.querySelectorAll('.delete-job-btn').forEach((button) => {
        button.addEventListener('click', async function () {
            const csrfToken = "{{ csrf_token() }}";
            const result = await Swal.fire({
                title: 'Delete this job?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
            });

            if (!result.isConfirmed) {
                return;
            }

            Swal.fire({
                title: 'Please wait...',
                text: 'Deleting job',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            const deleteFormData = new FormData();
            deleteFormData.append('_token', csrfToken);
            deleteFormData.append('_method', 'DELETE');

            try {
                const response = await fetch(this.dataset.deleteUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: deleteFormData,
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    Swal.close();
                    toastr.error(payload.message || 'Unable to delete job.');
                    return;
                }

                Swal.close();
                await Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: payload.message || 'Job deleted successfully.',
                });

                window.location.reload();
            } catch (error) {
                Swal.close();
                toastr.error('Unable to delete job.');
            }
        });
    });
</script>
@endpush

 
