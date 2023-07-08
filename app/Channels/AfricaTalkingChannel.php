<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 23/11/2019
 * Time: 10:57
 */

namespace App\Channels;

use Illuminate\Notifications\Notification;

class AfricaTalkingChannel
{
    /**
     * @param $notifiable
     * @param Notification $notification
     * @throws \Exception
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toATSms($notifiable);
    }
}