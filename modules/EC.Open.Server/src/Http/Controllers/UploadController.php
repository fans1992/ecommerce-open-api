<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use Illuminate\Http\Request;
use OCR;

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

        $ocr = app('ocr');
        $result = $ocr->businessLicense($file);
        dd($result);

        //获取扩展名，上传OSS
        $extension = strtolower($file->getClientOriginalExtension());
        $path = 'brand/' . $dir . '/' . date('Ymd') . '/' . generaterandomstring() . '.' . $extension;
        $url = upload_image($path, fopen($file, 'r'));

//        $result = OCR::baidu()->idcard('http://aliyuncdn.foridom.com/brand/id_card/20191226/rxpYPwbJlf.jpg', [
//            'detect_direction'      => false,      //是否检测图像朝向
//            'id_card_side'          => 'front',    //front：身份证正面；back：身份证背面 （注意，该参数必选）
//            'detect_risk'           => false,      //是否开启身份证风险类型功能，默认false
//        ]);

        return $this->success(['url' => $url]);

    }

}
