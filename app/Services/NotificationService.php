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

    public function complaintCreated($user, $complaint)
    {
        return $this->send(
            $user,
            'تم استلام الشكوى',
            'رقم الشكوى: ' . $complaint->tracking_number,
            [
                'type' => 'complaint_created',
                'complaint_id' => $complaint->id,
                'tracking_number' => $complaint->tracking_number,
            ]
        );
    }


    public function requestMoreInformation($user, $complaint)
    {
        return $this->send(
            $user,
            'مطلوب معلومات إضافية',
            'يرجى تزويدنا بمعلومات إضافية للشكوى رقم: ' . $complaint->tracking_number,
            [
                'type' => 'request_more_information',
                'complaint_id' => $complaint->id,
                'used_for_edit' => false
            ]
        );
    }

    public function noteAdded($user, $complaint)
    {
        return $this->send(
            $user,
            'تمت إضافة ملاحظة',
            'تمت إضافة ملاحظة على الشكوى رقم: ' . $complaint->tracking_number,
            [
                'type' => 'note_added',
                'complaint_id' => $complaint->id,
            ]
        );
    }

    public function statusUpdated($user, $complaint, $status)
    {
        return $this->send(
            $user,
            'تحديث حالة الشكوى',
            'تم تحديث حالة الشكوى رقم: ' . $complaint->tracking_number,
            [
                'type' => 'complaint_updated',
                'complaint_id' => $complaint->id,
                'new_status' => $status,
            ]
        );
    }


}
