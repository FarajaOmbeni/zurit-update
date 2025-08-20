<?php

namespace App\Services;

use App\Models\Meeting;
use Carbon\Carbon;

class IcsService
{
    public function generateIcsFile(Meeting $meeting): string
    {
        $startTime = Carbon::parse($meeting->start_time);
        $endTime = $startTime->copy()->addHour(); // Assuming 1-hour meetings

        $summary = "Coaching Session with " . $meeting->coach->name;
        $description = "Your coaching session with " . $meeting->coach->name . "\\n\\nJoin URL: " . $meeting->join_url;
        $location = "Zoom Meeting";

        // Format dates for ICS (UTC format)
        $dtStart = $startTime->utc()->format('Ymd\THis\Z');
        $dtEnd = $endTime->utc()->format('Ymd\THis\Z');
        $dtStamp = now()->utc()->format('Ymd\THis\Z');

        // Generate unique UID
        $uid = 'meeting-' . $meeting->id . '@' . config('app.url');

        $icsContent = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//' . config('app.name') . '//NONSGML Event//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:REQUEST',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . $dtStamp,
            'DTSTART:' . $dtStart,
            'DTEND:' . $dtEnd,
            'SUMMARY:' . $this->escapeIcsString($summary),
            'DESCRIPTION:' . $this->escapeIcsString($description),
            'LOCATION:' . $this->escapeIcsString($location),
            'STATUS:CONFIRMED',
            'SEQUENCE:0',
            'BEGIN:VALARM',
            'TRIGGER:-PT15M',
            'ACTION:DISPLAY',
            'DESCRIPTION:Coaching session reminder',
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR'
        ];

        return implode("\r\n", $icsContent);
    }

    public function generateIcsFileForCoach(Meeting $meeting): string
    {
        $startTime = Carbon::parse($meeting->start_time);
        $endTime = $startTime->copy()->addHour();

        $summary = "Coaching Session with " . $meeting->client->name;
        $description = "Your coaching session with " . $meeting->client->name . "\\n\\nStart URL: " . $meeting->start_url;
        $location = "Zoom Meeting";

        // Format dates for ICS (UTC format)
        $dtStart = $startTime->utc()->format('Ymd\THis\Z');
        $dtEnd = $endTime->utc()->format('Ymd\THis\Z');
        $dtStamp = now()->utc()->format('Ymd\THis\Z');

        // Generate unique UID
        $uid = 'meeting-coach-' . $meeting->id . '@' . config('app.url');

        $icsContent = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//' . config('app.name') . '//NONSGML Event//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:REQUEST',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . $dtStamp,
            'DTSTART:' . $dtStart,
            'DTEND:' . $dtEnd,
            'SUMMARY:' . $this->escapeIcsString($summary),
            'DESCRIPTION:' . $this->escapeIcsString($description),
            'LOCATION:' . $this->escapeIcsString($location),
            'STATUS:CONFIRMED',
            'SEQUENCE:0',
            'BEGIN:VALARM',
            'TRIGGER:-PT15M',
            'ACTION:DISPLAY',
            'DESCRIPTION:Coaching session reminder',
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR'
        ];

        return implode("\r\n", $icsContent);
    }

    private function escapeIcsString(string $string): string
    {
        // Escape special characters for ICS format
        $string = str_replace(['\\', ';', ',', "\n", "\r"], ['\\\\', '\\;', '\\,', '\\n', ''], $string);

        // Fold long lines (ICS specification requires lines to be max 75 octets)
        if (strlen($string) > 75) {
            $string = wordwrap($string, 73, "\r\n ", true);
        }

        return $string;
    }
}
