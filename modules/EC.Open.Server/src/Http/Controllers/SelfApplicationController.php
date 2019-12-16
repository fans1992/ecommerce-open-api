<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use Intervention\Image\ImageManager;
use Illuminate\Http\Request;
use Validator;
use Image;

class SelfApplicationController extends Controller
{
    /**
     * 生成商标图片
     *
     * @param ImageManager $image
     * @return \Dingo\Api\Http\Response
     */
    public function createBrandImage(ImageManager $image)
    {
        $img = $image->canvas(150, 150, '#fff');

        $name = request('brand_name');
        $length = mb_strlen($name);

        switch ($length) {
            case $length >= 0 && $length < 2:
                $size = 80;
                break;
            case $length >= 2 && $length < 4:
                $size = 40;
                break;
            case $length >= 4 && $length < 8:
                $size = 20;
                break;
            case $length >= 8 && $length < 15:
                $size = 10;
                break;
            case $length >= 15 && $length < 30:
                $size = 5;
                break;
            default:
                $size = 1;
        }

        $img->text($name, 75, 75, function ($font) use ($size) {

            $font->file(public_path('font/msyh.ttf'));

            $font->size($size);

            $font->valign('middle');

            $font->align('center');

            $font->color('#000000');
        })->stream();

        $path = 'brand/create/'. date('Ymd'). '/' . generaterandomstring() . '.png';
        $url = upload_image($path, $img->__toString());

        return $this->success(['url' => $url]);
    }

    /**
     * 手动上传商标图样
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function uploadBrandImage(Request $request)
    {
        //TODO: 需要验证是否传入avatar_file 参数
        $file = $request->file('brand_image');

        //图片处理
        $img = Image::make($file);
        $img->resize(250, null, function ($constraint) {
            $constraint->aspectRatio();
        })->crop(250, 250, 0, 0)->stream();

        //获取扩展名，上传OSS
        $extension = $file->getClientOriginalExtension();
        $path = 'brand/upload/'. date('Ymd'). '/' . generaterandomstring() . '.' . $extension;;
        $url = upload_image($path, $img->__toString());

        return $this->success(['url' => $url]);
    }


}
