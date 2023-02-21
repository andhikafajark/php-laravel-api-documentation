<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        return match (true) {
            $e instanceof AuthorizationException => response()
                ->json([
                    'success' => false,
                    'message' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                    'data' => null
                ], Response::HTTP_UNAUTHORIZED),
            $e instanceof BadRequestException => response()
                ->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'data' => null
                ], Response::HTTP_BAD_REQUEST),
            $e instanceof MethodNotAllowedHttpException => response()
                ->json([
                    'success' => false,
                    'message' => Response::$statusTexts[Response::HTTP_METHOD_NOT_ALLOWED],
                    'data' => null
                ], Response::HTTP_METHOD_NOT_ALLOWED),
            $e instanceof ModelNotFoundException, $e instanceof NotFoundHttpException => response()
                ->json([
                    'success' => false,
                    'message' => Response::$statusTexts[Response::HTTP_NOT_FOUND],
                    'data' => null
                ], Response::HTTP_NOT_FOUND),
            $e instanceof ValidationException => response()
                ->json([
                    'success' => false,
                    'message' => Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    'data' => $e->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY),
            default => response()
                ->json([
                    'success' => false,
                    'message' => Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
                    'data' => null
                ], Response::HTTP_INTERNAL_SERVER_ERROR)
        };
    }
}
