<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 07/10/2019
 * Time: 12:26
 */

namespace App\Traits;

/**
 * Filter records by the logged in user's branch id
 * Trait BranchFilterScope
 * @package App\Traits
 */
trait BranchFilterScope
{
    static function bootBranchFilterScope()
    {
        static::addGlobalScope(new TenantScope());
    }

}