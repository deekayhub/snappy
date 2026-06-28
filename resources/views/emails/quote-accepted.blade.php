@component('mail::message')
# Your Quote Has Been Accepted!

Hi {{ $quote->supplier->name }},

Congratulations! The customer has accepted your quote for **{{ $quote->job->title }}**.

**Quote Summary:**
- Job: {{ $quote->job->title }}
- Your Price: ${{ number_format($quote->total_price, 2) }}
- Customer: {{ $quote->job->user->name }}

@component('mail::button', ['url' => route('supplier-panel.quotes')])
View Your Quotes
@endcomponent

Please contact the customer to arrange the next steps.

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent
