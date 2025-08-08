<!-- resources/views/emails/meeting_invite.blade.php -->
@component('mail::message')
# Session Confirmed

Hi **{{ $meeting->client->name }}**,

Your coaching session is scheduled for  
**{{ $meeting->start_time->format('F j, Y \\a\\t g:i A') }}**.

@component('mail::button', ['url' => $meeting->join_url])
Join Zoom Meeting
@endcomponent

Thanks,  
{{ $meeting->coach->name }}
@endcomponent
