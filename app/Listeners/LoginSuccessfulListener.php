<?php

namespace App\Listeners;

use App\Events\Oauth\LoginSuccess;
use App\Models\LoginEvent;
use App\SmartMicro\Repositories\Contracts\UserInterface;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Request;

class LoginSuccessfulListener
{
    private $userRepository;

    /**
     * LoginSuccessfulListener constructor.
     * @param UserInterface $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param  LoginSuccess  $event
     * @return void
     */
    public function handle(LoginSuccess $event)
    {
        $user = $this->userRepository->getWhere('email', $event->email);
        if($user) {
            LoginEvent::create([
                'user_id'       =>  $user->id,
                'event'         =>  'in',
                'email'         =>  $event->email,
                'user_agent'    =>  Request::header('User-Agent'),
                'ip_address'    =>  Request::ip()
            ]);
        }
    }
}
