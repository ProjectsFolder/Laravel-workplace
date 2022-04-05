<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

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

        $params = [
            'success' => false,
            'error_message' => $e->getMessage(),
        ];
        if (true == env('APP_DEBUG')) {
            $params['error_code'] = $e->getCode();
            $params['error_file'] = $e->getFile();
            $params['error_line'] = $e->getLine();
            $params['error_traceback'] = $e->getTrace();
        }
        $serializer = $this->container->get(SerializerInterface::class);
        $responseData = $serializer->serialize([], 'api', $params);

        $code = 500;
        if ($e instanceof HttpExceptionInterface) {
            $code = $e->getStatusCode();
        }

        return new Response($responseData, $code, ['Content-Type' => 'application/json']);
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
