<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 03/06/2019
 * Time: 11:01
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\GeneralSetting;
use App\SmartMicro\Repositories\Contracts\GeneralSettingInterface;

class GeneralSettingRepository extends BaseRepository implements GeneralSettingInterface
{

    protected $model;

    /**
     * GeneralSettingRepository constructor.
     * @param GeneralSetting $model
     */
    function __construct(GeneralSetting $model)
    {
        $this->model = $model;
    }

}