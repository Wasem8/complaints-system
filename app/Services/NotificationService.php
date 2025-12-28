<?php

namespace App\Services;

use App\Notifications\FirebaseDatabaseNotification;

class NotificationService
{
    public function __construct(
        protected FirebaseService $firebase
    ) {}

    public function send(
        $user,
        string $title,
        string $body,
        array $data = []
    ) {
        // 1️⃣ تخزين الإشعار في قاعدة البيانات
        $user->notify(
            new FirebaseDatabaseNotification($title, $body, $data)
        );

        // 2️⃣ إرسال Firebase Push (إذا كان لديه token)
        if ($user->fcm_token) {
            $this->firebase->sendToToken(
                $user->fcm_token,
                $title,
                $body,
                $data
            );
        }
    }
}
