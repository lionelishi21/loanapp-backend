<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 10/11/2019
 * Time: 15:31
 */

namespace database\seeds;

use App\Models\PenaltyType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenaltyTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('penalty_types')->delete();

        PenaltyType::create([
            'name' => 'fixed_value',
            'display_name' => 'Fixed Value',
            'description' => "Fixed Value"
        ]);

        PenaltyType::create([
            'name' => 'principal_due_percentage',
            'display_name' => '% on Due Principal',
            'description' => "Due Principal Percentage"
        ]);

        PenaltyType::create([
            'name' => 'principal_plus_interest_due_percentage',
            'display_name' => '% on Due Principal + Due Interest',
            'description' => "Percentage on due principal plus due interest"
        ]);
    }

}