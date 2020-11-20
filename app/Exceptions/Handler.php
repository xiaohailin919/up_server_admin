<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
//        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param $request
     * @param Exception $e
     * @return Response
     */
    public function render($request, Exception $e)
    {
        /* 若 render 方法存在，则调用默认的 Render 渲染异常返回体 */
        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return Router::toResponse($request, $response);
        }
        /* 若该异常对象可直接返回，则直接构造响应体返回 */
        if ($e instanceof Responsable) {
            return $e->toResponse($request);
        }
        /* 构建响应体准备工作 */
        $e = $this->prepareException($e);

        /* API 路由，Token 认证失败 */
        if ($e instanceof UnauthorizedHttpException) {
            return $this->unauthorized($e);
        }
        /* 数据库操作失败异常 */
        if ($e instanceof QueryException) {
            return $this->convertQueryExceptionToResponse($e, $request);
        }
        if ($e instanceof NotFoundHttpException) {
            return $this->modelNotFound($request, $e);
        }
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }
        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }
        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        /* 若请求 API 接口，返回 JSON 结构体，若普通请求，返回 Error 页面 */
        return $request->expectsJson() || strpos($request->getUri(), '/api')
            ? $this->jsonExceptionResponse($e)
            : $this->prepareResponse($request, $e);

    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param Exception $e
     * @return JsonResponse
     */
    protected function jsonExceptionResponse(Exception $e): JsonResponse
    {
        $code    = method_exists($e, 'getCode') && $e->getCode() != 0 ? $e->getCode() : 500;
        $status  = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $code;
        $headers = method_exists($e, 'getHeaders') ? $e->getHeaders() : [];
        $message = method_exists($e, 'getMessage') ? $e->getMessage() : __('code.9998');

        $defaultRes = [
            'code' => $code == 0 ? 9998 : $code,
            'msg'  => $message,
            'data' => $this->convertExceptionToArray($e),
        ];

        /* 对于有响应体的异常，沿用之前的响应体，同时检查补充必须的三个指标 */
        if (method_exists($e, 'getResponse') && ($res = $e->getResponse()) != null) {
            $body = json_decode($res->getBody(), true);
            $data = $body;
            foreach ($defaultRes as $key => $value) {
                if (empty($data[$key])) {
                    $data[$key] = $value;
                }
            }
        } else {
            $data = $defaultRes;
        }

        return new JsonResponse(
            $data, $status, $headers,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * 处理验证失败的异常
     *
     * @param ValidationException $e
     * @param $request
     * @return JsonResponse|\Illuminate\Http\Response|Response|null
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request) {
        if ($e->response) {
            return $e->response;
        }

        if ($request->expectsJson() || strpos($request->getUri(), '/api')) {

            $errors = $e->errors();

            $data = ['code' => 10000, 'msg' => '', 'data' => []];
            foreach ($errors as $field => $errorMsgList) {
                if ($data['msg'] == '') {
                    $data['msg'] = $errorMsgList[0];
                }
                $data['data'][$field] = $errorMsgList[0];
            }

            return new JsonResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY, [],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        /* Web 下处理验证失败异常：返回上一页并携带错误信息 */
        return $this->invalid($request, $e);
    }

    /**
     * Api 路由组下，即使用 Token 方式鉴权，使用错误 Token 会抛出 UnauthorizedHttpException
     *
     * @param UnauthorizedHttpException $exception
     * @return JsonResponse
     */
    protected function unauthorized(UnauthorizedHttpException $exception): JsonResponse
    {
        $data = [
            'code' => Response::HTTP_UNAUTHORIZED,
            'msg'  => $exception->getMessage(),
            'data' => $this->convertExceptionToArray($exception)
        ];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED, $exception->getHeaders(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    protected function modelNotFound(Request $request, NotFoundHttpException $e)
    {
        if (!($request->expectsJson() || strpos($request->getUri(), '/api'))) {
            return $this->prepareResponse($request, $e);
        }

        $data = [
            'code' => 10000,
            'msg' => $e->getMessage(),
            'data' => $this->convertExceptionToArray($e),
        ];

        return new JsonResponse($data, Response::HTTP_NOT_FOUND, $e->getHeaders(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 特殊处理 QueryException
     *
     * @param QueryException $exception
     * @param $request
     * @return JsonResponse|\Illuminate\Http\Response|Response
     */
    protected function convertQueryExceptionToResponse(QueryException $exception, $request) {

        if ($request->expectsJson() || strpos($request->getUri(), '/api')) {
            $data = ['code' => 9995, 'msg' => __('code.9995'), 'data' => $this->convertExceptionToArray($exception)];
            return new JsonResponse($data, Response::HTTP_INTERNAL_SERVER_ERROR, [],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return $this->prepareResponse($request, $exception);
    }
}
