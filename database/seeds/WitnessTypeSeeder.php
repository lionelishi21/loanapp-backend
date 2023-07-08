<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/11/2019
 * Time: 20:53
 */

namespace database\seeds;

use App\Models\WitnessType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WitnessTypeSeeder extends Seeder
{

    public function run()
    {
        DB::table('witness_types')->delete();

        WitnessType::create([
            'name' => 'relative',
            'display_name' => 'Relative',
            'description' => "Family Relative"
        ]);

        WitnessType::create([
            'name' => 'friend',
            'display_name' => 'Friend',
            'description' => "Close Friend"
        ]);

        WitnessType::create([
            'name' => 'business_partner',
            'display_name' => 'Business Partner',
            'description' => "Business Partner"
        ]);

    }

}