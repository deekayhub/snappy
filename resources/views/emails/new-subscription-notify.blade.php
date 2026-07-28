@component('mail::message')
# New Subscription

A user has subscribed to a plan on {{ config('app.name') }}.

**User:** {{ $user->name }}
**Email:** {{ $user->email }}
**Role:** {{ ucfirst($user->getRoleNames()->first() ?? 'N/A') }}
**Plan:** {{ $planName }}
**Amount:** {{ $amount }}
**Billing:** {{ $billingPeriod }}

@component('mail::button', ['url' => config('app.url') . '/admin'])
View in Admin
@endcomponent

@endcomponent
