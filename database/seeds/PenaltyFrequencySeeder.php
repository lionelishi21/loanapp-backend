<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 10/11/2019
 * Time: 15:42
 */

namespace database\seeds;

use App\Models\PenaltyFrequency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenaltyFrequencySeeder extends Seeder
{

    public function run()
    {
        DB::table('penalty_frequencies')->delete();

        PenaltyFrequency::create([
            'name' => 'one_time',
            'display_name' => 'One Time',
            'description' => "Single Fixed Repayment"
        ]);

        PenaltyFrequency::create([
            'name' => 'daily',
            'display_name' => 'Daily',
            'description' => "Everyday Repayment"
        ]);

        PenaltyFrequency::create([
            'name' => 'weekly',
            'display_name' => 'Weekly',
            'description' => "Weekly Repayment"
        ]);

        PenaltyFrequency::create([
            'name' => 'monthly',
            'display_name' => 'Monthly',
            'description' => "Monthly Repayment"
        ]);
    }

}