<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/11/2019
 * Time: 23:05
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\SmsTemplate;
use App\SmartMicro\Repositories\Contracts\SmsTemplateInterface;

class SmsTemplateRepository extends BaseRepository implements SmsTemplateInterface
{
    protected $model;

    /**
     * SmsTemplateRepository constructor.
     * @param SmsTemplate $model
     */
    function __construct(SmsTemplate $model)
    {
        $this->model = $model;
    }

}