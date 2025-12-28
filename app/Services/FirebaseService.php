<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected string $projectId;
    protected string $credentialsPath;
    protected string $scope;
    protected string $oauthUrl;
    protected string $fcmUrl;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        $this->credentialsPath = config('firebase.credentials');
        $this->scope = config('firebase.scope');
        $this->oauthUrl = config('firebase.oauth_token_url');
        $this->fcmUrl = config('firebase.fcm_url');
    }

    /**
     * Generate OAuth2 Access Token
     */
    protected function getAccessToken(): string
    {
        $credentials = json_decode(
            file_get_contents($this->credentialsPath),
            true
        );

        $now = time();

        $base64UrlEncode = fn ($data) =>
        rtrim(strtr(base64_encode($data), '+/', '-_'), '=');

        $header = $base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $claim = $base64UrlEncode(json_encode([
            'iss'   => $credentials['client_email'],
            'scope' => $this->scope,
            'aud'   => $this->oauthUrl,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signatureInput = "$header.$claim";

        openssl_sign(
            $signatureInput,
            $signature,
            $credentials['private_key'],
            'sha256'
        );

        $jwt = $signatureInput . '.' . $base64UrlEncode($signature);

        $response = Http::asForm()->post($this->oauthUrl, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        return $response->json('access_token');
    }

    /**
     * Send Push Notification
     */
    public function sendToToken(
        string $token,
        string $title,
        string $body,
        array $data = []
    ) {
        $accessToken = $this->getAccessToken();

        // 🔴 Firebase requires all data values to be strings
        $data = collect($data)
            ->map(fn ($value) => (string) $value)
            ->toArray();

        $url = str_replace(
            ':project_id',
            $this->projectId,
            $this->fcmUrl
        );

        try {
            $response = Http::withToken($accessToken)
                ->post($url, [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'data' => $data,
                    ],
                ]);

            \Log::info('FCM PUSH RESPONSE', [
                'status' => $response->status(),
                'success' => $response->successful(),
                'token' => substr($token, 0, 15) . '...',
                'payload_data' => $data,
                'response' => $response->json(),
            ]);

            return $response;

        } catch (\Throwable $e) {

            \Log::error('FCM PUSH EXCEPTION', [
                'message' => $e->getMessage(),
                'token' => substr($token, 0, 15) . '...',
            ]);

            throw $e;
        }
    }


}
