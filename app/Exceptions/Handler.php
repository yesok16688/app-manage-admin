<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
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

    public function report(Throwable $e): void
    {
        if ($e instanceof ApiCallException) {
            Log::error(sprintf("call api error: %s \n url=%s; params=%s; resp=%s \n",
                $e->getMessage(), $e->getUrl(), $e->getParams(), $e->getMessage()), $e->getTrace());
        } else {
            Log::error($e);
        }
    }

    public function render($request, Throwable $e): Response
    {
        if ($e instanceof CodeException) {
            return response()->json(['code' => $e->getCode(), 'msg' => $e->getMessage()], $e->getHttpStatus());
        } else if ($e instanceof ModelNotFoundException) {
            return response()->json(['code' => ErrorCode::DATA_NOT_FOUND, 'msg' => '数据不存在'], 404);
        } else if ($e instanceof ValidationException) {
            return response()->json(['code' => -1, 'msg' => $e->getMessage()], 400);
        } else if ($e instanceof AuthenticationException) {
            return response()->json(['code' => ErrorCode::INVALID_TOKEN, 'msg' => $e->getMessage()], 400);
        }
        return response()->json(['code' => -1, 'msg' => $e->getMessage()], 500);
    }
}
