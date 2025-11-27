<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintNoteAdded extends Notification implements ShouldBroadcast
{
    use Queueable;

   protected Complaint $complaint;
   protected string $note;

    public function __construct(Complaint $complaint,string $note)
    {
        $this->complaint = $complaint;
        $this->note = $note;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database','broadcast','mail'];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.' . $this->complaint->user->id),
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('ملاحظة جديدة على الشكوى')
            ->line("تمت إضافة ملاحظة جديدة على الشكوى رقم #{$this->complaint->id}.")
            ->line("الملاحظة: {$this->note}")
            ->action('عرض الشكوى', url("/complaints/{$this->complaint->id}"))
            ->line('شكراً لاستخدامك نظامنا!');
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'complaint_id' => $this->complaint->id,
            'note'         => $this->note,
            'message'      => "تم إضافة ملاحظة جديدة على الشكوى.",
        ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'complaint_id' => $this->complaint->id,
            'note'         => $this->note,
            'message'      => "تم إضافة ملاحظة جديدة على الشكوى.",
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
