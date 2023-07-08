<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 16/12/2018
 * Time: 11:12
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Payment;
use App\SmartMicro\Repositories\Contracts\PaymentInterface;
use Illuminate\Support\Facades\DB;

class PaymentRepository extends BaseRepository implements PaymentInterface {

    protected $model;

    /**
     * PaymentRepository constructor.
     * @param Payment $model
     */
    function __construct(Payment $model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function totalMpesaDeposits() {
        return DB::table('payments')
            ->select(DB::raw('COALESCE(sum(payments.amount), 0.0) as totalMpesa'))
            ->where('payments.is_mpesa','=', true)
            ->first()->totalMpesa;
    }

}