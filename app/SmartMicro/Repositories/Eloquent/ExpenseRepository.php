<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 15:57
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Expense;
use App\SmartMicro\Repositories\Contracts\ExpenseInterface;

class ExpenseRepository extends BaseRepository implements ExpenseInterface
{

    protected $model;

    /**
     * ExpenseRepository constructor.
     * @param Expense $model
     */
    function __construct(Expense $model)
    {
        $this->model = $model;
    }

}