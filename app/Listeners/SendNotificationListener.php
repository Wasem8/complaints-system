<?php

namespace App\Listeners;

use App\Events\SendNotification;
use App\Models\FcmToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
class SendNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SendNotification $event): void
    {
        $tokens = FcmToken::where('user_id', $event->userId)->get();

        if ($tokens->isEmpty()) {
            return;
        }

        foreach ($tokens as $token) {
            try {
                app(FirebaseService::class)->send(
                    token: $token->token,
                    title: $event->title,
                    body: $event->body,
                    data: $event->data ?? []
                );
            } catch (\Throwable $e) {
                // token غير صالح
                $token->delete();
            }
        }
    }
}
