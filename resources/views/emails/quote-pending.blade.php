@component('mail::message')
# Your Quote Has Been Marked as Pending

Hi {{ $quote->supplier->name }},

The customer has reviewed your quote for **{{ $quote->job->title }}** and moved it back to pending. They may still be considering their options.

**Quote Summary:**
- Job: {{ $quote->job->title }}
- Your Price: ${{ number_format($quote->total_price, 2) }}
- Customer: {{ $quote->job->user->name }}

@component('mail::button', ['url' => route('supplier-panel.quotes')])
View Your Quotes
@endcomponent

We'll notify you when the customer makes a decision.

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent
