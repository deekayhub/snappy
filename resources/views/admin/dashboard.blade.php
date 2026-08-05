@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content-wrapper p-3">
    <div class="row">
        <div class="col-sm-12 mb-3">
            <div class="row">
                <div class="col-sm-4 col-xl-2 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Jobs</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-briefcase align-middle"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <h2 class="mt-1 mb-3">{{ $totalJobs ?? '0' }}</h2>
                            <div class="mb-0">
                                <span class="badge {{ $jobPercentage >= 0 ? 'badge-success' : 'badge-danger' }}">
                                    {{ number_format($jobPercentage, 1) }}%
                                </span>
                                <span class="text-muted">Since last week</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-xl-2 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Open Jobs</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-folder align-middle"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <h2 class="mt-1 mb-3">{{ $openJobs ?? '0' }}</h2>
                            <div class="mb-0">
                                <span class="badge badge-info">{{ number_format($openJobShare, 1) }}%</span>
                                <span class="text-muted">of all jobs</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-xl-2 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Quotes</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square align-middle"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <h2 class="mt-1 mb-3">{{ $totalQuotes ?? '0' }}</h2>
                            <div class="mb-0">
                                <span class="badge {{ $quotePercentage >= 0 ? 'badge-success' : 'badge-danger' }}">
                                    {{ number_format($quotePercentage, 1) }}%
                                </span>
                                <span class="text-muted">Since last week</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-xl-2 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Revenue</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign align-middle"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <h2 class="mt-1 mb-3">£ {{ number_format((float) ($totalRevenue ?? 0), 2) }}</h2>
                            <div class="mb-0">
                                <span class="badge {{ $revenuePercentage >= 0 ? 'badge-success' : 'badge-danger' }}">
                                    {{ number_format($revenuePercentage, 1) }}%
                                </span>
                                <span class="text-muted">Since last week</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-xl-2 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Supplier</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity align-middle"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                    </div>
                                </div>
                            </div>
                            <h2 class="mt-1 mb-3">{{ $supplierCount ?? '0' }}</h2>
                            <div class="mb-0">
                                <span class="badge {{ $supplierPercentage >= 0 ? 'badge-success' : 'badge-danger' }}">
                                    {{ number_format($supplierPercentage, 1) }}%
                                </span>
                                <span class="text-muted">Since last week</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-xl-2 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Customer</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users align-middle"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <h2 class="mt-1 mb-3">{{ $customerCount ?? '0' }}</h2>
                            <div class="mb-0">
                                <span class="badge {{ $customerPercentage >= 0 ? 'badge-success' : 'badge-danger' }}">
                                    {{ number_format($customerPercentage, 1) }}%
                                </span>
                                <span class="text-muted">Since last week</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="row">
            <div class="col-lg-8 d-flex flex-column">
                <div class="row flex-grow">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card card-rounded">
                        <div class="card-body">
                            <div class="d-sm-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="card-title card-title-dash">Jobs Overview</h4>
                                <p class="card-subtitle card-subtitle-dash">Customer jobs created by month</p>
                            </div>
                            {{-- <div>
                                <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle toggle-dark mb-0 me-0" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> This month </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                    <h6 class="dropdown-header">Settings</h6>
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Separated link</a>
                                </div>
                                </div>
                            </div> --}}
                            </div>
                            <div class="d-sm-flex align-items-center mt-1 justify-content-between">
                            <div class="d-sm-flex align-items-center mt-4 justify-content-between">
                                <h2 class="me-2 fw-bold">{{ $jobsCurrentWeek ?? '0' }}</h2>
                                <h4 class="{{ $jobsWeekPercentage >= 0 ? 'text-success' : 'text-danger' }}">({{ $jobsWeekPercentage >= 0 ? '+' : '' }}{{ number_format($jobsWeekPercentage, 1) }}%)</h4>
                                <span class="text-muted ms-1">this week</span>
                            </div>
                            <div class="me-3">
                                <div id="marketingOverview-legend"></div>
                            </div>
                            </div>
                            <div class="chartjs-bar-wrapper mt-3">
                            <canvas id="marketingOverview"></canvas>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row flex-grow">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card card-rounded">
                            <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                    <div>
                                        <h4 class="card-title card-title-dash">Latest Quotes</h4>
                                        {{-- <p class="card-subtitle card-subtitle-dash">You have 0+ new Quotes</p> --}}
                                    </div>
                                    <div>
                                        {{-- <button class="btn btn-primary text-white mb-0 me-0" type="button"><i class="mdi mdi-account-plus"></i>Add new member</button> --}}
                                    </div>
                                </div>
                                <div class="table-responsive  mt-1">
                                <table class="table w-100 select-table">
                                    <thead>
                                    <tr>                                        
                                        <th>Customer</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($jobs->take(10) as $job )
                                            <tr>                                        
                                                <td>
                                                    <div>
                                                        <h6>{{ $job->user?->name ?? '' }}</h6>
                                                        <p>{{ $job->user?->email ?? '' }}</p>
                                                    </div>
                                                </td>
                                                <td>
                                                <h6>{{ $job->title ?? '' }}</h6>
                                                {{-- <p class="text-truncate">{{ $job->description ?? '' }}</p> --}}
                                                </td>
                                                <td>{{ $job->categoryId?->name ?? 'General' }}</td>
                                                <td>
                                                <div class="badge badge-opacity-success">{{ $job->status }}</div>
                                                </td>
                                            </tr>  
                                        @empty    
                                            <tr>
                                                <td colspan="4" class="text-center">No quotes found.</td>
                                            </tr>                                      
                                        @endforelse                                    
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-flex flex-column">
                <div class="row flex-grow">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card card-rounded">
                            <div class="card-body">
                                <div class="row">
                                <div class="col-lg-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="card-title card-title-dash">Type By Amount</h4>
                                    </div>
                                    <div>
                                    <canvas class="my-auto" id="doughnutChart"></canvas>
                                    </div>
                                    <div id="doughnutChart-legend" class="mt-5 text-center"></div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row flex-grow">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card card-rounded">
                        <div class="card-body">
                            <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="card-title card-title-dash"> Report</h4>
                                </div>
                                <div>
                                    <div class="dropdown">
                                    <button class="btn btn-light dropdown-toggle toggle-dark mb-0 me-0" type="button" id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Month Wise </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                                        <h6 class="dropdown-header">week Wise</h6>
                                        <a class="dropdown-item" href="#">Year Wise</a>
                                    </div>
                                    </div>
                                </div>
                                </div>
                                <div class="mt-3">
                                <canvas id="leaveReport"></canvas>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row flex-grow">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        var canvas = document.getElementById('marketingOverview');
        if (!canvas || typeof Chart === 'undefined') return;

        var existing = Chart.getChart(canvas);
        if (existing) existing.destroy();

        var monthlyJobs = @json($monthlyJobsChart);

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: monthlyJobs.map(function (m) { return m.label; }),
                datasets: [{
                    label: 'Jobs',
                    data: monthlyJobs.map(function (m) { return m.total; }),
                    backgroundColor: "#1F3BB3",
                    borderColor: '#1F3BB3',
                    borderWidth: 0,
                    barPercentage: 0.55,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        border: { display: false },
                        grid: { display: true, drawTicks: false, color: "#F0F0F0", zeroLineColor: '#F0F0F0' },
                        ticks: { beginAtZero: true, autoSkip: true, maxTicksLimit: 4, color: "#6B778C", font: { size: 10 } }
                    },
                    x: {
                        border: { display: false },
                        stacked: true,
                        grid: { display: false, drawTicks: false },
                        ticks: { autoSkip: false, maxTicksLimit: 12, color: "#6B778C", font: { size: 9 } }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });

        var doughnutCanvas = document.getElementById('doughnutChart');
        if (doughnutCanvas && typeof Chart !== 'undefined') {
            var existingDoughnut = Chart.getChart(doughnutCanvas);
            if (existingDoughnut) existingDoughnut.destroy();

            var amountByStatus = @json($quoteAmountByStatus);

            new Chart(doughnutCanvas, {
                type: 'doughnut',
                data: {
                    labels: amountByStatus.map(function (s) { return s.label; }),
                    datasets: [{
                        data: amountByStatus.map(function (s) { return s.value; }),
                        backgroundColor: amountByStatus.map(function (s) { return s.color; }),
                        borderColor: amountByStatus.map(function (s) { return s.color; }),
                        borderWidth: 1
                    }]
                },
                options: {
                    cutout: 90,
                    animationEasing: "easeOutBounce",
                    animateRotate: true,
                    animateScale: false,
                    responsive: true,
                    maintainAspectRatio: true,
                    showScale: true,
                    plugins: { legend: { display: false } }
                }
            });

            var legendEl = document.getElementById('doughnutChart-legend');
            if (legendEl) {
                legendEl.innerHTML = '';
                var ul = document.createElement('ul');
                ul.style.cssText = 'list-style:none;margin:0;padding:0;display:inline-flex;flex-direction:column;gap:6px;text-align:left;';
                amountByStatus.forEach(function (s) {
                    var li = document.createElement('li');
                    li.style.fontSize = '.8rem';
                    li.style.color = '#6B778C';
                    li.innerHTML = '<span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:' + s.color + ';margin-right:6px;vertical-align:-1px;"></span>' +
                        s.label + ' &mdash; &pound;' + Number(s.value).toLocaleString('en-GB', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    ul.appendChild(li);
                });
                legendEl.appendChild(ul);
            }
        }
    });
</script>
@endpush

