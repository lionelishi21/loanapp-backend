<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 31/08/2019
 * Time: 16:15
 */

namespace App\Traits;


use App\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check()) {
            if($this->isAdmin())
                return $builder;

            $activeBranchId= auth('api')->user()->branch_id;
                return $builder->where($model->getTable() . '.branch_id',  '=', $activeBranchId);
        }
        return $builder;
    }

    /**
     * Checks if current active user has all available permissions (Thus admin)
     * @return bool
     */
    private function isAdmin() {

        // System wide permissions
        $allPermissions = Permission::all()->toArray();
        $allPermissions = array_map(function($allPermission) {
            return $allPermission['name'];
        }, $allPermissions);

        // Current user permissions
        $userPerms = [];
        if(auth()->user()){
            $userPerms = auth()->user()->role->permissions->toArray();
            $userPerms = array_map(function($userPerm) {
                return $userPerm['name'];
            }, $userPerms);
        }

        if(empty(array_diff($allPermissions, $userPerms))) {
            return true;
        }
            return false;
    }
}