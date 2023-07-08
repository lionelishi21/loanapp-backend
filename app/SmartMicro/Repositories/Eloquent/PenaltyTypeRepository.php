<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 10/11/2019
 * Time: 16:09
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\PenaltyType;
use App\SmartMicro\Repositories\Contracts\PenaltyTypeInterface;

class PenaltyTypeRepository extends BaseRepository implements PenaltyTypeInterface
{
    protected $model;

    /**
     * PenaltyTypeRepository constructor.
     * @param PenaltyType $model
     */
    function __construct(PenaltyType $model)
    {
        $this->model = $model;
    }

}