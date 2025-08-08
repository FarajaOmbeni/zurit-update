<?php
// app/Services/ZoomService.php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ZoomService
{
    protected function accessToken(): string
    {
        $resp = Http::asForm()->post('https://zoom.us/oauth/token', [
            'grant_type'    => 'account_credentials',
            'account_id'    => config('services.zoom.account_id'),
        ])->withBasicAuth(
            config('services.zoom.client_id'),
            config('services.zoom.client_secret')
        );

        return $resp->json('access_token');
    }

    public function createMeeting(array $payload): array
    {
        $token = $this->accessToken();

        $response = Http::withToken($token)
            ->post(config('services.zoom.base') . '/users/me/meetings', $payload);

        return $response->json();
    }
}
