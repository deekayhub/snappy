@extends('supplier-panel.layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="content-wrapper p-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Notifications</h1>
            <p class="text-muted mb-0">Instant job alerts and activity updates.</p>
        </div>
        <form method="POST" action="{{ route('supplier-panel.notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="btn btn-outline-primary rounded-4">Mark all as read</button>
        </form>
    </div>

    @forelse ($notifications as $notification)
        <div class="card border-0 shadow-sm rounded-4 mb-3 {{ $notification->is_read ? '' : 'border-start border-primary border-4' }}">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">{{ $notification->message }}</div>
                    <div class="small text-muted">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
                <div class="d-flex gap-2">
                    @if($notification->action_url)
                        <form method="POST" action="{{ route('supplier-panel.notifications.read', $notification) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary rounded-4">View</button>
                        </form>
                    @endif
                    @if(!$notification->is_read)
                        <form method="POST" action="{{ route('supplier-panel.notifications.read', $notification) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light rounded-4">Mark read</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-light border rounded-4">No notifications yet.</div>
    @endforelse

    <div class="mt-4">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
