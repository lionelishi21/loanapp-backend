<?php

namespace App\Rules;

use App\Models\Account;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class MoreThanAccountBalance implements Rule
{
    protected $memberId;

    /**
     * MoreThanAccountBalance constructor.
     * @param $memberId
     */
    public function __construct($memberId)
    {
        $this->memberId = $memberId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $accountId = Account::where('account_name', $this->memberId)
            ->where('account_code', MEMBER_DEPOSIT_CODE)
            ->select('id')
            ->first()['id'];

        $balance = DB::table('account_ledgers')
            ->select(DB::raw('COALESCE(sum(account_ledgers.amount), 0.0) as balance'))
            ->where('account_ledgers.account_id', $accountId)
            ->first()->balance;

        $balance = -1 * $balance;

        if($balance >= $value){
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ucfirst(':attribute exceeds available balance.');
    }
}
