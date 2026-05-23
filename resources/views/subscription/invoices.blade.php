@extends('layouts.app')
@section('title', 'Invoices')
@section('section')
    <div class="container py-5">
        <div class="mb-4">
            <a href="{{ route('subscription.index') }}" class="text-decoration-none mb-2 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i> Back to Subscription
            </a>
            <h1 class="fw-bold">Invoices</h1>
            <p class="text-muted">View and download your past invoices.</p>
        </div>

        @if($invoices->isEmpty())
            <div class="alert alert-info">
                No invoices available yet. Invoices will appear after your first subscription payment.
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                                    <td>{{ $invoice->total() }}</td>
                                    <td>
                                        @if($invoice->paid)
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-warning">Open</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('subscription.invoice.download', $invoice->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection