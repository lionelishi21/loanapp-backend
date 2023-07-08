<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/11/2019
 * Time: 09:23
 */

namespace App\Traits;


use App\Models\CommunicationSetting;
use App\Notifications\Email\LoanApplicationApprovedEmail;
use App\Notifications\Email\LoanApplicationRejectedEmail;
use App\Notifications\Email\NewLoanApplicationEmail;
use App\Notifications\Email\NewMemberWelcomeEmail;
use App\Notifications\Email\NewUserWelcomeEmail;
use App\Notifications\Email\PasswordResetEmail;
use App\Notifications\Email\PaymentReceivedEmail;
use App\Notifications\Email\SystemSummaryEmail;
use App\Notifications\Sms\LoanApplicationApprovedSms;
use App\Notifications\Sms\LoanApplicationRejectedSms;
use App\Notifications\Sms\NewLoanApplicationSms;
use App\Notifications\Sms\NewMemberWelcomeSms;
use App\Notifications\Sms\NewUserWelcomeSms;
use App\Notifications\Sms\PasswordResetSms;
use App\Notifications\Sms\PaymentReceivedSms;
use App\Notifications\Sms\SystemSummarySms;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

trait CommunicationMessage
{
    public static function send($event, $notifiable, $data) {

        try{
            switch ($event){
            case 'new_member_welcome':{
                $setting = CommunicationSetting::where('name', 'new_member_welcome')->first();

                if(!is_null($setting) && $setting->email_template){
                    Notification::send($notifiable, new NewMemberWelcomeEmail($data));
                }
                if(!is_null($setting) && $setting->sms_template){
                    Notification::send($notifiable, new NewMemberWelcomeSms($data));
                }
            }
                break;
            case 'new_user_welcome':{
                $setting = CommunicationSetting::where('name', 'new_user_welcome')->first();

                if(!is_null($setting) && $setting->email_template){
                        Notification::send($notifiable, new NewUserWelcomeEmail($data));
                }
                if(!is_null($setting) && $setting->sms_template){
                    Notification::send($notifiable, new NewUserWelcomeSms($data));
                }
            }
                break;
            case 'reset_password':{
                $setting = CommunicationSetting::where('name', 'reset_password')->first();

                if(!is_null($setting) && $setting->email_template){
                    Notification::send($notifiable, new PasswordResetEmail($data));
                }
                if(!is_null($setting) && $setting->sms_template){
                    Notification::send($notifiable, new PasswordResetSms());
                }
            }
                break;
            case 'new_loan_application':{
                $setting = CommunicationSetting::where('name', 'new_loan_application')->first();

                if(!is_null($setting) && $setting->email_template){
                    Notification::send($notifiable, new NewLoanApplicationEmail($data));
                }
                if(!is_null($setting) && $setting->sms_template){
                    Notification::send($notifiable, new NewLoanApplicationSms($data));
                }
            }
                break;
            case 'loan_application_approved':{
                $setting = CommunicationSetting::where('name', 'loan_application_approved')->first();

                if(!is_null($setting) && $setting->email_template){
                    Notification::send($notifiable, new LoanApplicationApprovedEmail($data));
                }
                if(!is_null($setting) && $setting->sms_template){
                    Notification::send($notifiable, new LoanApplicationApprovedSms($data));
                }
            }
                break;
            case 'loan_application_rejected':{
                $setting = CommunicationSetting::where('name', 'loan_application_rejected')->first();

                if(!is_null($setting) && $setting->email_template){
                    Notification::send($notifiable, new LoanApplicationRejectedEmail($data));
                }
                if(!is_null($setting) && $setting->sms_template){
                    Notification::send($notifiable, new LoanApplicationRejectedSms($data));
                }
            }
                break;
            case 'payment_received':{
                $setting = CommunicationSetting::where('name', 'payment_received')->first();

                if(!is_null($setting) && $setting->email_template){
                    Notification::send($notifiable, new PaymentReceivedEmail($data));
                }
                if(!is_null($setting) && $setting->sms_template){
                    Notification::send($notifiable, new PaymentReceivedSms($data));
                }
            }
                break;
            case 'system_summary':{
                $setting = CommunicationSetting::where('name', 'system_summary')->first();

                if(!is_null($setting) && $setting->email_template){
                    Notification::send($notifiable, new SystemSummaryEmail($data));
                }
                if(!is_null($setting) && $setting->sms_template){
                    Notification::send($notifiable, new SystemSummarySms($data));
                }
            }
                break;
            default: {
            }
        }
        }
        catch (\Exception $exception){
            Log::info($exception->getMessage());
        }
    }
}