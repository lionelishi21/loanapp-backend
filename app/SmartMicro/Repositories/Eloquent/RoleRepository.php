<?php
/**
 * Created by PhpStorm.
 * Role: kevin
 * Date: 26/10/2018
 * Time: 21:54
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Role;
use App\SmartMicro\Repositories\Contracts\RoleInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleRepository extends BaseRepository implements RoleInterface {

    protected $model;

    /**
     * RoleRepository constructor.
     * @param Role $model
     */
    function __construct(Role $model)
    {
        $this->model = $model;
    }
}