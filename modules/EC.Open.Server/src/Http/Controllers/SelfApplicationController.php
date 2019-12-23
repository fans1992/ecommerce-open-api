<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use GuoJiangClub\Component\NiceClassification\Models\UserClassification;
use GuoJiangClub\Component\NiceClassification\NiceClassification;
use GuoJiangClub\EC\Open\Server\Transformers\UserClassificationTransformer;
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
        $length = strlen($name);
        $chinese = $this->isChinese($name);

        switch ($length) {
            case $length >= 0 && $length < 2:
                $size = 90;
                break;
            case $length >= 2 && $length < 4:
                $size = 70;
                break;
            case $length >= 4 && $length < 7:
                $size = 40;
                break;
            case $length >= 7 && $length < 10:
                $size = $chinese ? 35 :20;
                break;
            case $length >= 10 && $length < 15:
                $size = $chinese ? 30 :15;
                break;
            case $length >= 15 && $length < 20:
                $size = $chinese ? 20 :12;
                break;
            case $length >= 20 && $length < 25:
                $size = $chinese ? 20 :5;
                break;
            default:
                $size = 1;
        }

        $img->text($name, 75, 75, function ($font) use ($size) {

            $font->file(public_path('font/SourceHanSansCN-Normal-2.otf'));

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
     * 自助申请方案下载
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response|mixed
     */
    public function getClassificationsExportData(Request $request)
    {
        $classificationIds = array_column($request->input('classifications'), 'id');
        $classifications = NiceClassification::query()->whereIn('id', $classificationIds)
            ->with(['parent.parent:id,classification_name,classification_code,parent_id,level'])
            ->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);


        foreach ($classifications as $classification) {
            //群组
            if (!$classifications->contains('id', $classification->parent->id)) {
                $classifications->push($classification->parent);
            }

            //分类
            if (!$classifications->contains('id', $classification->parent->parent->id)) {
                $classifications->push($classification->parent->parent);
            }
        }

        $classificationsTree = $classifications->toTree();

        $excelData = [];
        $i = 2;
        $colorLine = [];
        if (count($classificationsTree) > 0) {
            foreach ($classificationsTree as $topClassification) {
//                if (isset($item->bind)) {
//                    $item->open_id = $item->bind->open_id;
//                }
                $excelData[] = [
                    '第' . $topClassification->classification_code . '类',
                    $topClassification->classification_name,
                ];
                $colorLine[] = $i;
                $i++;
                foreach ($topClassification->children as $group) {
                    foreach ($group->children as $product) {
                        $excelData[] = [$product->classification_code, $product->classification_name];
                        $i++;
                    }
                }

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

        $excel = Excel::create($fileName, function ($excel) use ($excelData, $title, $colorLine) {
            $excel->sheet('Sheet1', function ($sheet) use ($excelData, $title, $colorLine) {
                $sheet->prependRow(1, $title);
                $sheet->rows($excelData);

                $sheet->setWidth([
                    'A' => 30,
                    'B' => 50,
                ]);

                $sheet->setHeight([
                    1 => 50,
                ]);

                $sheet->setStyle([
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 15,
                        'bold' => true,
                    ]
                ]);

                // Alignment
                $style = array(
                    'alignment' => array(
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    )
                );
                $sheet->getDefaultStyle()->applyFromArray($style);

                for ($i = 2; $i <= count($excelData) + 1; $i++) {
                    if (in_array($i, $colorLine))
                        $sheet->row($i, function ($row) {
                            $row->setBackground('#AAAAFF');
                        });
                }

            });
        })->store('xls', storage_path('exports'), false);

        $result = \File::move(storage_path('exports') . '/' . $fileName . '.xls', storage_path('app/public/exports/') . $fileName . '.xls');

        if (!$result) {
            return $this->failed('failed');
        }

        $url = url('/storage/exports/' . $fileName . '.xls');
        if ($request->input('action') === 'preview') {
            return $this->success(['url' => 'https://view.officeapps.live.com/op/view.aspx?src=' . $url]);
        }

        return $this->success(['url' => $url]);

    }

    /**
     * 用户历史申请分类记录
     *
     * @return \Dingo\Api\Http\Response
     */
    public function userRecordIndex()
    {
        $classificationList = UserClassification::all();

        return $this->response()->collection($classificationList, new UserClassificationTransformer());
    }

    /**
     * 判断字符串是不是全为中文
     *
     * @param $string
     * @return bool
     */
    protected function isChinese($string)
    {
        if (preg_match("/^[\x7f-\xff]+$/", $string)){
            //全是汉字
            return true;
        }

        return false;
    }

}
