<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The labels shown in the mail for each changed profile field.
     *
     * @var array<string, string>
     */
    private const array FIELD_LABELS = [
        'name' => 'Name',
        'email' => 'Email address',
        'password' => 'Password',
    ];

    /**
     * Create a new notification instance.
     *
     * @param  list<string>  $changedFields
     */
    public function __construct(public array $changedFields) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your profile has been updated')
            ->markdown('mail.profile-updated', [
                'changedFieldLabels' => array_map(fn (string $field): string => self::FIELD_LABELS[$field], $this->changedFields),
                'url' => route('profile.edit'),
            ]);
    }
}
