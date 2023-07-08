<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 22/01/2020
 * Time: 18:09
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Withdrawal;
use App\SmartMicro\Repositories\Contracts\WithdrawalInterface;

class WithdrawalRepository extends BaseRepository implements WithdrawalInterface
{

    protected $model;

    /**
     * WithdrawalRepository constructor.
     * @param Withdrawal $model
     */
    function __construct(Withdrawal $model)
    {
        $this->model = $model;
    }

}