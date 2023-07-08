<?php
/**
 * Created by PhpStorm.
 * LoanType: kevin
 * Date: 26/10/2018
 * Time: 12:21
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\LoanType;
use App\SmartMicro\Repositories\Contracts\LoanTypeInterface;

class LoanTypeRepository extends BaseRepository implements LoanTypeInterface {

    protected $model;

    /**
     * LoanTypeRepository constructor.
     * @param LoanType $model
     */
    function __construct(LoanType $model)
    {
        $this->model = $model;
    }

}