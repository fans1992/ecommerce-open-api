<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use GuoJiangClub\Component\NiceClassification\NiceClassification;
use Intervention\Image\ImageManager;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Image;
use Cache;
use Storage;

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

        $path = 'brand/create/' . date('Ymd') . '/' . generaterandomstring() . '.png';
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
        $path = 'brand/upload/' . date('Ymd') . '/' . generaterandomstring() . '.' . $extension;;
        $url = upload_image($path, $img->__toString());

        return $this->success(['url' => $url]);
    }

    /**
     * 导出领取记录
     * @return mixed
     */
    public function getClassificationsExportData(Request $request)
    {
        $classificationIds = array_column($request->input('classifications'), 'id');
        $classifications = NiceClassification::query()->whereIn('id', $classificationIds)->get();

        $excelData = [];
        if (count($classifications) > 0) {
            $i = 0;
            foreach ($classifications as $classification) {
//                if (isset($item->bind)) {
//                    $item->open_id = $item->bind->open_id;
//                }

                $excelData[$i][] = $classification->classification_code;
                $excelData[$i][] = $classification->classification_name;
                $i++;
            }
        }
//
//        $cacheName = generate_export_cache_name('export_classification_get_cache_');
//
//        if (Cache::has($cacheName)) {
//            $cacheData = Cache::get($cacheName);
//            Cache::put($cacheName, array_merge($cacheData, $excelData), 300);
//        } else {
//            Cache::put($cacheName, $excelData, 300);
//        }

        Storage::makeDirectory('public/exports');

        $title = ['商标分类编号', '商标分类名称'];
        $prefix = '商标注册共大类_';
        $fileName = generate_export_name($prefix);

//        $excelData = $this->cache->pull($cache);

        set_time_limit(10000);
        ini_set('memory_limit', '300M');

        $excel = Excel::create($fileName, function ($excel) use ($excelData, $title) {
            $excel->sheet('Sheet1', function ($sheet) use ($excelData, $title) {
                $sheet->prependRow(1, $title);
                $sheet->rows($excelData);
//                    $sheet->setWidth(array(
//                        'A' => 5,
//                        'B' => 20,
//                        'C' => 10,
//                        'D' => 40,
//                        'E' => 5,
//                        'F' => 10,
//                        'G' => 10,
//                        'H' => 5,
//                        'I' => 5,
//                        'J' => 20,
//                        'K' => 10,
//                        'L' => 30,
//                        'M' => 30,
//                        'N' => 80,
//                        'O' => 100
//                    ));
            });
        })->store('xls', storage_path('exports'), false);

        $result = \File::move(storage_path('exports') . '/' . $fileName . '.xls', storage_path('app/public/exports/') . $fileName . '.xls');

        if (!$result) {
            return $this->failed('failed');
        }

        if ($request->input('action') === 'preview') {
            return $this->success(['url' => 'https://view.officeapps.live.com/op/view.aspx?src=' . $fileName . '.xls']);
        }

        return $this->success(['url' => url('/storage/exports/' . $fileName . '.xls')]);

    }

}
