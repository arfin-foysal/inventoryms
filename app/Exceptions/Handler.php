<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;


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

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized.',
            'data' => [],
        ], 401);
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'error' => null,
                'errors' => [],
                'message' => 'No query results for model ['.$exception->getModel().'] '.$exception->getIds(),
            ], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'error' => 'Validation error',
                'errors' => $exception->errors(),
                'message' => 'Error: '.$exception->validator->errors()->first(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'status' => false,
                'error' => 'Unauthenticated. Please log in and try again.',
                'data' => [],
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof UnauthorizedHttpException) {
            return response()->json([
                'status' => false,
                'error' => 'Unauthorized access. Please check your credentials.',
                'data' => [],
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof InvalidSignatureException) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid signature.',
                'exception' => get_class($exception),
            ], Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof ErrorMessageException) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    
        return parent::render($request, $exception);

        
    }
}

