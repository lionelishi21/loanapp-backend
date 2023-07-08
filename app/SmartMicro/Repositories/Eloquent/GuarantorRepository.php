<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 12:38
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Guarantor;
use App\SmartMicro\Repositories\Contracts\GuarantorInterface;

class GuarantorRepository extends BaseRepository implements GuarantorInterface {

    protected $model;

    /**
     * GuarantorRepository constructor.
     * @param Guarantor $model
     */
    function __construct(Guarantor $model)
    {
        $this->model = $model;
    }

}