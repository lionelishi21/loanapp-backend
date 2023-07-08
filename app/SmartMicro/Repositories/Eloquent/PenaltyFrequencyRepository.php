<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 10/11/2019
 * Time: 16:03
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\PenaltyFrequency;
use App\SmartMicro\Repositories\Contracts\PenaltyFrequencyInterface;

class PenaltyFrequencyRepository extends BaseRepository implements PenaltyFrequencyInterface
{
    protected $model;

    /**
     * PenaltyFrequencyRepository constructor.
     * @param PenaltyFrequency $model
     */
    function __construct(PenaltyFrequency $model)
    {
        $this->model = $model;
    }

}