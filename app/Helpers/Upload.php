<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class Upload
{
    /**
     * 上传文件
     * @param UploadedFile $file
     * @param string $dir
     * @return array
     * @throws Exception
     */
    public static function put(UploadedFile $file, $dir = ''): array {
        /* 获取文件后缀 */
        $ext = $file->getClientOriginalExtension();
        
        $fileName = md5(time() . random_int(0,10000)) . '.' . $ext; //随机名称

        $dirDate = date('Y').'/'.date('m').'/'.date('d').'/';

        $savePath = $dir . '/' . $dirDate . $fileName; // 存储到指定文件，

        Storage::disk('upload')->put($savePath, File::get($file));

        return ['code' => 0, 'path' => $savePath];
    }

    /**
     * 根据数据库保存的路径(包括文件名)获取下载的url
     * @param $savePath
     * @return string
     */
    public static function getUrlBySavePath($savePath)
    {
        $domainName = env('DN_CDN');
        if($savePath == ''){
            // 默认图片
            return $domainName . '/default_icon.png';
        }
        return $domainName . '/' . $savePath;
    }
    
    public static function crawl($fileUrl)
    {
        if(empty($fileUrl)){
            return false;
        }
        $fileName = '';
        $client = new \GuzzleHttp\Client(['verify' => false]);  //忽略SSL错误
        try {
            $response = $client->get($fileUrl);
            if ($response->getStatusCode() != 200) {
                return false;
            }
            $image = $response->getBody()->getContents();
            $info = getimagesizefromstring($image);
            $ext = image_type_to_extension($info[2]);
            $fileName = date('Y/m/d/') . md5(time().rand(0, 10000)) . $ext;
            Storage::disk('upload')->put($fileName, $image);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            // Connect error
            return false;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Client error
            return false;
        }
        
        return $fileName;
    }
}
