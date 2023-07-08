<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/11/2019
 * Time: 14:40
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\EmailTemplate;
use App\SmartMicro\Repositories\Contracts\EmailTemplateInterface;

class EmailTemplateRepository extends BaseRepository implements EmailTemplateInterface
{
    protected $model;

    /**
     * EmailTemplateRepository constructor.
     * @param EmailTemplate $model
     */
    function __construct(EmailTemplate $model)
    {
        $this->model = $model;
    }

}