<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use GuoJiangClub\Component\NiceClassification\NiceClassification;
use GuoJiangClub\Component\NiceClassification\RepositoryContract as NiceClassificationRepository;
use GuoJiangClub\EC\Open\Backend\Store\Model\Industry;
use GuoJiangClub\EC\Open\Server\Transformers\NiceClassificationTransformer;
use Illuminate\Http\Request;
use Validator;

class IndustryController extends Controller
{

    public function __construct(NiceClassificationRepository $niceClassificationRepository)
    {
        $this->niceClassificationRepository = $niceClassificationRepository;
    }

    public function index(Request $request)
    {

        $niceClassificationList = NiceClassification::query()
            ->where('parent_id', $request->input('pid'))
            ->orderBy('classification_code')
            ->get();

        return $this->response()->collection($niceClassificationList, new NiceClassificationTransformer());
    }

    /**
     * 获取分类
     *
     * @return \Dingo\Api\Http\Response
     */
    public function getClassificationByGroupID()
    {
        $classifications = NiceClassification::query()->where('parent_id', request('parent_id'))->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);
        return $this->response()->collection($classifications, new NiceClassificationTransformer());

    }

    public function classifictionStore(Request $request, Industry $industry)
    {
        $input = $request->all();

//        if (isset($input['value'])) {
//            $updateData = $input['value'];
//            foreach ($updateData as $item) {
//                SpecsValue::find($item['id'])->update($item);
//            }
//        }
//
//        if (isset($input['delete_id'])) {
//            $deleteData = $input['delete_id'];
//            foreach ($deleteData as $item) {
//                SpecsValue::find($item)->update(['status' => 0]);
//            }
//        }

//        dd($input);
        $classifications[] = [
            'nice_classification_id' => $input['nice_classification_id'],
            'alias' => $input['alias'],
        ];

        foreach ($input['groups'] as $group) {
            $classifications[] = [
                'nice_classification_id' => $group['group_id'],
                'alias' => '',
            ];

            foreach ($group['products'] as $product) {
                $classifications[] = [
                    'nice_classification_id' => $product,
                    'alias' => '',
                ];
            }
        }

        foreach ($classifications as $item) {
            $classification = NiceClassification::query()->find($item['nice_classification_id']);

            $industry->recommendClassifications()->attach($classification, [
                'alias' => $item['alias'],
                'nice_classification_parent_id' => $classification['parent_id'] ?: 0,
            ]);
        }

        $this->success();

    }


}
