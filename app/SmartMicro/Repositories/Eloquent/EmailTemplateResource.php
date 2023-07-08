<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/11/2019
 * Time: 22:59
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\EmailTemplate;
use App\SmartMicro\Repositories\Contracts\EmailTemplateInterface;

class EmailTemplateResource extends BaseRepository implements EmailTemplateInterface
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