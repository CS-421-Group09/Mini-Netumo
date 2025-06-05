<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\SlackMessage;

class AlertNotification extends Notification
{
    use Queueable;

    public $alert;

    /**
     * Create a new notification instance.
     */
    public function __construct($alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail'];
        if (!empty($notifiable->slack_webhook_url)) {
            $channels[] = 'slack';
        }
        // For custom webhooks (e.g., Discord), you can trigger in the Alert observer/model
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Netumo Alert: ' . ucfirst($this->alert->type))
            ->line($this->alert->message)
            ->line('Target: ' . $this->alert->target->name . ' (' . $this->alert->target->url . ')')
            ->line('Thank you for using Netumo!');
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->content('Netumo Alert: ' . $this->alert->message);
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
