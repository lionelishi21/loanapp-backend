<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 29/11/2019
 * Time: 23:17
 */

namespace App\Notifications\Sms;

use App\Channels\AfricaTalkingChannel;
use App\Models\SmsTemplate;
use App\SmartMicro\Repositories\Eloquent\SmsSendRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetSms extends Notification implements ShouldQueue
{
    use Queueable;

    private $member, $loanApplication, $smsSendRepository;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
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

        $template = SmsTemplate::where('name', 'password_reset')->get()->first();
        $body = $template['body'];

        $body = str_replace('{first_name}', $notifiable['first_name'], $body);

        $this->smsSendRepository->send($phone, $body);
    }

}
