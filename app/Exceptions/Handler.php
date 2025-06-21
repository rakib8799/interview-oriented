<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException; // Import AuthorizationException
use Illuminate\Validation\ValidationException; // Import ValidationException
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // Import NotFoundHttpException
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException; // Import MethodNotAllowedHttpException
use Illuminate\Database\Eloquent\ModelNotFoundException; // Import ModelNotFoundException
use Illuminate\Http\JsonResponse; // Import JsonResponse

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
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Handle API-specific exceptions
        if ($request->is('api/*')) { // Check if the request is an API request
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $exception->errors(),
                ], 422);
            }

            if ($exception instanceof AuthorizationException) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 403);
            }

            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Resource not found.',
                ], 404);
            }

            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => 'Endpoint not found.',
                ], 404);
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'message' => 'Method not allowed for this endpoint.',
                ], 405);
            }

            // Generic server error for other exceptions
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $exception->getMessage(), // For development, you might show more details
                // 'trace' => $exception->getTrace(), // For development
            ], 500);
        }

        // For web requests, fall back to default Laravel exception handling
        return parent::render($request, $exception);
    }
}
