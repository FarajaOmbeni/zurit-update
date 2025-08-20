@component('mail::message')
# Your session is scheduled

Hi **{{ $meeting->coach->name }}**,

Your coaching session with **{{ $meeting->client->name }}** is scheduled for
**{{ $meeting->start_time->format('F j, Y \\a\\t g:i A') }}**.

@component('mail::button', ['url' => $meeting->start_url])
    Start Zoom Meeting
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
