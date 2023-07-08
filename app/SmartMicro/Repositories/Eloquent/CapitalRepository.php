<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/11/2019
 * Time: 20:36
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Capital;
use App\SmartMicro\Repositories\Contracts\CapitalInterface;

class CapitalRepository extends BaseRepository implements CapitalInterface
{
    protected $model;

    /**
     * CapitalRepository constructor.
     * @param Capital $model
     */
    function __construct(Capital $model)
    {
        $this->model = $model;
    }

}