<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 31/08/2019
 * Time: 16:21
 */

namespace App\Traits;


use Illuminate\Support\Facades\Auth;

/**
 * Adds value to branch_id field is not provided.
 *
 * Trait BranchScope
 * @package App\Traits
 */
trait BranchScope
{
    static function bootBranchScope()
    {
        static::creating(function ($model) {
            if($model->branch_id == '' && Auth::user() != null){
                $model->branch_id = Auth::user()->branch_id;
                $model->created_by = Auth::user()->id;
            }
        });
        static::updating(function ($model) {
            if($model->branch_id == '' && Auth::user() != null){
                $model->branch_id = Auth::user()->branch_id;
            }
            $model->updated_by = Auth::user()->id;
        });
        static::deleting(function ($model) {
            $model->deleted_by = Auth::user()->id;
        });
    }

}