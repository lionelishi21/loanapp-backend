<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 16:54
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\FiscalPeriod;
use App\SmartMicro\Repositories\Contracts\FiscalPeriodInterface;

class FiscalPeriodRepository extends BaseRepository implements FiscalPeriodInterface
{
    protected $model;

    /**
     * FiscalPeriodRepository constructor.
     * @param FiscalPeriod $model
     */
    function __construct(FiscalPeriod $model)
    {
        $this->model = $model;
    }

}