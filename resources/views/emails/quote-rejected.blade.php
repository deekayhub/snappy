@component('mail::message')
# Your Quote Has Been Rejected

Hi {{ $quote->supplier->name }},

Unfortunately, the customer has rejected your quote for **{{ $quote->job->title }}**.

**Quote Summary:**
- Job: {{ $quote->job->title }}
- Your Price: ${{ number_format($quote->total_price, 2) }}
- Customer: {{ $quote->job->user->name }}

@component('mail::button', ['url' => route('supplier-panel.quotes')])
View Your Quotes
@endcomponent

Don't be discouraged — there are plenty of other opportunities available. Keep applying!

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent
