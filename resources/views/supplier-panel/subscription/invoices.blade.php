@extends('supplier-panel.layouts.app')
@section('title', 'Invoices')

@section('content')
    <div class="content-wrapper p-3">
        <div class="mb-4">
            <a href="{{ route('supplier-panel.subscription.index') }}" class="text-decoration-none d-inline-flex align-items-center mb-2">
                <i class="mdi mdi-arrow-left me-1"></i> Back to Subscription
            </a>
            <h1 class="fw-bold mb-1">Invoices</h1>
            <p class="text-muted mb-0">View and download your subscription invoices.</p>
        </div>

        @if($invoices->isEmpty())
            <div class="alert alert-info border-0 shadow-sm rounded-4">
                No invoices available yet. Invoices will appear after your first subscription payment.
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
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
                                        <a href="{{ route('supplier-panel.subscription.invoice.download', $invoice->id) }}" class="btn btn-sm btn-outline-primary rounded-4">
                                            <i class="mdi mdi-download me-1"></i> Download
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
