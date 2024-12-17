<?php


namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success response with the given data.
     *
     * @param mixed $data The data to include in the response.
     * @param string $message The success message.
     * @param int $statusCode The HTTP status code.
     * @return JsonResponse The JSON response with the success status.
     */
    public function successResponse($data, $message = 'Request was successful.', $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return an error response with the given message and errors.
     *
     * @param string $message The error message.
     * @param int $statusCode The HTTP status code.
     * @param array|null $errors Optional errors to include in the response.
     * @return JsonResponse The JSON response with the error status.
     */
    public function errorResponse($message = 'Something went wrong.', $statusCode = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Return a validation error response with the given validation errors.
     *
     * @param $errors The validation errors.
     * @return JsonResponse The JSON response with validation error details.
     */
    public function validationErrorResponse($errors): JsonResponse
    {
        return $this->errorResponse('Validation failed', 422, $errors);
    }
}
