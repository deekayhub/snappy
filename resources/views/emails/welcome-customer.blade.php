@component('mail::message')
# Welcome to SnappyQuotes, {{ $user->name }}!

Thank you for registering as a customer. You can now post jobs and receive quotes from verified suppliers.

@component('mail::button', ['url' => route('customer.jobs.create')])
Post a Job
@endcomponent

Once you post a job, suppliers in your category will start submitting quotes. Compare offers, choose the best one, and get the job done.

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent
