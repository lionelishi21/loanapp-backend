<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 11:48
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\EmailSetting;
use App\SmartMicro\Repositories\Contracts\EmailSettingInterface;

class EmailSettingRepository extends BaseRepository implements EmailSettingInterface
{
    protected $model;

    /**
     * EmailSettingRepository constructor.
     * @param EmailSetting $model
     */
    function __construct(EmailSetting $model)
    {
        $this->model = $model;
    }

}