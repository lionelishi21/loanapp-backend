<?php

namespace App\Listeners;

use App\Events\Oauth\Logout;
use App\Models\LoginEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Request;

class LogoutSuccessfulListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        LoginEvent::create([
            'user_id'       =>  $event->user->id,
            'event'         =>  'out',
            'email'         =>  $event->user->email,
            'user_agent'    =>  Request::header('User-Agent'),
            'ip_address'    =>  Request::ip()
        ]);
    }
}
