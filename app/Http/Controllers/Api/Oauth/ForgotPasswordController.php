<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 06/11/2019
 * Time: 03:59
 */

namespace App\Http\Controllers\Api\OAuth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Traits\CommunicationMessage;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ForgotPasswordController extends ApiController
{
    use SendsPasswordResetEmails;

    /**
     * ForgotPasswordController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    public function __invoke(Request $request)
    {
      //
    }

    /**
     * @param ForgotPasswordRequest $request
     * @return string
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // We have a user
        if(isset($user)){
            // Create reset token
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => str_random(20),
                'created_at' => Carbon::now()
            ]);

            //Get the token just created above
            $tokenData = DB::table('password_resets')
                ->where('email', $request->email)->first();

            // Send sms and email notification
            if(!is_null($tokenData) && !is_null($user))
                CommunicationMessage::send('reset_password', $user, $tokenData);
        }
    }


    /**
     * @param ResetPasswordRequest $request
     * @return array
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $password = $request->password;

        $tokenData = DB::table('password_resets')
            ->where('token', $request->token)->first();

        if (isset($tokenData)){
            $user = User::where('email', $tokenData->email)->first();

            if (isset($user)){
                $user->password = $password;
                $user->update();

                //Delete the token
                DB::table('password_resets')->where('email', $user->email)->delete();
                return $this->respondWithSuccess('Password Changed successfully');
            }
        }
            return $this->respondWithError('Password Changed successfully');
    }
}