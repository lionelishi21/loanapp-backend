<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 14/11/2019
 * Time: 15:35
 */

namespace database\seeds;

use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsTemplateSeeder extends Seeder
{

    public function run()
    {
        DB::table('sms_templates')->delete();

        // 1.
        SmsTemplate::create([
            'name' => 'new_member_welcome',
            'display_name' => 'New Member Welcome',
            'body' => "Hi {first_name}, Welcome.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}"
        ]);

        // 2.
        SmsTemplate::create([
            'name' => 'new_user_welcome',
            'display_name' => 'New User Welcome',
            'body' => "Hi {first_name}, Welcome.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}"
        ]);

        // 3.
        SmsTemplate::create([
            'name' => 'password_reset',
            'display_name' => 'Password Reset',
            'body' => "We received a request to reset your password. Below is a code to confirm this reset. {password_reset_code}",
            'tags' => "{first_name}"
        ]);

        // 4.
        SmsTemplate::create([
            'name' => 'new_loan_application',
            'display_name' => 'New Loan Application',
            'body' => "We have received  your loan application. Our staff will review and communicate.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}, {amount_applied}, {repayment_period}, {loan_type}, {interest_rate}"
        ]);

        // 5.
        SmsTemplate::create([
            'name' => 'loan_application_approved',
            'display_name' => 'Loan Application Approved',
            'body' => "Congratulations. Your loan Application has been approved.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}, {amount_applied}, {loan_reference_number}, {repayment_period}, {start_date}, {interest_rate}, {loan_type}"
        ]);

        // 6.
        SmsTemplate::create([
            'name' => 'loan_application_rejected',
            'display_name' => 'Loan Application Rejected',
            'body' => "Unfortunately. Your Loan Application has been rejected.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}, {amount_applied}, {rejection_notes}"
        ]);

        // 7.
        SmsTemplate::create([
            'name' => 'payment_received',
            'display_name' => 'Payment Received',
            'body' => "We have received your payment of {amount}. Thank you.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {amount}, {payment_date}, {phone}, {receipt_number}"
        ]);

        // 8.
        SmsTemplate::create([
            'name' => 'system_summary',
            'display_name' => 'System Summary',
            'body' => "System Summary",
            'tags' => ""
        ]);

    }

}