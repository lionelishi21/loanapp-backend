<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 10:32
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Account;
use App\SmartMicro\Repositories\Contracts\AccountInterface;
use Illuminate\Support\Facades\DB;

class AccountRepository extends BaseRepository implements AccountInterface
{

    protected $model;

    /**
     * AccountRepository constructor.
     * @param Account $model
     */
    function __construct(Account $model)
    {
        $this->model = $model;
    }

    /**
     * @param $accountCode
     * @return mixed
     */
    function filterAccounts($accountCode)
    {
        return $this->model
            ->where('account_code', $accountCode)
            ->get();
    }

    /**
     * @param $accountCode
     * @param $select
     * @param array $load
     * @return array|mixed
     */
    public function listAccounts($accountCode, $select, $load = array()) {

        array_push($select, 'id');

        $data = [];
        try{
            if($load){
                $data =  $this->model->where('account_code', $accountCode)->with($load)->get($select);
            }else
                $data =  $this->model->where('account_code', $accountCode)->get($select);
        }catch(\Exception $e){}

        return $data;
    }

    /**
     * Fetch account statement
     * Uses incrementing journal_id to group.
     * This saves us trouble for cases with records having exact same timestamps.
     * @param $accountId
     * @return \Illuminate\Support\Collection
     */
    public function fetchAccountStatement($accountId) {
        return DB::table('account_ledgers as t1')
            ->where('t1.account_id', $accountId)
            ->leftJoin('journals', 't1.journal_id', '=', 'journals.id')
            ->select(DB::raw(
                't1.account_id,
                t1.journal_id,
                t1.created_at,
                t1.amount,
                journals.narration,
                SUM(t2.amount) AS balance'
            ))
            ->join('account_ledgers AS t2', function($join){
                $join->on('t2.account_id', '=', 't1.account_id')
                    ->on('t2.journal_id', '<=', 't1.journal_id');
            })
            ->groupBy('t1.account_id', 't1.journal_id', 't1.amount')
            ->orderBy('t1.id', 'asc')
            ->get();
    }

    /**
     * Old idea - use record timestamps to generate account statement
     * @param $accountId
     * @return \Illuminate\Support\Collection
     */
    private function accountStatementUsingTimestamps($accountId) {
        return DB::table('account_ledgers as t1')
            ->where('t1.account_id', $accountId)
            ->leftJoin('journals', 't1.journal_id', '=', 'journals.id')
            ->select(DB::raw(
                't1.account_id,
                t1.journal_id,
                t1.created_at,
                t1.amount,
                journals.narration,
                SUM(t2.amount) AS balance'
            ))
            ->join('account_ledgers AS t2', function($join){
                $join->on('t2.account_id', '=', 't1.account_id')
                     ->on('t2.created_at', '<=', 't1.created_at');
            })
             ->groupBy('t1.account_id', 't1.created_at', 't1.amount')
             ->orderBy('t1.created_at')
            ->get();
    }

    /**
     * @param $accountId
     * @return mixed
     */
    public function accountBalance($accountId) {
        return DB::table('account_ledgers')
            ->select(DB::raw('COALESCE(sum(account_ledgers.amount), 0.0) as balance'))
            ->where('account_ledgers.account_id', $accountId)
            ->first()->balance;
    }

}