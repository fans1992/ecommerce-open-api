<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * 上传图片
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('file');
        $dir = $request->get('fileType');

        //获取扩展名，上传OSS
        $extension = strtolower($file->getClientOriginalExtension());
        $path = 'brand/' . $dir . '/' . date('Ymd') . '/' . generaterandomstring() . '.' . $extension;
        $url = upload_image($path, fopen($file, 'r'));

        return $this->success(['url' => $url]);

    }

}
