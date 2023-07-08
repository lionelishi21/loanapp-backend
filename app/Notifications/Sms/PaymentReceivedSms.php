<?php

namespace App\Notifications\Sms;

use App\Channels\AfricaTalkingChannel;
use App\Models\SmsTemplate;
use App\SmartMicro\Repositories\Eloquent\SmsSendRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentReceivedSms extends Notification implements ShouldQueue
{
    use Queueable;

    private $user, $loan, $payment, $smsSendRepository;

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
        return [AfricaTalkingChannel::class];
    }

    /**
     * Send Sms Via Africa Talking API
     * @param $notifiable
     * @throws \Exception
     */
    public function toATSms($notifiable)
    {
        $this->smsSendRepository = new SmsSendRepository();
        $phone = $notifiable['phone'];

        $template = SmsTemplate::where('name', 'payment_received')->get()->first();
        $body = $template['body'];

        $body = str_replace('{first_name}', $notifiable['first_name'], $body);
        $body = str_replace('{middle_name}', $notifiable['middle_name'], $body);
        $body = str_replace('{last_name}', $notifiable['last_name'], $body);
        $body = str_replace('{amount}', $this->payment->amount, $body);
        $body = str_replace('{payment_date}', $this->payment->payment_date, $body);
        $body = str_replace('{phone}',  $notifiable['phone'], $body);
        $body = str_replace('{receipt_number}', $this->payment->receipt_number, $body);

        $this->smsSendRepository->send($phone, $body);
    }

}
