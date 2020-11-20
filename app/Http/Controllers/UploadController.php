<?php


namespace App\Http\Controllers;


use App\Helpers\Upload;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends ApiController {

    /**
     * @var string[] 特定类型的文件格式限定
     */
    private $fileTypeExtMap = [
        'posts'  => ['png', 'jpg', 'jpeg', 'gif'],
        'avatar' => ['png', 'jpg', 'jpeg']
    ];

    /**
     * 文件上传接口
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function uploadFile(Request $request): JsonResponse {
        $rules = [
            'type' => 'required',
            'file' => 'required|file',
        ];
        $this->validate($request, $rules);

        $type = $request->get('type');
        $file = $request->file('file');
        $fileExtension = $file->getClientOriginalExtension();

        /* 是否支持文件类型 */
        if (array_key_exists($type, $this->fileTypeExtMap)
            && !in_array($fileExtension, $this->fileTypeExtMap[$type], true)) {
            return $this->jsonResponse(['upload' => 'File type no supported for ' . $type . ': ' . $fileExtension], 10000);
        }

        $uploadRes = Upload::put($file, $type);
        $path = Upload::getUrlBySavePath($uploadRes['path']);

        return $this->jsonResponse(['path' => $path], 0);
    }
}