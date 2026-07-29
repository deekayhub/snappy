<x-mail::message>
# New Quote Received

Hello {{ $job->user->name }},

Good news! A supplier has submitted a quote for your job.

### Job Details
- **Job Title:** {{ $job->title }}
- **Job Description:** {{ $job->description }}
- **Job Location:** {{ $job->location }}
- **Job Budget:** £{{ number_format($job->budget, 2) }}
- **Job Deadline:** {{ $job->deadline->format('d M Y') }}

### Supplier Details
- **Name:** {{ $quote->supplier->name }}
@if ($quote->supplier->supplierProfile?->company_name)
- **Company:** {{ $quote->supplier->supplierProfile->company_name }}
@endif
- **Email:** {{ $quote->supplier->email }}
@if ($quote->supplier->phone)
- **Phone:** {{ $quote->supplier->phone }}
@endif
@if ($quote->supplier->supplierProfile?->website)
- **Website:** {{ $quote->supplier->supplierProfile->website }}
@endif
@if ($quote->supplier->supplierProfile?->address)
- **Address:** {{ $quote->supplier->supplierProfile->address }}
@endif
#### Quote Details
- **Quoted Price:**  £{{ number_format($quote->price_for_job, 2) }}
- **Delivery Cost:**  £{{ number_format($quote->delivery_cost, 2) }}
- **Discount Offered:**  £{{ number_format($quote->discount_offered, 2) }}
- **Total Price:**  £{{ number_format($quote->total_price, 2) }}

@if ($quote->notes)
### Supplier Notes

{{ $quote->notes }}
@endif

@if ($quote->product_image || $quote->product_link)
### Product Details

@if ($quote->product_image)
![Product Image]({{ asset($quote->product_image) }})
@endif
@if ($quote->product_link)
- **Product Link:** {{ $quote->product_link }}
@endif
@endif

<x-mail::button :url="route('customer-panel.jobs')">
View Quote
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>