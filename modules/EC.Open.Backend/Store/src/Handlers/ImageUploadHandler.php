<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Handlers;

class ImageUploadHandler
{
    protected $allowed_ext = ['doc', 'docx', 'pdf', 'png', 'jpg', 'jpeg'];

    public function save($file, $type = 'image')
    {
        /** @var  $file \Illuminate\Http\UploadedFile */
        $format = $file->getClientOriginalExtension();
        if (!in_array($format, $this->allowed_ext)) {
            abort(500, "不支持" . $format . "文件格式");
        }

        $extension = strtolower($format);
        $file_prefix = str_plural($type);
        $disk = \Storage::disk('qiniu');

        $filename = $file_prefix . '/' . time() . '_' . str_random(10) . '.' . $extension;

        $responseUpload = $disk->put($filename, fopen($file, 'r'));

        if ($responseUpload === false) {
            return response()->json(['status' => false, 'message' => '上传七牛云失败']);
        }

        return $disk->getUrl($filename);
    }

}