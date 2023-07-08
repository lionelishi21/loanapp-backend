<?php

namespace App\Notifications\Sms;

use App\Channels\AfricaTalkingChannel;
use App\Models\SmsTemplate;
use App\SmartMicro\Repositories\Eloquent\SmsSendRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SystemSummarySms extends Notification implements ShouldQueue
{
    use Queueable;

    private $summary;

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

        $template = SmsTemplate::where('name', 'system_summary')->get()->first();
        $body = $template['body'];


        $this->smsSendRepository->send($phone, $body);
    }
}
