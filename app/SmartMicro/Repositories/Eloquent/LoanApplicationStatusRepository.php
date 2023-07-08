<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 12:44
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\LoanApplicationStatus;
use App\SmartMicro\Repositories\Contracts\LoanApplicationStatusInterface;

class LoanApplicationStatusRepository extends BaseRepository implements LoanApplicationStatusInterface {

    protected $model;

    /**
     * LoanApplicationStatusRepository constructor.
     * @param LoanApplicationStatus $model
     */
    function __construct(LoanApplicationStatus $model)
    {
        $this->model = $model;
    }

}