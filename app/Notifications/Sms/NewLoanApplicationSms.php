<?php

namespace App\Notifications\Sms;

use App\Channels\AfricaTalkingChannel;
use App\Models\SmsTemplate;
use App\SmartMicro\Repositories\Eloquent\SmsSendRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewLoanApplicationSms extends Notification implements ShouldQueue
{
    use Queueable;

    private $loanApplication, $smsSendRepository;

    /**
     * LoanApplicationApprovedEmail constructor.
     * @param $loanApplication
     */
    public function __construct($loanApplication)
    {
        $this->loanApplication = $loanApplication;
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

        $template = SmsTemplate::where('name', 'new_loan_application')->get()->first();
        $body = $template['body'];

        $body = str_replace('{first_name}', $notifiable['first_name'], $body);
        $body = str_replace('{middle_name}', $notifiable['middle_name'], $body);
        $body = str_replace('{last_name}', $notifiable['last_name'], $body);
        $body = str_replace('{phone}',  $notifiable['phone'], $body);

        $body = str_replace('{amount_applied}',  $notifiable['amount_applied'], $body);
        $body = str_replace('{repayment_period}',  $notifiable['repayment_period'], $body);
        $body = str_replace('{loan_type}',  $notifiable['loan_type'], $body);
        $body = str_replace('{interest_rate}',  $notifiable['interest_rate'], $body);

        $this->smsSendRepository->send($phone, $body);
    }
}
