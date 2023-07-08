<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/01/2020
 * Time: 23:12
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\MpesaCustomSend;
use App\SmartMicro\Repositories\Contracts\MpesaCustomSendInterface;

class MpesaCustomSendRepository extends BaseRepository implements MpesaCustomSendInterface
{

    protected $model;

    /**
     * MpesaCustomSendRepository constructor.
     * @param MpesaCustomSend $model
     */
    function __construct(MpesaCustomSend $model)
    {
        $this->model = $model;
    }

}