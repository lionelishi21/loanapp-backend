<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 08/09/2019
 * Time: 22:45
 */

namespace database\seeds;

use App\Models\PaymentFrequency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentFrequencySeeder extends Seeder
{

    public function run()
    {

        DB::table('payment_frequencies')->delete();

        PaymentFrequency::create([
            'name' => 'one_time',
            'display_name' => 'One Time',
            'description' => "Single Fixed Repayment"
        ]);

        PaymentFrequency::create([
            'name' => 'daily',
            'display_name' => 'Daily',
            'description' => "Everyday Repayment"
        ]);

        PaymentFrequency::create([
            'name' => 'weekly',
            'display_name' => 'Weekly',
            'description' => "Weekly Repayment"
        ]);

        PaymentFrequency::create([
            'name' => 'monthly',
            'display_name' => 'Monthly',
            'description' => "Monthly Repayment"
        ]);

    }

}