<?php

namespace App\Exceptions;

use App\Http\Controllers\Api\Oauth\InvalidCredentialsException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Passport\Exceptions\MissingScopeException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
      //  \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function render($request, Exception $exception)
    {
        if ($this->isHttpException($exception)) {
            if ($exception instanceof NotFoundHttpException) {

                return response()->json(
                    [
                        'error'         => true,
                        'message'       => $exception->getMessage() != '' ? $exception->getMessage() : 'Sorry, the resource you are looking for could not be found..',
                        'status_code'   => 404
                    ], 404);
            }
        }

        if ($exception instanceof MissingScopeException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'You do not have permission to access this resource..',
                    'status_code'   => 403
                ], 403);
        }

        if ($exception instanceof AuthorizationException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'This action is unauthorized. You do not have permission to access this resource..',
                    'status_code'   => 403
                ], 403);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {

            return response()->json(
                 [
                    'error'         => true,
                    'message'       => 'Method is not allowed.',
                    'status_code'   => 405
                ], 405);
        }

        if ($exception instanceof UnauthorizedHttpException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'Provided login credentials were incorrect ...',
                    'status_code'   => 401
                ], 401);
        }

        if ($exception instanceof InvalidCredentialsException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'Provided login credentials were incorrect ...',
                    'status_code'   => 401
                ], 401);
        }

        if ($exception instanceof JsonEncodingException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'Invalid data provided ...',
                    'status_code'   => 400
                ], 400);
        }

        if ($exception instanceof DecryptException) {

            return response()->json(
                [
                    'error'         => true,
                    'message'       => 'The MAC is invalid. CHeck application keys',
                    'status_code'   => 401
                ], 401);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(
            [
                'error'         => true,
                'message'       => 'Unauthenticated..',
                'status_code'   => 401
            ], 401);

    }
}
