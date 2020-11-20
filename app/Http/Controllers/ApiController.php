<?php

namespace App\Http\Controllers;

use App\Services\Permission as PermissionService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Api Controller 基类，所有 API 控制器都需要继承本基类
 *
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{
    public function __construct() {

        /* 校验登录 */
        $this->middleware(['jwt.auth', 'user.status']);

        /* 校验权限 */
        PermissionService::checkAccessPermission();
    }

    /**
     * 特殊业务响应码所对应的 Http 状态码列表
     *
     * @var array
     */
    private $serviceHttpCodeMap = [
        0     => Response::HTTP_OK,                            // 请求成功
        1     => Response::HTTP_CREATED,                       // 创建成功
        2     => Response::HTTP_ACCEPTED,                      // 请求已接受
        3     => Response::HTTP_NON_AUTHORITATIVE_INFORMATION, // 非授权信息
        4     => Response::HTTP_NO_CONTENT,                    // 无内容
        5     => Response::HTTP_RESET_CONTENT,                 // 重置内容
        9001  => Response::HTTP_UNAUTHORIZED,                  // 需要登录
        9002  => Response::HTTP_UNAUTHORIZED,                  // 登录会话已过期
        9003  => Response::HTTP_FORBIDDEN,                     // 无访问权限
        9004  => Response::HTTP_PRECONDITION_FAILED,           // Csrf token 错误
        9005  => Response::HTTP_OK,                            // Certificate 参数错误
        9993  => Response::HTTP_INTERNAL_SERVER_ERROR,         // 远程服务调用失败
        9994  => Response::HTTP_INTERNAL_SERVER_ERROR,         // 用户已登录，但获取失败
        9995  => Response::HTTP_INTERNAL_SERVER_ERROR,         // 数据库操作失败
        9996  => Response::HTTP_NOT_IMPLEMENTED,               // 功能未实现
        9997  => Response::HTTP_SERVICE_UNAVAILABLE,           // 服务不可用
        9998  => Response::HTTP_INTERNAL_SERVER_ERROR,         // 服务器内部错误
        9999  => Response::HTTP_INTERNAL_SERVER_ERROR,         // 普通错误
        10000 => Response::HTTP_UNPROCESSABLE_ENTITY,          // 请求参数错误
        10001 => Response::HTTP_METHOD_NOT_ALLOWED,            // 接口不支持当前请求方法
        10002 => Response::HTTP_GONE,                          // 资源已删除
        10003 => Response::HTTP_NOT_FOUND,                     // 未找到资源
    ];

    /**
     * 判断是否有页面访问权限，否则抛出“无访问权限”的异常
     * 该方法已废弃，请将所有调用该方法的代码转为 permission 中的配置
     *
     * @param $permissionName
     * @deprecated
     */
    protected function checkAccessPermission($permissionName) {
        PermissionService::checkAccessPermission($permissionName);
    }

    /**
     * Json 响应体，继承于 Response，即可进行一切 Response 操作
     *
     * @param array $data 响应体数据
     * @param int $code 业务响应码
     * @param string $message 响应码消息
     * @param int $httpCode Http 状态码
     * @param bool $log 是否日志记录
     * @return JsonResponse Json 响应体
     */
    public function jsonResponse($data = [], $code = 0, $message = '', $httpCode = 200, $log = false): JsonResponse {
        if ($message === '') {
            $message = __('code.' . $code);
        }

        if ($httpCode === 200) {
            $httpCode = array_key_exists($code, $this->serviceHttpCodeMap) ? $this->serviceHttpCodeMap[$code] : 422;
        }

        $response = [
            'code' => $code,
            'msg'  => $message,
            'data' => $data
        ];

        if($log){
            Log::info('Global Output', $response);
        }

        return new JsonResponse($response, $httpCode, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 解析数据库事务中的异常作为响应体
     *
     * @param Exception $e
     * @return JsonResponse
     */
    public function transactionExceptionResponse(Exception $e): JsonResponse
    {
        return $this->jsonResponse([
            'msg'   => $e->getMessage(),
            'line'  => $e->getLine(),
            'trace' => $e->getTrace()
        ], 9995);
    }

    /**
     * 从 分页对象 中获取分类列表数据
     *
     * @param LengthAwarePaginator $paginator
     * @return array
     */
    protected function parseResByPaginator(LengthAwarePaginator $paginator): array {

        $res = [
            'total'     => (int)$paginator->total(),
            'page_no'   => (int)$paginator->currentPage(),
            'page_size' => (int)$paginator->perPage(),
            'list'      => [],
        ];

        $list = $paginator->items();
        foreach ($list as $item) {
            $res['list'][] = $item->toArray();
        }

        return $res;
    }

    protected function getControllerUses()
    {
        return request()->route()->getAction()['uses'] ?? '';
    }
}
