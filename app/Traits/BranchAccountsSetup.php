<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 30/08/2019
 * Time: 18:30
 */

namespace App\Traits;

use App\Models\Account;
use App\Models\AccountType;
use Ramsey\Uuid\Uuid;

trait BranchAccountsSetup {

    static function bootBranchAccountsSetup()
    {
        static::created(function ($model) {
            $branchId = $model->id;
            $branchCode = $model->branch_code;

            $currentAssetTypeId = AccountType::where('name', CURRENT_ASSET)->select('id')->first()['id'];
            $capitalTypeId = AccountType::where('name', CAPITAL)->select('id')->first()['id'];
            $lendingActivityTypeId = AccountType::where('name', LENDING_ACTIVITY)->select('id')->first()['id'];

            $data = [
                [
                    'id' => Uuid::uuid4(),
                    'account_type_id' => $currentAssetTypeId,
                    'account_code'  => CASH_ACCOUNT_CODE,
                    'account_name'  => CASH_ACCOUNT_NAME,
                    'branch_id'  => $branchId,
                    'account_number'  => $branchCode.'-0001',
                ],
                [
                    'id' => Uuid::uuid4(),
                    'account_type_id' => $currentAssetTypeId,
                    'account_code'  => MPESA_ACCOUNT_CODE,
                    'account_name'  => MPESA_ACCOUNT_NAME,
                    'branch_id'  => $branchId,
                    'account_number'  => $branchCode.'-0002',
                ],
                [
                    'id' => Uuid::uuid4(),
                    'account_type_id' => $currentAssetTypeId,
                    'account_code'  => BANK_ACCOUNT_CODE,
                    'account_name'  => BANK_ACCOUNT_NAME,
                    'branch_id'  => $branchId,
                    'account_number'  => $branchCode.'-0003',
                ],
                [
                    'id' => Uuid::uuid4(),
                    'account_type_id' => $capitalTypeId,
                    'account_code'  => CAPITAL_ACCOUNT_CODE,
                    'account_name'  => CAPITAL_ACCOUNT_NAME,
                    'branch_id'  => $branchId,
                    'account_number'  => $branchCode.'-0004',
                ],
                [
                    'id' => Uuid::uuid4(),
                    'account_type_id' => $lendingActivityTypeId,
                    'account_code'  => PENALTY_ACCOUNT_CODE,
                    'account_name'  => PENALTY_ACCOUNT_NAME,
                    'branch_id'  => $branchId,
                    'account_number'  => $branchCode.'-0005',
                ],
                [
                    'id' => Uuid::uuid4(),
                    'account_type_id' => $lendingActivityTypeId,
                    'account_code'  => INTEREST_ACCOUNT_CODE,
                    'account_name'  => INTEREST_ACCOUNT_NAME,
                    'branch_id'  => $branchId,
                    'account_number'  => $branchCode.'-0006',
                ],
                [
                    'id' => Uuid::uuid4(),
                    'account_type_id' => $lendingActivityTypeId,
                    'account_code'  => SERVICE_FEE_ACCOUNT_CODE,
                    'account_name'  => SERVICE_FEE_ACCOUNT_NAME,
                    'branch_id'  => $branchId,
                    'account_number'  => $branchCode.'-0007',
                ]
            ];
            foreach ($data as $key => $value){
                Account::create($value);
            }
        });
    }
}