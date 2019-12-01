<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use GuoJiangClub\EC\Open\Backend\Store\Model\Industry;
use GuoJiangClub\EC\Open\Backend\Store\Model\NiceClassification;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\IndustryRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\NiceClassificationRepository;
use iBrand\Backend\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;
use Validator;

class IndustryController extends Controller
{
    protected $industryRepository;

    protected $niceClassificationRepository;

    public function __construct(IndustryRepository $industryRepository, NiceClassificationRepository $niceClassificationRepository)
    {
        $this->industryRepository = $industryRepository;
        $this->niceClassificationRepository = $niceClassificationRepository;

    }

    public function index()
    {
        $industries = $this->industryRepository->getLevelIndustry();

        return LaravelAdmin::content(function (Content $content) use ($industries) {

            $content->header('行业列表');

            $content->breadcrumb(
                ['text' => '行业列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '行业管理']
            );

            $content->body(view('store-backend::industry.index', compact('industries')));
        });
    }

    public function create()
    {
        $industries = $this->industryRepository->getLevelIndustry(0, '&nbsp;&nbsp;');
        foreach ($industries as $k => $c) {
            if ($c->level > 1) {
                unset($industries[$k]);
            }
        }
        $industry = new Industry();

        return LaravelAdmin::content(function (Content $content) use ($industries, $industry) {

            $content->header('添加分类');

            $content->breadcrumb(
                ['text' => '添加分类', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分类管理']
            );

            $content->body(view('store-backend::industry.create', compact('industries', 'industry')));
        });
    }

    public function store(Request $request)
    {
        $input = $request->except('_token', 'file');
        if (!$input['name']) {
            return $this->ajaxJson(false, [], 500, '请填写行业名称');
        }

        $industry = $this->industryRepository->create($input);

        $this->industryRepository->setIndustryLevel($industry->id, $input['parent_id']);

        return $this->ajaxJson();
    }

    public function edit($id)
    {
        $industry = $this->industryRepository->find($id);
        $industries = $this->industryRepository->getLevelIndustry(0, '&nbsp;&nbsp;');
        foreach ($industries as $k => $c) {
            if ($c->level > 1) {
                unset($industries[$k]);
            }
        }

        return LaravelAdmin::content(function (Content $content) use ($industries, $industry) {

            $content->header('修改行业');

            $content->breadcrumb(
                ['text' => '修改行业', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '行业管理']
            );

            $content->body(view('store-backend::industry.edit', compact('industries', 'industry')));
        });
    }

    public function update(Request $request, $id)
    {
        $input = $request->except(['_token', 'file']);
        if (!$input['name']) {
            return $this->ajaxJson(false, [], 500, '请填写分类名称');
        }
        $industry = $this->industryRepository->update($input, $id);

        $this->industryRepository->setIndustryLevel($industry->id, $input['parent_id']);
        $this->industryRepository->setSonIndustryLevel($industry->id);

        return $this->ajaxJson();
    }


    public function destroy()
    {
        $status = false;
        $id = request('id');
        if ($this->industryRepository->delIndustry($id)) {
            $status = true;
        }
        return $this->ajaxJson($status);
    }

    public function industry_sort(Request $request)
    {
        $input = $request->except('_token');
        $id = $request->input('id');
        $this->industryRepository->update($input, $id);
        return $this->ajaxJson();
    }


    /**
     * 推荐类别管理
     * @param $id
     */
    public function classifictionIndex($id)
    {
        $industry = Industry::query()->find($id);

        return LaravelAdmin::content(function (Content $content) use ($industry) {

            $content->header($industry->name. '---推荐类别列表');

            $content->breadcrumb(
                ['text' => '行业推荐类别管理', 'url' => 'store/industry', 'no-pjax' => 1],
                ['text' => '编辑行业推荐类别', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '行业管理']

            );

            $content->body(view('store-backend::industry.value.edit', compact('industry')));
        });
    }


    public function addClassification($industry_id)
    {
        $classifications = NiceClassification::query()->where('parent_id', 0)->orderBy('classification_code')->get();
        return view('store-backend::industry.value.add_value', compact('industry_id', 'classifications'));
    }


    public function classifictionStore(Request $request)
    {
        $input = $request->except('_token');

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

        if (isset($input['add_value'])) {
            $createData = $input['add_value'];

            /** @var Industry $industry */
            $industry = Industry::query()->find($input['industry_id']);

            foreach ($createData as $item) {
                $classification = NiceClassification::query()->find($item['nice_classification_id']);

                $industry->recommendClassifications()->attach($classification, [
                    'alias' => $item['alias'],
                    'nice_classification_parent_id' => $classification['parent_id'] ?: 0,
                ]);
            }
        }

        return $this->ajaxJson();

    }


    /**
     * 获取推荐分类列表api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecommendData(Request $request)
    {
        /** @var Industry $industry */
        $industry = Industry::query()->find($request->input('industry_id'));
        $data = $industry->recommendClassifications()->where('nice_classification_parent_id', 0)->orderBy('id', 'desc')->paginate(10);

        return $this->ajaxJson(true, $data);
    }



    /**
     * 编辑单个推荐分类
     *
     * @return mixed
     */
    public function editClassification(Request $request)
    {
        /** @var Industry $industry */
        $industry = Industry::query()->find($request->input('industry_id'));

        $classification_id = $request->input('nice_classification_id');
        $classification =  $industry->recommendClassifications()->wherePivot('nice_classification_id', $classification_id)->first();

        return view('store-backend::industry.value.edit_value', compact('classification', 'classification_id'));
    }


    /**
     * 保存单个推荐分类
     * @param Request $request
     * @return mixed
     */
    public function storeClassification(Request $request)
    {
        $input = $request->except('_token');
        $industryId = $request->input('industry_id');

        $rules = [
            'alias' => 'required',
        ];
        $message = array(
            "alias.required" => "分类别名 不能为空",
        );

        $attributes = array(
            "alias" => '分类别名',
        );

        $validator = Validator::make(
            $request->all(),
            $rules,
            $message,
            $attributes
        );
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();

            return $this->ajaxJson(false, [], 300, $show_warning);

        }

        /** @var Industry $industry */
        $industry = Industry::query()->find($industryId);
        $industry->recommendClassifications()->updateExistingPivot($request->input('nice_classification_id'), $input);

        return $this->ajaxJson();

    }

    /**
     * 删除类别推荐
     *
     * @param $id
     */
    public function delClassification(Request $request)
    {
        /** @var Industry $industry */
        $industry = Industry::query()->find($request->input('industry_id'));

        $industry->recommendClassifications()->detach($request->input('nice_classification_id'));
        return $this->ajaxJson(true);
    }


    /**
     * 行业推荐获取商标分类数据
     * @return mixed
     */
    public function getClassificationByGroupID()
    {
        if (request()->has('type-click-category-button')) {
            $classifications = NiceClassification::query()->where('parent_id', request('parentId'))->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);

            return response()->json($classifications);
        } elseif (request()->has('type-select-category-button')) {
            $classifications = NiceClassification::query()->where('parent_id', request('parentId'))->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);
            return view('store-backend::industry.value.classification-item', compact('classifications'));

        } else {
            $classifications = NiceClassification::query()->where('parent_id', 0)->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);
//            $classifications = $this->niceClassificationRepository->getOneLevelNiceClassification();
            return view('store-backend::industry.value.classification-item', compact('classifications'));
        }
    }





}
