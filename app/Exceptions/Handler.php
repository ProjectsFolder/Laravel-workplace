<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $e
     *
     * @return void
     *
     * @throws Exception
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request  $request
     * @param Exception $e
     *
     * @return Response
     *
     * @throws Exception
     */
    public function render($request, Exception $e): Response
    {
        $e = $this->prepareException($e);

        $code = 500;
        if ($e instanceof HttpExceptionInterface) {
            $code = $e->getStatusCode();
        }
        $params = [
            'success' => false,
            'error' => $e->getMessage(),
        ];

        if (true == env('APP_DEBUG')) {
            $params['file'] = $e->getFile();
            $params['line'] = $e->getLine();
            $params['trace'] = $e->getTrace();
        }

        return new JsonResponse($params, $code);
    }

    /**
     * @param Validator $validator
     */
    public static function failed(Validator $validator)
    {
        $errors = $validator->errors()->all();
        $message = "Validation errors:\n";
        $message .= implode("\n", $errors);
        throw new HttpException(400, $message);
    }
}
