<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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


        $this->renderable(function (ValidationException $exception) {
            return \response()->json(['success' => false, 'message' => $exception->getMessage(), 'code' => 422], 422);
        });

        $this->renderable(function (NotFoundHttpException $exception) {
            return \response()->json(['success' => false, 'message' => $exception->getMessage(), 'code' => 404], 404);
        });

        $this->renderable(function (AccessDeniedHttpException $exception) {
            return \response()->json(['success' => false, 'message' => $exception->getMessage(), 'code' => 403], 403);
        });

        $this->renderable(function (MethodNotAllowedHttpException $exception) {
            return \response()->json(['success' => false, 'message' => $exception->getMessage(), 'code' => 405], 405);
        });

        $this->renderable(function (UnauthorizedException $exception) {
            return \response()->json(['success' => false, 'message' => $exception->getMessage(), 'code' => 401], 401);
        });

        $this->renderable(function (\Error $exception) {
            return \response()->json(['success' => false, 'message' => $exception->getMessage(), 'code' => 500], 500);
        });


    }
}
