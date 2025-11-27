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

class ComplaintMoreInfoRequested extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected Complaint $complaint;
    protected string $message;
    /**
     * Create a new notification instance.
     */
    public function __construct(Complaint $complaint, string $message)
    {
        $this->complaint = $complaint;
        $this->message = $message;
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
            ->subject('مطلوب معلومات إضافية حول الشكوى')
            ->line("تم طلب معلومات إضافية حول الشكوى رقم #{$this->complaint->id}.")
            ->line("الرسالة: {$this->message}")
            ->action('عرض الشكوى', url("/complaints/{$this->complaint->id}"))
            ->line('يرجى استكمال المعلومات المطلوبة في أسرع وقت.');
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'complaint_id' => $this->complaint->id,
            'message'      => $this->message,
            'status'       => 'need_more_info',
            'text'         => 'تم طلب معلومات إضافية للشكوى',
        ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'complaint_id' => $this->complaint->id,
            'message'      => $this->message,
            'status'       => 'need_more_info',
            'text'         => 'تم طلب معلومات إضافية للشكوى',
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
