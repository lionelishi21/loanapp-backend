<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 15/10/2019
 * Time: 12:23
 */

namespace database\seeds;

use App\Models\FinanceStatement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceStatementSeeder extends Seeder
{

    public function run()
    {
        DB::table('finance_statements')->delete();

        FinanceStatement::create([
            'name' => 'trial_balance',
            'display_name' => 'Trial balance',
            'description' => "Trial balance"
        ]);

        FinanceStatement::create([
            'name' => 'income_statement',
            'display_name' => 'Income Statement',
            'description' => "Income Statement"
        ]);

    }

}