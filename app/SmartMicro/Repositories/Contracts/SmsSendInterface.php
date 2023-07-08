<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 23/11/2019
 * Time: 09:56
 */

namespace App\SmartMicro\Repositories\Contracts;

interface SmsSendInterface extends BaseInterface
{
    /**
     * @param $recipients
     * @param $message
     * @return mixed
     */
    public function send($recipients, $message);
}