@extends('admin.layouts.app')
@section('title', 'Reports')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Reports Dashboard</h3>
        <div>
            <button class="btn btn-outline-primary btn-sm">Export CSV</button>
            <button class="btn btn-primary btn-sm">Generate Report</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-3">
                    <label>Date From</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Date To</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Report Type</label>
                    <select class="form-control">
                        <option>All</option>
                        <option>Jobs</option>
                        <option>Orders</option>
                        <option>Customers</option>
                        <option>Suppliers</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-success w-100">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Revenue</h6>
                    <h3 class="mb-1">₹45,200</h3>
                    <span class="text-success">+12% from last week</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Jobs</h6>
                    <h3 class="mb-1">128</h3>
                    <span class="text-success">+8% growth</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Orders</h6>
                    <h3 class="mb-1">76</h3>
                    <span class="text-danger">-3% decline</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>New Customers</h6>
                    <h3 class="mb-1">34</h3>
                    <span class="text-success">+15% increase</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Charts Section -->
    <div class="row mt-4">

        <!-- Revenue Chart -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Revenue Overview</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">User Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="userChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- Table -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h6 class="mb-0">Recent Reports</h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Report Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Weekly Jobs Report</td>
                        <td>10 Apr 2026</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>₹12,000</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Customer Growth</td>
                        <td>09 Apr 2026</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>₹8,500</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Supplier Activity</td>
                        <td>08 Apr 2026</td>
                        <td><span class="badge bg-danger">Failed</span></td>
                        <td>₹5,200</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Revenue Line Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
        datasets: [{
            label: 'Revenue',
            data: [5000, 7000, 6000, 8000, 9000, 7500, 10000],
            borderWidth: 2,
            fill: false
        }]
    }
});

// Pie Chart
const pieCtx = document.getElementById('userChart').getContext('2d');
new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: ['Customers', 'Suppliers'],
        datasets: [{
            data: [70, 30],
        }]
    }
});
</script>
@endsection