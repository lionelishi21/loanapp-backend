<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/07/2019
 * Time: 13:24
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Asset;
use App\SmartMicro\Repositories\Contracts\AssetInterface;

class AssetRepository extends BaseRepository implements AssetInterface
{

    protected $model;

    /**
     * AssetRepository constructor.
     * @param Asset $model
     */
    function __construct(Asset $model)
    {
        $this->model = $model;
    }

}