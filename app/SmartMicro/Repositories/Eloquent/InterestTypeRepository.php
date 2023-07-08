<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 07/08/2019
 * Time: 08:50
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\InterestType;
use App\SmartMicro\Repositories\Contracts\InterestTypeInterface;

class InterestTypeRepository extends BaseRepository implements InterestTypeInterface
{

    protected $model;

    /**
     * InterestTypeRepository constructor.
     * @param InterestType $model
     */
    function __construct(InterestType $model)
    {
        $this->model = $model;
    }

}