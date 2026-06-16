@extends('customer-panel.layouts.app')
@section('title', 'Suppliers')
@push('styles')
    <style>
        .supplier-action-btn.view {
            background: #0887c8;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="content-wrapper p-3">
       <div class="page-header">
            <h3 class="page-title"> 
                Register Suppliers
            </h3> 
        </div>
        {{-- <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control"
                                placeholder="Search suppliers...">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select id="statusFilter" class="form-select bg-white text-dark">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="ratingFilter" class="form-select bg-white text-dark">
                            <option value="">All Ratings</option>
                            <option value="4">⭐⭐⭐⭐ & up</option>
                            <option value="3">⭐⭐⭐ & up</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button id="resetBtn" class="btn btn-outline-secondary w-100">
                            Reset
                        </button>
                    </div>

                </div>
            </div>
        </div> --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table w-100 border " id="supplierTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Logo</th>
                                <th>Company Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Rating</th>
                                <th>Total Reviews</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                {{-- <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                Button 
                </button> --}}

                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" style="width: 600px;">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Supplier Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        {{-- @include('customer-panel.supplier.supplier-details') --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            let table = $('#supplierTable').DataTable({
                processing: true,
                serverSide: true,
                // scrollY: '400px',
                scrollCollapse: true,
                paging: true,
                ajax: {
                    url: "{{ route('customer-panel.suppliers') }}",
                     data: function (d) {
                        d.search = $('#searchInput').val();
                        d.status = $('#statusFilter').val();
                        d.rating = $('#ratingFilter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'company_logo', name: 'company_logo', orderable: false, searchable: false },
                    { data: 'company_name', name: 'supplierProfile.company_name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'avg_rating', name: 'avg_rating' },
                    { data: 'total_reviews', name: 'total_reviews' },
                     
                    { data: 'actions', orderable: false, searchable: false }
                ],
                
            });

            let delayTimer;
            $('#searchInput').on('keyup', function () {
                clearTimeout(delayTimer);
                delayTimer = setTimeout(() => table.draw(), 400);
            });

            $('#statusFilter').on('change', function () {
                table.draw();
            });

            $('#ratingFilter').on('change', function () {
                table.draw();
            });

            $('#resetBtn').on('click', function () {
                $('#searchInput').val('');
                $('#statusFilter').val('');
                $('#ratingFilter').val('');
                table.draw();
            });
        });
        $(document).on('click', '.supplier-action-btn.view', function () {
            const supplierId = $(this).data('id');
            const offcanvasElement = document.getElementById('offcanvasExample');
            const offCanvas = new bootstrap.Offcanvas(offcanvasElement);

            offCanvas.show();
            $('#offcanvasExample .offcanvas-body').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 mb-0">Loading supplier details...</p>
                </div>
            `);

            $.ajax({
                url: "{{ route('customer-panel.suppliers.details', ':id') }}".replace(':id', supplierId), 
                type: 'GET',
                success: function (response) {
                    $('#offcanvasExample .offcanvas-body').html(response);
                },
                error: function (xhr) {
                    $('#offcanvasExample .offcanvas-body').html(`
                        <div class="alert alert-danger">
                            Failed to load supplier details.
                        </div>
                    `);
                }
            });
        });
    </script>

@endsection
@push('scripts')
@endpush