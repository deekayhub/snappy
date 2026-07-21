@component('mail::message')
# Your Quote Has Been Marked as Completed!

Hi {{ $quote->supplier->name }},

Great news! The customer has marked your quote for **{{ $quote->job->title }}** as completed.

**Completed Work Summary:**
- Job: {{ $quote->job->title }}
- Your Price: ${{ number_format($quote->total_price, 2) }}
- Customer: {{ $quote->job->user->name }}

@component('mail::button', ['url' => route('supplier-panel.jobs')])
View Your Quotes
@endcomponent

Thank you for your excellent service! If the customer left a rating, you can view it in your dashboard.

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent
