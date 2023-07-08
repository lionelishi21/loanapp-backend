<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 11:10
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Employee;
use App\SmartMicro\Repositories\Contracts\EmployeeInterface;

class EmployeeRepository extends BaseRepository implements EmployeeInterface {

    protected $model;

    /**
     * EmployeeRepository constructor.
     * @param Employee $model
     */
    function __construct(Employee $model)
    {
        $this->model = $model;
    }

}