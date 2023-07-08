<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/10/2019
 * Time: 23:29
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\ReportType;
use App\SmartMicro\Repositories\Contracts\ReportTypeInterface;

class ReportTypeRepository extends BaseRepository implements ReportTypeInterface
{
    protected $model;

    /**
     * ReportTypeRepository constructor.
     * @param ReportType $model
     */
    function __construct(ReportType $model)
    {
        $this->model = $model;
    }

}