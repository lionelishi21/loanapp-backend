<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/07/2019
 * Time: 13:25
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\AssetPhoto;
use App\SmartMicro\Repositories\Contracts\AssetPhotoInterface;

class AssetPhotoRepository extends BaseRepository implements AssetPhotoInterface
{

    protected $model;

    /**
     * AssetPhotoRepository constructor.
     * @param AssetPhoto $model
     */
    function __construct(AssetPhoto $model)
    {
        $this->model = $model;
    }

}