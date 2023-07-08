<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 07/08/2019
 * Time: 09:22
 */

namespace database\seeds;

use App\Models\InterestType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterestTypeSeeder extends Seeder
{

    public function run()
    {

        DB::table('interest_types')->delete();

        InterestType::create([
            'name' => 'reducing_balance',
            'display_name' => 'Reducing Balance',
            'description' => "Reducing Balance"
        ]);

        InterestType::create([
            'name' => 'fixed',
            'display_name' => 'Fixed Rate',
            'description' => "Fixed Rate"
        ]);

    }

}