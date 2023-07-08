<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/11/2019
 * Time: 17:46
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\CommunicationSetting;
use App\SmartMicro\Repositories\Contracts\CommunicationSettingInterface;

class CommunicationSettingRepository extends BaseRepository implements CommunicationSettingInterface
{

    protected $model;

    /**
     * CommunicationSettingtingRepository constructor.
     * @param CommunicationSetting $model
     */
    function __construct(CommunicationSetting $model)
    {
        $this->model = $model;
    }

}