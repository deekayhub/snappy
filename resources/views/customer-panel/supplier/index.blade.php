@extends('supplier-panel.layouts.app')
@section('title', 'Suppliers')

@section('content')
    <div class="content-wrapper p-3">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <h6>Total Suppliers</h6>
                    <h3 id="totalSuppliers">0</h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <h6>Active Suppliers</h6>
                    <h3 id="activeSuppliers">0</h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <h6>Avg Rating</h6>
                    <h3 id="avgRating">0</h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm p-3">
                    <h6>Total Reviews</h6>
                    <h3 id="totalReviews">0</h3>
                </div>
            </div>
        </div>

        <div class="card mb-3 p-3 shadow-sm">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search supplier...">
                </div>

                <div class="col-md-3">
                    <select id="statusFilter" class="form-control">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="ratingFilter" class="form-control">
                        <option value="">All Ratings</option>
                        <option value="4">4+ Rating</option>
                        <option value="3">3+ Rating</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button id="filterBtn" class="btn btn-dark w-100">Apply</button>
                </div>
            </div>
        </div>

        <table class="table table-bordered" id="supplierTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Rating</th>
                    <th>Total Reviews</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

@endsection
@push('scripts')
    <script>
        $(function () {

            let table = $('#supplierTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('customer-panel.suppliers') }}",
                    data: function (d) {
                        d.search = $('#searchInput').val();
                        d.status = $('#statusFilter').val();
                        d.rating = $('#ratingFilter').val();
                    }
                },
                columns: [
                    { data: 'name' },
                    { data: 'email' },
                    { data: 'phone' },
                    { data: 'phone' },
                    { data: 'phone' },
                    { data: 'phone' },
                     
                    { data: 'action', orderable: false, searchable: false }
                ]
            });

            $('#filterBtn').click(function () {
                table.draw();
            });

            $('#searchInput').keyup(function () {
                table.draw();
            });

        });
    </script>
@endpush