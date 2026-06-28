@extends('supplier-panel.layouts.app')
@section('title', 'Analytics Dashboard')

@section('content')
<div class="content-wrapper p-3">
    <div class="mb-4">
        <h1 class="fw-bold mb-1">Advanced Analytics</h1>
        <p class="text-muted mb-0">Track your performance, win rates, and earnings over time.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-muted small">Total Quotes Submitted</div>
                    <div class="display-6 fw-bold">{{ $winRate?->total ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-muted small">Accepted + Completed</div>
                    <div class="display-6 fw-bold text-success">{{ ($winRate?->accepted ?? 0) + ($winRate?->completed ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-muted small">Total Earnings</div>
                    <div class="display-6 fw-bold text-primary">£{{ number_format((float) $totalEarnings, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h4 class="mb-3">Monthly Quote Submissions</h4>
                    <canvas id="monthlyChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h4 class="mb-3">Category Breakdown</h4>
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: @json($monthlyQuoteStats->pluck('month')),
                datasets: [{
                    label: 'Quotes',
                    data: @json($monthlyQuoteStats->pluck('total')),
                    backgroundColor: 'rgba(15, 118, 110, 0.7)',
                    borderRadius: 6,
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

    var categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: @json($categoryBreakdown->pluck('name')),
                datasets: [{
                    data: @json($categoryBreakdown->pluck('total')),
                    backgroundColor: ['#0f766e', '#0ea5e9', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6', '#f97316', '#6366f1'],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
});
</script>
@endpush
