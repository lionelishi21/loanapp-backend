<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 22:51
 */

namespace database\seeds;

use App\Models\AccountClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountClassSeeder extends Seeder
{

    public function run()
    {
        $classes = [
            ASSET,
            LIABILITY,
            INCOME,
            EXPENDITURE,
        ];

        $categories = [
            'ASSET' => 'DR',
            'LIABILITY' => 'CR',
            'INCOME' => 'CR',
            'EXPENDITURE' => 'DR',
        ];

        DB::table('account_classes')->delete();

        foreach ($classes as $key => $value) {

            AccountClass::create([
                'name' => $value,
                'category' => $categories[$value]
            ]);
        }

    }

}