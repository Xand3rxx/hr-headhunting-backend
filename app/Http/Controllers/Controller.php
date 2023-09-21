<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Return a success response with or without data.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse(mixed $data, string $message = '', int $statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'success' => true,
            'data'    => !empty($data) ? $data : null,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Return a failed response with or without data.
     * 
     * @param string $message
     * @param array $errors
     * @param mixed $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function failedResponse(string $message = '', array $errors = [], int $statusCode = Response::HTTP_NOT_FOUND)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
