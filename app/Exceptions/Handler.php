<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Ghost\Api\ApiResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($request->is('api/*')) {

            $responseData = [
                'exception'     => get_class($e),
                'error_code'    => $e->getCode(),
                'message'       => $e->getMessage()
            ];

            if (config('app.debug')) {
                $responseData += [
                    'file'          => $e->getFile(),
                    'line'          => $e->getLine(),
                    'stack'         => $e->getTrace()
                ];
            }

            return response()->json(
                (new ApiResponse())->fail($responseData), $this->isHttpException($e) ? $e->getStatusCode() : 400
            );
        }

        return parent::render($request, $e);
    }
}
