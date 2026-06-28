@extends('supplier-panel.layouts.app')
@section('title', 'Reports')

@section('content')
<div class="content-wrapper p-3">
    <div class="mb-4">
        <h1 class="fw-bold mb-1 fs-2">Reports</h1>
        <p class="text-muted mb-0">Market demand, your performance, and quote analytics.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="mdi mdi-briefcase text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Jobs</div>
                            <div class="h3 fw-bold mb-0">{{ $totalJobs }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="mdi mdi-calendar-clock text-success" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Jobs (30 days)</div>
                            <div class="h3 fw-bold mb-0">{{ $recentJobsCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="mdi mdi-send text-info" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <div class="text-muted small">My Quotes</div>
                            <div class="h3 fw-bold mb-0">{{ $myQuoteStats['total'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="mdi mdi-chart-line text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Win Rate</div>
                            <div class="h3 fw-bold mb-0">
                                @php($won = $myQuoteStats['accepted'] + $myQuoteStats['completed'])
                                {{ $myQuoteStats['total'] > 0 ? round(($won / $myQuoteStats['total']) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Quoted jobs</h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="trendToggle" id="trendMonthly" value="monthly" checked>
                            <label class="btn btn-outline-primary rounded-start-4" for="trendMonthly">Monthly</label>
                            <input type="radio" class="btn-check" name="trendToggle" id="trendYearly" value="yearly">
                            <label class="btn btn-outline-primary rounded-end-4" for="trendYearly">Yearly</label>
                        </div>
                    </div>
                    <canvas id="monthlyTrendChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3">My quote status</h5>
                    <canvas id="quoteStatusChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3">Jobs by category</h5>
                    <div class="d-flex flex-column gap-2">
                        @forelse ($jobsByCategory as $item)
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-capitalize">{{ $item->name }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width: 100px; height: 6px;">
                                        @php($pct = $totalJobs > 0 ? round(($item->total / $totalJobs) * 100) : 0)
                                        <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="badge bg-light text-dark rounded-pill">{{ $item->total }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No data yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3">Jobs by location</h5>
                    <div class="d-flex flex-column gap-2">
                        @forelse ($jobsByLocation as $item)
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">{{ $item->location_name }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width: 100px; height: 6px;">
                                        @php($pct = $totalJobs > 0 ? round(($item->total / $totalJobs) * 100) : 0)
                                        <div class="progress-bar bg-success rounded-pill" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="badge bg-light text-dark rounded-pill">{{ $item->total }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No data yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3">Budget distribution</h5>
                    <div class="d-flex flex-column gap-2">
                        @foreach ($budgetRanges as $label => $count)
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">{{ $label }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width: 100px; height: 6px;">
                                        @php($pct = $totalJobs > 0 ? round(($count / $totalJobs) * 100) : 0)
                                        <div class="progress-bar bg-warning rounded-pill" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="badge bg-light text-dark rounded-pill">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($topJobCategories->isNotEmpty())
    <div class="row g-4 mt-2">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">My top categories by quotes</h5>
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr class="small text-muted">
                                <th>Category</th>
                                <th class="text-end">Quotes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topJobCategories as $item)
                                <tr>
                                    <td class="text-capitalize">{{ $item->name }}</td>
                                    <td class="text-end fw-semibold">{{ $item->quote_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var trendCtx = document.getElementById('monthlyTrendChart');
    var trendChart;
    if (trendCtx) {
        var monthlyLabels = @json($monthlyTrend->pluck('month'));
        var monthlyData = @json($monthlyTrend->pluck('total'));
        var yearlyLabels = @json($yearlyTrend->pluck('year'));
        var yearlyData = @json($yearlyTrend->pluck('total'));

        function updateTrendChart(period) {
            var labels = period === 'monthly' ? monthlyLabels : yearlyLabels;
            var data = period === 'monthly' ? monthlyData : yearlyData;

            if (trendChart) {
                trendChart.data.labels = labels;
                trendChart.data.datasets[0].data = data;
                trendChart.update();
            } else {
                trendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jobs',
                            data: data,
                            borderColor: '#0f766e',
                            backgroundColor: 'rgba(15, 118, 110, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            }
        }

        updateTrendChart('monthly');

        document.querySelectorAll('input[name="trendToggle"]').forEach(function (el) {
            el.addEventListener('change', function () {
                if (this.checked) {
                    updateTrendChart(this.value);
                }
            });
        });
    }

    var statusCtx = document.getElementById('quoteStatusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json(array_column($myQuoteStatusChart, 'status')),
                datasets: [{
                    data: @json(array_column($myQuoteStatusChart, 'count')),
                    backgroundColor: @json(array_column($myQuoteStatusChart, 'color')),
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 12 }
                    }
                },
                cutout: '65%',
            }
        });
    }
});
</script>
@endpush
