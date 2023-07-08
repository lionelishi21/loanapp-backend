<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/10/2019
 * Time: 13:36
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\SmsSetting;
use App\SmartMicro\Repositories\Contracts\SmsSettingInterface;

class SmsSettingRepository extends BaseRepository implements SmsSettingInterface
{
    protected $model;

    /**
     * SmsSettingRepository constructor.
     * @param SmsSetting $model
     */
    function __construct(SmsSetting $model)
    {
        $this->model = $model;
    }

}