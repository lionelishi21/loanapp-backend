<?php
/**
 * Created by PhpStorm.
 * WitnessType: kevin
 * Email: robisignals@gmail.com
 * Date: 11/07/2019
 * Time: 07:35
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\WitnessType;
use App\SmartMicro\Repositories\Contracts\WitnessTypeInterface;

class WitnessTypeRepository extends BaseRepository implements WitnessTypeInterface
{

    protected $model;

    /**
     * WitnessTypeRepository constructor.
     * @param WitnessType $model
     */
    function __construct(WitnessType $model)
    {
        $this->model = $model;
    }

}