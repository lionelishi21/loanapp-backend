<?php

namespace App\Listeners;

use App\Events\Oauth\LoginFailed;
use App\Models\FailedLogin;
use App\Models\LoginEvent;
use App\SmartMicro\Repositories\Contracts\UserInterface;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Request;

class LoginFailureListener
{
    private $userRepository;

    /**
     * LoginFailureListener constructor.
     * @param UserInterface $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param  LoginFailed  $event
     * @return void
     */
    public function handle(LoginFailed $event)
    {
        $user = $this->userRepository->getWhere('email', $event->email);
        $userId= '';

        if($user) {
            $userId = $user->id;
        }

        FailedLogin::create([
            'user_id'       =>  $userId,
            'email'         =>  $event->email,
            'user_agent'    =>  Request::header('User-Agent'),
            'ip_address'    =>  Request::ip()
        ]);
    }
}
