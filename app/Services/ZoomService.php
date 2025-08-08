<?php
// app/Services/ZoomService.php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ZoomService
{
    protected function accessToken(): string
    {
        $response = Http::asForm()
            ->withBasicAuth(
                config('services.zoom.client_id'),
                config('services.zoom.client_secret')
            )
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => config('services.zoom.account_id'),
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Unable to obtain Zoom access token: ' . $response->body());
        }

        return (string) $response->json('access_token');
    }

    public function createMeeting(array $payload): array
    {
        $token = $this->accessToken();

        $response = Http::withToken($token)
            ->post(config('services.zoom.base') . '/users/me/meetings', $payload);

        if ($response->failed()) {
            throw new \RuntimeException('Zoom meeting creation failed: ' . $response->body());
        }

        return $response->json();
    }
}
