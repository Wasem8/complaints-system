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
            'Complaint Received Successfully',
            'Ref:' . $complaint->tracking_number,
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
            'More Inforamtion Required',
            'Please Add More Info To Complaint With Ref:' . $complaint->tracking_number,
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
            'Note Added Successfully',
            'Note Added To Complaint With Ref: ' . $complaint->tracking_number,
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
            'Status Changed Successfully',
            'Stauts Changed to Complaint With Ref: ' . $complaint->tracking_number,
            [
                'type' => 'complaint_changed',
                'complaint_id' => $complaint->id,
                'new_status' => $status,
            ]
        );
    }


}
