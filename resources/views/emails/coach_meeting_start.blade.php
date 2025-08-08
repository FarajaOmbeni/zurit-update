@component('mail::message')
    # Your session is scheduled

    Hi <b>{{ $meeting->coach->name }}</b>,

    Your coaching session with <b>{{ $meeting->client->name }}</b> is scheduled for
    <b>{{ $meeting->start_time->format('F j, Y \\a\\t g:i A') }}</b>.

    @component('mail::button', ['url' => $meeting->start_url])
        Start Zoom Meeting
    @endcomponent

    Thanks,
    {{ config('app.name') }}
@endcomponent
