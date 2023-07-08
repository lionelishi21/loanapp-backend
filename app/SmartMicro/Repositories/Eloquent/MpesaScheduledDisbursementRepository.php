<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 31/01/2020
 * Time: 10:31
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\MpesaScheduledDisbursement;
use App\SmartMicro\Repositories\Contracts\MpesaScheduledDisbursementInterface;

class MpesaScheduledDisbursementRepository extends BaseRepository implements MpesaScheduledDisbursementInterface
{

    protected $model;

    /**
     * MpesaScheduledDisbursementRepository constructor.
     * @param MpesaScheduledDisbursement $model
     */
    function __construct(MpesaScheduledDisbursement $model)
    {
        $this->model = $model;
    }

}