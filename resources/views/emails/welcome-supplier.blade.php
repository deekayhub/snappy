@component('mail::message')
# Welcome to SnappyQuotes, {{ $user->name }}!

Thank you for registering as a supplier. You now have access to:

- Browse and search available jobs
- Submit quotes to potential customers
- Manage your supplier profile
- Track your quote activity

@component('mail::button', ['url' => route('supplier-panel.dashboard')])
Go to Dashboard
@endcomponent

To get the most out of SnappyQuotes, consider completing your supplier profile and setting up your organisation categories.

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent
