<?php

namespace App\Notifications\Sms;

use App\Channels\AfricaTalkingChannel;
use App\Models\SmsTemplate;
use App\SmartMicro\Repositories\Eloquent\SmsSendRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewUserWelcomeSms extends Notification implements ShouldQueue
{
    use Queueable;

    private $member, $loanApplication, $smsSendRepository;

    /**
     * NewMemberWelcomeSms constructor.
     * @param $member
     */
    public function __construct($member)
    {
        $this->member = $member;
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

        $template = SmsTemplate::where('name', 'new_user_welcome')->get()->first();
        $body = $template['body'];

        $body = str_replace('{first_name}', $notifiable['first_name'], $body);
        $body = str_replace('{middle_name}', $notifiable['middle_name'], $body);
        $body = str_replace('{last_name}', $notifiable['last_name'], $body);
        $body = str_replace('{phone}',  $notifiable['phone'], $body);

        $this->smsSendRepository->send($phone, $body);
    }
}
