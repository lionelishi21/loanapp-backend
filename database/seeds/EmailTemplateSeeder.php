<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 14/11/2019
 * Time: 15:33
 */

namespace database\seeds;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{

    public function run()
    {
        DB::table('email_templates')->delete();

        // 1.
        EmailTemplate::create([
            'name' => 'new_member_welcome',
            'display_name' => 'New Member Welcome',
            'subject' => 'New member Welcome',
            'body' => "Hi {first_name}, Welcome.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}"
        ]);

        // 2.
        EmailTemplate::create([
            'name' => 'new_user_welcome',
            'display_name' => 'New User Welcome',
            'subject' => 'New user Welcome',
            'body' => "Hi {first_name}, Welcome.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}"
        ]);

        // 3.
        EmailTemplate::create([
            'name' => 'password_reset',
            'display_name' => 'Password Reset',
            'subject' => 'Password Reset',
            'body' => "We received a request to reset your password. Below is a code to confirm this reset. {password_reset_code}",
            'tags' => "{first_name}"
        ]);

        // 4.
        EmailTemplate::create([
            'name'      => 'new_loan_application',
            'display_name'      => 'New Loan Application',
            'subject'   => 'New Loan Application',
            'body'      => "We have received  your loan application. Our staff will review and communicate.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}, {amount_applied}, {repayment_period}, {loan_type}, {interest_rate}"
        ]);

        // 5.
        EmailTemplate::create([
            'name' => 'loan_application_approved',
            'display_name' => 'Loan Application Approved',
            'subject' => 'Loan Application Review',
            'body' => "Congratulations. Your loan Application has been approved.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}, {repayment_period}, {loan_type}, {interest_rate}, {amount_applied}, {loan_reference_number}, {repayment_period}, {start_date}"
        ]);

        // 6.
        EmailTemplate::create([
            'name' => 'loan_application_rejected',
            'display_name' => 'Loan Application Rejected',
            'subject' => 'Loan Application Review',
            'body' => "Unfortunately. Your Loan Application has been rejected.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {phone}, {amount_applied}, {rejection_notes}"
        ]);

        // 7.
        EmailTemplate::create([
            'name' => 'payment_received',
            'display_name' => 'Payment Received',
            'subject' => 'Payment Received.',
            'body' => "We have received your payment of {amount}. Thank you.",
            'tags' => "{first_name}, {middle_name}, {last_name}, {amount}, {payment_date}, {phone}, {receipt_number}"
        ]);

        // 8.
        EmailTemplate::create([
            'name' => 'system_summary',
            'display_name' => 'System Summary',
            'subject' => 'System Summary',
            'body' => " See data ...",
            'tags' => ""
        ]);

    }

}