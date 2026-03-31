<?php

namespace Modules\Shared\Infrastructure\Helpers;

use Illuminate\Http\JsonResponse;
use Modules\Shared\Infrastructure\Constants\HttpStatus;

class ApiResponse
{
    /**
     * Generate a standardized successful API JSON response.
     *
     * @param  mixed        $data     The response data payload.
     * @param  string|null  $message  The success message.
     * @param  int          $code     The HTTP status code. Defaults to HttpStatus::OK.
     * @return JsonResponse           The JSON response object with success indicator.
     */
    public static function success(mixed $data = null, ?string $message = 'Success', int $code = HttpStatus::OK->value): JsonResponse
    {
        return response()->json([
            'is_success' => true,
            'message'    => $message,
            'data'       => $data,
        ], $code);
    }

    /**
     * Generate a standardized error API JSON response.
     *
     * @param  string       $message  The error message.
     * @param  int          $code     The HTTP status code. Defaults to HttpStatus::BAD_REQUEST.
     * @param  mixed|null   $errors   Additional error details, if any.
     * @return JsonResponse           The JSON response object with error indicator.
     */
    public static function error(string $message = 'Error', int $code = HttpStatus::BAD_REQUEST->value, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'is_success' => false,
            'message'    => $message,
            'errors'     => $errors,
        ], $code);
    }
}
