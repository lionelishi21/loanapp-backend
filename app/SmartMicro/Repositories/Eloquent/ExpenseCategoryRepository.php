<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 15:59
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\ExpenseCategory;
use App\SmartMicro\Repositories\Contracts\ExpenseCategoryInterface;

class ExpenseCategoryRepository extends BaseRepository implements ExpenseCategoryInterface
{

    protected $model;

    /**
     * ExpenseCategoryRepository constructor.
     * @param ExpenseCategory $model
     */
    function __construct(ExpenseCategory $model)
    {
        $this->model = $model;
    }

}