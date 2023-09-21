<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use BadMethodCallException;
use Error;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Throw exception with route model binding on API calls.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        //This exception is triggered when there is; Invalid Credentials, Unauthenticated Access
        // Expired or Invalid Tokens, Authentication Timeout, Multiple Login Attempts
        if ($exception instanceof AuthenticationException && ($request->wantsJson() || request()->is('api/*'))) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your login credentials.',
                'data' => []
            ], Response::HTTP_UNAUTHORIZED);
        }

        // This exception is triggered when Incorrect HTTP Method is passed e.g passing GET to a POST route, Route Not Defined
        if ($exception instanceof MethodNotAllowedHttpException && ($request->wantsJson() || request()->is('api/*'))) {
            return response()->json([
                'success' => false,
                'message' => 'Method Not Allowed.',
                'data' => []
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        // This exception is triggered when; Route Not Found, Controller Action Not Found, Resource Not Found
        if ($exception instanceof NotFoundHttpException && ($request->wantsJson() || request()->is('api/*'))) {
            return response()->json([
                'success' => false,
                'message' => 'The specified resource(s) cannot be found.',
                'data' => []
            ], Response::HTTP_NOT_FOUND);
        }

        // This exception is triggered when there is; 400 Bad Request, 401 Unauthorized, 403 Forbidden
        // 404 Not Found, 405 Method Not Allowed , 500 Internal Server Error:
        if ($exception instanceof HttpException && ($request->wantsJson() || request()->is('api/*'))) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to perform the specified action at the moment.',
                'data' => []
            ], $exception->getStatusCode());
        }

        //This exception is triggered when; Retrieving Single Model: When using methods like findOrFail() or firstOrFail()
        // Finding Model by Primary Key
        // Relationship Not Found
        if ($exception instanceof ModelNotFoundException && ($request->wantsJson() || request()->is('api/*'))) {
            return response()->json([
                'success' => false,
                'message' => 'The specified data cannot be found.',
                'data' => []
            ], Response::HTTP_NOT_FOUND);
        }

        // This exception is triggered when there is; Syntax Errors, Connection Errors, Integrity Constraint Violations
        // Data Type Mismatches, Deadlocks, Duplicate Key Entries
        if ($exception instanceof QueryException && ($request->wantsJson() || request()->is('api/*'))) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to perform the specified action at the moment.',
                'data' => []
            ], Response::HTTP_NOT_FOUND);
        }

        // Related to login credentials when making an API calls using guzzle client
        if ($exception instanceof RequestException && ($request->wantsJson() || request()->is('api/*'))) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to perform the specified action at the moment.',
                'data' => []
            ], Response::HTTP_FAILED_DEPENDENCY);
        }

        // This serves as a catch-all exception for any kind of error that occurs
        //during the data transfer process specifically for guzzle http
        if ($exception instanceof TransferException && ($request->wantsJson() || request()->is('api/*'))) {
            return response()->json([
                'success' => false,
                'message' => 'Request failed.',
                'data' => []
            ], Response::HTTP_FAILED_DEPENDENCY);
        }

        // This is triggered when there is a bad method called
        if ($exception instanceof BadMethodCallException && ($request->wantsJson() || request()->is('api/*'))) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to perform the specified action.',
                'data' => []
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        if ($exception instanceof Error && ($request->wantsJson() || request()->is('api/*'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid operation',
                'data' => []
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return parent::render($request, $exception);
    }
}
