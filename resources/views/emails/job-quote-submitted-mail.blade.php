<x-mail::message>
# New Quote Received

Hello {{ $job->user->name }},

Good news! A supplier has submitted a quote for your job.

### Job Details
- **Job Title:** {{ $job->title }}
- **Supplier:** {{ $quote->supplier->name }}
- **Quoted Price:**  £{{ number_format($quote->price_for_job, 2) }}
- **Delivery Cost:**  £{{ number_format($quote->delivery_cost, 2) }}
- **Discount Offered:**  £{{ number_format($quote->discount_offered, 2) }}
- **Total Price:**  £{{ number_format($quote->total_price, 2) }}

@if ($quote->notes)
### Supplier Notes

{{ $quote->notes }}
@endif

<x-mail::button :url="route('customer-panel.jobs')">
View Quote
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>