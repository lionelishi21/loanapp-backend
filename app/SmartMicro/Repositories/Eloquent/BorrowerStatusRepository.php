<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 12:50
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\BorrowerStatus;
use App\SmartMicro\Repositories\Contracts\BorrowerStatusInterface;

class BorrowerStatusRepository extends BaseRepository implements BorrowerStatusInterface {

    protected $model;

    /**
     * BorrowerStatusRepository constructor.
     * @param BorrowerStatus $model
     */
    function __construct(BorrowerStatus $model)
    {
        $this->model = $model;
    }

}