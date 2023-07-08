<?php

namespace App\Notifications\Email;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReceivedEmail extends Notification implements ShouldQueue
{
    use Queueable;

    private $loan, $payment;

    /**
     * Create a new notification instance.
     * PaymentReceivedNotification constructor.
     * @param $payment
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $template = EmailTemplate::where('name', 'payment_received')->get()->first();
        $subject = $template['subject'];
        $body = $template['body'];

        $subject = str_replace('{first_name}', $notifiable['first_name'], $subject);
        $subject = str_replace('{middle_name}', $notifiable['middle_name'], $subject);
        $subject = str_replace('{last_name}', $notifiable['last_name'], $subject);
        $subject = str_replace('{amount}', $this->payment->amount, $subject);
        $subject = str_replace('{payment_date}', $this->payment->payment_date, $subject);
        $subject = str_replace('{phone}',  $notifiable['phone'], $subject);
        $subject = str_replace('{receipt_number}', $this->payment->receipt_number, $subject);

        $body = str_replace('{first_name}', $notifiable['first_name'], $body);
        $body = str_replace('{middle_name}', $notifiable['middle_name'], $body);
        $body = str_replace('{last_name}', $notifiable['last_name'], $body);
        $body = str_replace('{amount}', $this->payment->amount, $body);
        $body = str_replace('{payment_date}', $this->payment->payment_date, $body);
        $body = str_replace('{phone}',  $notifiable['phone'], $body);
        $body = str_replace('{receipt_number}', $this->payment->receipt_number, $body);

        return (new MailMessage)
            ->subject($subject) // it will use this class name if you don't specify
            ->greeting('') // example: Dear Sir, Hello Madam, etc ...
            ->level('info')// It is kind of email. Available options: info, success, error. Default: info
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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'first_name' => $notifiable['first_name']
        ];
    }
}
