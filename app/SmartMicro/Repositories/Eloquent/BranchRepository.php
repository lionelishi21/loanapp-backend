<?php
/**
 * Created by PhpStorm.
 * Branch: kevin
 * Date: 26/10/2018
 * Time: 21:39
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Branch;
use App\SmartMicro\Repositories\Contracts\BranchInterface;

class BranchRepository extends BaseRepository implements BranchInterface {

    protected $model;

    /**
     * BranchRepository constructor.
     * @param Branch $model
     */
    function __construct(Branch $model)
    {
        $this->model = $model;
    }

}