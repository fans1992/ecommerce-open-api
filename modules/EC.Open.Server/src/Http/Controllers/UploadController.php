<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use Illuminate\Http\Request;
use OCR;
use Image;

class UploadController extends Controller
{
    /**
     * 上传图片
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function credentialsStore(Request $request)
    {
        $file = $request->file('file');
        $dir = $request->get('fileType');

        //图片处理
        $img = Image::make($file);
        $img->resize(1000, null, function ($constraint) {
            $constraint->aspectRatio();
        })->stream();

        //获取扩展名，上传OSS
        $extension = strtolower($file->getClientOriginalExtension());
        $path = 'brand/' . $dir . '/' . date('Ymd') . '/' . generaterandomstring() . '.' . $extension;
        $url = upload_image($path, $img->__toString());

        return $this->success(['url' => $url]);

    }

}
