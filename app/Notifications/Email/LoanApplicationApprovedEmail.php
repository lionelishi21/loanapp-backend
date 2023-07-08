<?php

namespace App\Notifications\Email;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanApplicationApprovedEmail extends Notification implements ShouldQueue
{
    use Queueable;

    private $loanApplication;

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
        $template = EmailTemplate::where('name', 'loan_application_approved')->get()->first();
        $subject = $template['subject'];
        $body = $template['body'];

        $subject = str_replace('{first_name}', $notifiable['first_name'], $subject);
        $subject = str_replace('{middle_name}', $notifiable['middle_name'], $subject);
        $subject = str_replace('{last_name}', $notifiable['last_name'], $subject);
        $subject = str_replace('{phone}',  $notifiable['phone'], $subject);
        $subject = str_replace('{amount_applied}',  $this->loanApplication->amount_applied, $subject);
        $subject = str_replace('{loan_reference_number}',  $this->loanApplication->loan_reference_number, $subject);
        $subject = str_replace('{repayment_period}',  $this->loanApplication->repayment_period, $subject);
        $subject = str_replace('{start_date}',  $this->loanApplication->start_date, $subject);
        $subject = str_replace('{repayment_period}',  $this->loanApplication->repayment_period, $subject);
        $subject = str_replace('{loan_type}',  $this->loanApplication->loan_type, $subject);
        $subject = str_replace('{interest_rate}',  $this->loanApplication->interest_rate, $subject);

        $body = str_replace('{first_name}', $notifiable['first_name'], $body);
        $body = str_replace('{middle_name}', $notifiable['middle_name'], $body);
        $body = str_replace('{last_name}', $notifiable['last_name'], $body);
        $body = str_replace('{phone}',  $notifiable['phone'], $body);
        $body = str_replace('{amount_applied}',  $this->loanApplication->amount_applied, $body);
        $body = str_replace('{loan_reference_number}',  $this->loanApplication->loan_reference_number, $body);
        $body = str_replace('{repayment_period}',  $this->loanApplication->repayment_period, $body);
        $body = str_replace('{start_date}',  $this->loanApplication->start_date, $body);
        $body = str_replace('{repayment_period}',  $this->loanApplication->repayment_period, $body);
        $body = str_replace('{loan_type}',  $this->loanApplication->loan_type, $body);
        $body = str_replace('{interest_rate}',  $this->loanApplication->interest_rate, $body);

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
