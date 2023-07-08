<?php

namespace App\Notifications\Email;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetEmail extends Notification implements ShouldQueue
{
    use Queueable;

    private $passwordReset;

    /**
     * NewUserWelcomeSms constructor.
     * @param $passwordReset
     */
    public function __construct($passwordReset)
    {
        $this->passwordReset = $passwordReset;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $template = EmailTemplate::where('name', 'password_reset')->get()->first();
        $subject = $template['subject'];
        $body = $template['body'];

        $body = str_replace('{password_reset_code}', $this->passwordReset->token, $body);

        return (new MailMessage)
            ->subject($subject)
            ->line($body);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
