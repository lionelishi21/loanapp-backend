<?php


namespace App\Http\Controllers\Api\OAuth;


use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends ApiController
{

    private $loginProxy;

    public function __construct(LoginProxy $loginProxy)
    {
        $this->loginProxy = $loginProxy;
    }

    /**
     * @param LoginRequest $request
     * @return array
     * @throws \Exception
     */
    public function login(LoginRequest $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');
        return $this->loginProxy->attemptLogin($email, $password);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function refresh(Request $request)
    {
        return $this->loginProxy->attemptRefresh();
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        $this->loginProxy->logout();
        return $this->setStatusCode(204)->respondWithSuccess("Logged out ...");
    }

}