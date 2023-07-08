<?php
/**
 * Created by PhpStorm.
 * LoanStatus: kevin
 * Date: 26/10/2018
 * Time: 12:31
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\LoanStatus;
use App\SmartMicro\Repositories\Contracts\LoanStatusInterface;

class LoanStatusRepository extends BaseRepository implements LoanStatusInterface {

    protected $model;

    /**
     * LoanStatusRepository constructor.
     * @param LoanStatus $model
     */
    function __construct(LoanStatus $model)
    {
        $this->model = $model;
    }

}