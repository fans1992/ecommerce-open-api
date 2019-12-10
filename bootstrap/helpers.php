<?php

use Illuminate\Support\Facades\Storage;

function ngrok_url($routeName, $parameters = [])
{
    // 开发环境，并且配置了 NGROK_URL
    if (app()->environment('local') && $url = config('app.ngrok_url')) {
        // route() 函数第三个参数代表是否绝对路径
        //todo
        //return $url . \Dingo\Api\Facade\API::route($routeName, $parameters, false);
        return $url . '/api/payment/alipay/notify';
    }

    return route($routeName, $parameters);
}


/**
 * @param $path
 * @param $file
 * @param string $drive
 * @return bool
 */
function upload_image($path, $file, $drive = 'oss')
{
    if (!$path) return false;

    //将图片上传到OSS中，并返回图片路径信息 值如:avatar/WsH9mBklpAQUBQB4mL.jpeg
    $disk = Storage::disk($drive);
    $disk->put($path, $file);

    //由于图片不在本地，所以我们应该获取图片的完整路径，
    //值如：https://test.oss-cn-hongkong.aliyuncs.com/avatar/8GdIcz1NaCZ.jpeg
    switch ($drive) {
        case 'qiniu':
            return $disk->getUrl($path);
        case 'oss':
            return $disk->url($path);
    }
}


/**
 * 生成随机字符串
 *
 * @param int $length
 * @return string
 */
function generaterandomstring($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; ++$i) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}