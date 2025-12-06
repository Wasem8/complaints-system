<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintStatusUpdated extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    protected Complaint $complaint;
    protected string $newStatus;


    public function __construct(Complaint $complaint, string $newStatus)
    {
        $this->complaint = $complaint;
        $this->newStatus = $newStatus;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.' . $this->complaint->user->id),
        ];
    }




    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تم تحديث حالة الشكوى')
            ->line("تم تغيير حالة الشكوى #{$this->complaint->id} إلى: {$this->newStatus}")
            ->action('عرض الشكوى', url("/complaints/{$this->complaint->id}"))
            ->line('شكراً لاستخدامك نظامنا!');
    }



    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'complaint_id' => $this->complaint->id,
            'new_status'   => $this->newStatus,
            'message'      => "تم تحديث حالة الشكوى إلى: {$this->newStatus}",
        ]);
    }


    public function toDatabase(object $notifiable): array
    {
        return [
            'complaint_id' => $this->complaint->id,
            'new_status'   => $this->newStatus,
            'message'      => "تم تحديث حالة الشكوى إلى: {$this->newStatus}",
        ];
    }
}
