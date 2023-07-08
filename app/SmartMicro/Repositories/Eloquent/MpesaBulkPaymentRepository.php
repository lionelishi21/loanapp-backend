<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/01/2020
 * Time: 09:22
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\MpesaBulkPayment;
use App\SmartMicro\Repositories\Contracts\MpesaBulkPaymentInterface;
use Illuminate\Support\Facades\DB;

class MpesaBulkPaymentRepository extends BaseRepository implements MpesaBulkPaymentInterface
{

    protected $model;

    /**
     * MpesaBulkPaymentRepository constructor.
     * @param MpesaBulkPayment $model
     */
    function __construct(MpesaBulkPayment $model)
    {
        $this->model = $model;
    }

    public function customerCount() {
        return count(DB::table('mpesa_bulk_payments')->distinct()->select('receiver_party_public_name')->get());
    }

    /**
     * @return mixed
     */
    public function transactionValue() {
        return DB::table('mpesa_bulk_payments')
            ->select(DB::raw('COALESCE(sum(mpesa_bulk_payments.transaction_amount), 0.0) as balance'))
            ->first()->balance;
    }

}