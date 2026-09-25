<?php

namespace App\Notifications;

use App\Models\Idea;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IdeaPublished extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Idea $idea) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your idea has been published')
            ->markdown('mail.idea-published', [
                'idea' => $this->idea,
                'url' => route('ideas.show', $this->idea),
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array{idea_id: int, description: string, url: string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'idea_id' => $this->idea->id,
            'description' => $this->idea->description,
            'url' => route('ideas.show', $this->idea),
        ];
    }
}
