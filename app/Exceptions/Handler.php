<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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

    protected $dontReport = [
        CodeException::class,
        ValidationException::class,
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
    }

    public function report(Throwable $e)
    {
        Log::error($e);
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof CodeException) {
            return response()->json(['code' => $e->getErrCode(), 'message' => $e->getErrMsg()], $e->getHttpStatus());
        }
        if ($e instanceof ValidationException) {
            return response()->json(['code' => -1, 'message' => $e->getMessage()], 400);
        }
        return response()->json(['code' => -1, 'message' => $e->getMessage()], 500);
    }
}
