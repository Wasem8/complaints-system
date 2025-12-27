<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class FcmService
{
    private function getAccessToken(): string
    {
        $client = new GoogleClient();
        $client->setAuthConfig(config('services.firebase.credentials'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        return $client->fetchAccessTokenWithAssertion()['access_token'];
    }

    public function sendNotificationToUser($user, string $title, string $body, array $data = [])
    {

        if (!$user->fcm_token) {
            return null;
        }

        return $this->sendNotification(
            $user->fcm_token,
            $title,
            $body,
            $data
        );
    }

    public function sendNotification(string $token, string $title, string $body, array $data = [])
    {
        $response = Http::withToken($this->getAccessToken())
            ->post(
                'https://fcm.googleapis.com/v1/projects/' .
                config('services.firebase.project_id') .
                '/messages:send',
                [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => $data,
                    ],
                ]
            );

        if ($response->failed()) {
            $res = $response->json();
            logger()->error('FCM failed', $res);

            if (
                isset($res['error']['status']) &&
                in_array($res['error']['status'], ['NOT_FOUND', 'UNREGISTERED'])
            ) {
                \App\Models\User::where('fcm_token', $token)
                    ->update(['fcm_token' => null]);
            }
        } else {
            logger()->info('FCM success', $response->json());
        }

        return $response->json();
    }
}
