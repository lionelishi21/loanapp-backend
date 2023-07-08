<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 11:16
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Member;
use App\SmartMicro\Repositories\Contracts\MemberInterface;

class MemberRepository extends BaseRepository implements MemberInterface {

    protected $model;

    /**
     * MemberRepository constructor.
     * @param Member $model
     */
    function __construct(Member $model)
    {
        $this->model = $model;
    }

}