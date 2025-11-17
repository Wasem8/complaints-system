<?php

namespace App\Services;

use App\Models\RefreshToken;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenService
{
    public function createTokens($user, $device = null, $ip = null)
    {
        $accessToken = JWTAuth::fromUser($user);

        $raw = Str::random(64);

        $hash = hash('sha256', $raw);

        RefreshToken::where('user_id', $user->id)->delete();

        RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => $hash,
            'device' => $device,
            'ip' => $ip,
            'expires_at' => now()->addDays(30),
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $raw,
            'expires_in' => config('jwt.ttl') * 60,
        ];
    }

    public function refresh($rawToken)
    {
        $hash = hash('sha256', $rawToken);

        return DB::transaction(function () use ($hash) {

            $record = RefreshToken::where('token_hash', $hash)
                ->lockForUpdate()
                ->first();

            if (!$record)
                return null;

            if (now()->gt($record->expires_at)) {
                $record->delete();
                return null;
            }

            $user = $record->user;

            $record->delete();

            return $this->createTokens($user);
        });
    }

    public function revokeUserTokens($user)
    {
        RefreshToken::where('user_id', $user->id)->delete();

        if (JWTAuth::getToken())
            JWTAuth::invalidate(JWTAuth::getToken());
    }
}
