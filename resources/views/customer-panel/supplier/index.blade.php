@extends('customer-panel.layouts.app')
@section('title', 'Suppliers')
@push('styles')
    <style>
        #supplierTable thead th {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding: 0.75rem 0.85rem;
        }
        #supplierTable tbody td {
            vertical-align: middle;
            padding: 0.75rem 0.85rem;
        }
        #supplierTable tbody tr:hover {
            background: #f1f3f5;
        }
        .filter-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #6c757d;
            margin-bottom: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="content-wrapper p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0 fw-bold">Register Suppliers</h3>
        </div>

        <div class="card shadow-sm mb-2 border-0 rounded-4">
            <div class="card-body py-2 px-3">
                <div class="row g-1 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fa fa-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search name, email, phone...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="categoryFilter" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="ratingFilter" class="form-select form-select-sm">
                            <option value="">Any Rating</option>
                            <option value="4">4 ★ & up</option>
                            <option value="3">3 ★ & up</option>
                            <option value="2">2 ★ & up</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-1">
                            <button id="filterBtn" class="btn btn-primary btn-sm flex-fill px-2">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                            <button id="resetBtn" class="btn btn-outline-secondary btn-sm flex-fill px-2">
                                <i class="fa fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table w-100 mb-0" id="supplierTable">
                        <thead>
                            <tr>
                                <th style="width:50px">#</th>
                                <th style="width:70px">Logo</th>
                                <th>Company Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th style="max-width:400px;white-space:normal">Categories</th>
                                <th>Rating</th>
                                <th style="width:100px">Reviews</th>
                                <th style="width:200px">Website</th>
                                <th style="width:80px">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" style="width: 600px;">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title fw-bold" id="offcanvasExampleLabel">Supplier Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">

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
                scrollX: true,
                scrollCollapse: true,
                paging: true,
                pageLength: 25,
                ajax: {
                    url: "{{ route('customer-panel.suppliers') }}",
                    data: function (d) {
                        d.search = $('#searchInput').val();
                        d.category = $('#categoryFilter').val();
                        d.rating = $('#ratingFilter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'company_logo', name: 'company_logo', orderable: false, searchable: false },
                    { data: 'company_name', name: 'supplierProfile.company_name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'categories', name: 'categories', orderable: false, searchable: false },
                    { data: 'avg_rating', name: 'avg_rating' },
                    { data: 'total_reviews', name: 'total_reviews' },
                    { data: 'website', name: 'website', orderable: false, searchable: false },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                order: [[5, 'desc']],
                language: {
                    search: "",
                    searchPlaceholder: "Search table...",
                    emptyTable: "No suppliers found."
                },
                dom: '<"d-flex justify-content-between align-items-center px-3 py-2 border-bottom"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center px-3 py-2"ip>',
            });

            $('#filterBtn').on('click', function () {
                table.draw();
            });

            $('#resetBtn').on('click', function () {
                $('#searchInput').val('');
                $('#categoryFilter').val('');
                $('#ratingFilter').val('');
                table.draw();
            });

            $('#searchInput').on('keydown', function (e) {
                if (e.key === 'Enter') {
                    table.draw();
                }
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
