<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 25/10/2018
 * Time: 11:31
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\User;
use App\SmartMicro\Repositories\Contracts\UserInterface;

class UserRepository extends BaseRepository implements UserInterface {

    protected $model;

    /**
     * UserRepository constructor.
     * @param User $model
     */
    function __construct(User $model)
    {
        $this->model = $model;
    }

}