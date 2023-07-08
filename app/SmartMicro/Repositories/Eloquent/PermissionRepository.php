<?php
/**
 * Created by PhpStorm.
 * Permission: kevin
 * Date: 26/10/2018
 * Time: 21:54
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Permission;
use App\SmartMicro\Repositories\Contracts\PermissionInterface;

class PermissionRepository extends BaseRepository implements PermissionInterface {

    protected $model;

    /**
     * PermissionRepository constructor.
     * @param Permission $model
     */
    function __construct(Permission $model)
    {
        $this->model = $model;
    }

}