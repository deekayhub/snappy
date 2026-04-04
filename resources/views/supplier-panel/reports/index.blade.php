@extends('supplier-panel.layouts.app')
@section('title', 'Supplier Reports')

@section('content')
<div class="content-wrapper p-3">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h4 class="mb-3">Demand by category</h4>
                    <div class="list-group list-group-flush">
                        @forelse ($jobsByCategory as $item)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span>{{ $item->category_name }}</span>
                                <span class="badge bg-primary rounded-pill">{{ $item->total }}</span>
                            </div>
                        @empty
                            <div class="text-muted">No category data yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h4 class="mb-3">Demand by location</h4>
                    <div class="list-group list-group-flush">
                        @forelse ($jobsByLocation as $item)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span>{{ $item->location_name }}</span>
                                <span class="badge bg-success rounded-pill">{{ $item->total }}</span>
                            </div>
                        @empty
                            <div class="text-muted">No location data yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
