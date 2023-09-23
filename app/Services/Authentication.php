<?php

namespace App\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authentication
{
    /**
     * Authenticate the incoming user login request.
     * @param mixed  $request
     * @return void
     */
    public function handle($request)
    {
        try {
            // Get the accepted login credentials
            $credentials = $request->only('email', 'password');

            if (!auth()->attempt($credentials)) {
                return throwException('The login information you provided is not recognized. Please verify your credentials and try again.', Response::HTTP_UNAUTHORIZED);
            }

            // The authenticated user
            $user = $request->user();

            // Revoke all previous tokens
            // $user->tokens->each(function ($token, $key) {
            //     revokeAccessToken($token->id);
            // });

            // return $user->createToken('HR_HEADHUNTING_AUTH_TOKEN')->accessToken;

            // Generate a unique user token for the current user
            return [
                'token' => $user->createToken('HR_HEADHUNTING_AUTH_TOKEN')->accessToken,
                'user'  => new \App\Http\Resources\CandidateResource($user)
            ];
        } catch (\Throwable $throwable) {
            logger($throwable->getMessage());
            throwException('We apologize for the inconvenience, but we were unable to complete your login request.',  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
