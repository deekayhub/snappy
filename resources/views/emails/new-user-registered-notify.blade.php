@component('mail::message')
# New {{ ucfirst($role) }} Registration

A new user has registered on {{ config('app.name') }}.

**Name:** {{ $user->name }}
**Email:** {{ $user->email }}
@if($user->phone)
**Phone:** {{ $user->phone }}
@endif
**Role:** {{ ucfirst($role) }}
**Registered:** {{ $user->created_at->format('d M Y h:i A') }}

@if($role === 'supplier' && $user->supplierProfile)
**Company:** {{ $user->supplierProfile->company_name }}
**Address:** {{ $user->supplierProfile->address }}
@if($user->supplierProfile->website)
**Website:** {{ $user->supplierProfile->website }}
@endif
@endif

@if($role === 'customer' && $user->customerProfile)
@if($user->customerProfile->school_name)
**School:** {{ $user->customerProfile->school_name }}
@endif
@if($user->customerProfile->county)
**County:** {{ $user->customerProfile->county }}
@endif
@endif

@component('mail::button', ['url' => config('app.url') . '/admin'])
View in Admin
@endcomponent

@endcomponent
