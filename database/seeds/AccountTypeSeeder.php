<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 23:03
 */

namespace database\seeds;

use App\Models\AccountClass;
use App\Models\AccountType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountTypeSeeder extends Seeder
{

    public function run()
    {
        DB::table('account_types')->delete();

        $assetClassId = AccountClass::where('name', ASSET)->select('id')->first()['id'];
        $liabilitiesClassId = AccountClass::where('name', LIABILITY)->select('id')->first()['id'];
        $incomeClassId = AccountClass::where('name', INCOME)->select('id')->first()['id'];
        $expenditureClassId  = AccountClass::where('name', EXPENDITURE)->select('id')->first()['id'];

        // Asset Account Types
        AccountType::create([
            'account_class_id' => $assetClassId,
            'name' => CURRENT_ASSET,
            'code' => CURRENT_ASSET_CODE,
            'description' => CURRENT_ASSET
        ]);
        AccountType::create([
            'account_class_id' => $assetClassId,
            'name' => FIXED_ASSET,
            'code' => FIXED_ASSET_CODE,
            'description' => FIXED_ASSET
        ]);
        AccountType::create([
            'account_class_id' => $assetClassId,
            'name' => LOAN_RECEIVABLE,
            'code' => LOAN_RECEIVABLE_CODE,
            'description' => LOAN_RECEIVABLE
        ]);

        //Income Account Types
        AccountType::create([
            'account_class_id' => $incomeClassId,
            'name' => LENDING_ACTIVITY,
            'code' => LENDING_ACTIVITY_CODE,
            'description' => LENDING_ACTIVITY
        ]);

        // Expenditure Account types
        AccountType::create([
            'account_class_id' => $expenditureClassId,
            'name' => EXPENSE,
            'code' => EXPENSE_CODE,
            'description' => EXPENSE
        ]);

        // Liability Account types
        AccountType::create([
                'account_class_id' => $liabilitiesClassId,
                'name' => CAPITAL,
                'code' => CAPITAL_CODE,
                'description' => CAPITAL
            ]);

        AccountType::create([
            'account_class_id' => $liabilitiesClassId,
            'name' => MEMBER_DEPOSIT,
            'code' => MEMBER_DEPOSIT_CODE,
            'description' => MEMBER_DEPOSIT
        ]);
    }

}