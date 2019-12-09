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


    /**
     * 行业推荐类别保存
     *
     * @param Request $request
     * @param Industry $industry
     * @return \Illuminate\Http\JsonResponse
     */
    public function classifictionStore(Request $request)
    {
        $input = $request->except('_token');
        $industry = Industry::query()->find($input['industry_id']);

//        if ($classification = $industry->recommendClassifications()->find($input['top_nice_classification_id'])) {
//            return $this->ajaxJson(false, [], 500, '无法重复添加,该行业对应' . $classification->classification_code . '分类已存在相关记录');
//        }

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

        $classifications[] = [
            'nice_classification_id' => $input['top_nice_classification_id'],
            'alias' => $input['alias'] ?? '',
        ];

        $childrenIds = $input['category_id'] ?? [];
        foreach (array_unique($childrenIds) as $classification) {
            $classifications[] = [
                'nice_classification_id' => $classification,
                'alias' => '',
            ];
        }

        foreach ($classifications as $item) {
            $classification = NiceClassification::query()->find($item['nice_classification_id']);
            if ($industry->recommendClassifications()->find($classification->id)) {
                continue;
            }

            $industry->recommendClassifications()->attach($classification, [
                'alias' => $item['alias'],
                'nice_classification_parent_id' => $classification['parent_id'] ?: 0,
            ]);
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
    public function updateClassification(Request $request)
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

        $niceClassificationId = $request->input('nice_classification_id');

        //根节点以及所有子节点
        $niceClassificationIds = $industry->recommendClassifications()
            ->where('nice_classification.id',  $niceClassificationId)
            ->orWhere('parent_id', $niceClassificationId)
            ->orWhereHas('parent', function ($query) use ($niceClassificationId) {
                $query->where('parent_id', $niceClassificationId);
            })
            ->pluck('nice_classification.id')->toArray();

        $industry->recommendClassifications()->detach($niceClassificationIds);
        return $this->ajaxJson(true);
    }


    /**
     * 行业推荐获取商标分类数据
     * @return mixed
     */
    public function getClassificationByGroupID()
    {
        if (request()->has('type-click-category-button')) {
            //点击分类按钮
            $query = NiceClassification::query()->where('parent_id', request('parentId'));
            if ($search = request()->input('search')) {
                $query->where('classification_name', $search);
            }

            $classifications = $query->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);

            return response()->json($classifications);

        } elseif (request()->has('type-select-category-button')) {
            //下拉框筛选分类
            $parentId = request('parentId');

            //二级分类
            $niceClassificationQuery = NiceClassification::query()->where('parent_id', $parentId);
            if ($search = request()->input('search')) {
                $niceClassificationQuery->whereHas('children', function ($query) use($search) {
                    $query->where('classification_name', $search);
                });
            }
            $classifications = $niceClassificationQuery->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);

            $industry = Industry::query()->find(request('industryId'));

            $recommendClassifications = $industry->recommendClassifications()
                ->where('parent_id', $parentId)
                ->orWhereHas('parent', function ($query) use($parentId) {
                    $query->where('parent_id', $parentId);
                })
                ->get(['parent_id', 'nice_classification.id']);

            $cateIds = $recommendClassifications->pluck('id')->all();

            if ($search = request()->input('search')) {
                $cateNames = collect();
            } else {
                $cateNames = $industry->recommendClassifications()->where('parent_id', $parentId)->get();
                foreach ($cateNames as $cateName) {
                    $cateName->children =  $industry->recommendClassifications()->where('parent_id', $cateName->id)->get();
                }

            }

//            $category_ids = [];
//            foreach ($recommendClassifications as $recommendClassification) {
//                $category_ids[] = [$recommendClassification->parent_id, $recommendClassification->id];
//            }

            //三级分类
            $categoriesLevelTwo = [];
            foreach ($classifications as $classification) {
                if (in_array($classification->id, $cateIds)) {
                    $productQuery = NiceClassification::query()->where('parent_id', $classification->id);
                    if ($search = request()->input('search')) {
                        $productQuery->where('classification_name', $search);
                    }
                    $categoriesLevelTwo[] = $productQuery->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);
                }
            }

            return view('store-backend::industry.value.classification-item', compact('classifications', 'categoriesLevelTwo', 'cateNames', 'cateIds'));

        } else {
//            $parentId = request('parentId');
            $query = NiceClassification::query();
            if ($search = request()->input('search')) {
                $like = $search;
                $query->where('classification_name', $like);

//                $like = $search;
//                $query->where(function ($query) use($like) {
//                    $query->where('classification_name', $like)
//                        ->orWhereHas('children', function ($query) use($like) {
//                            $query->where('classification_name', $like)
//                                ->orWhereHas('children', function ($query) use($like) {
//                                    $query->where('classification_name', $like);
//                                });
//                        });
//                });
            }

            $classification = $query->first(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);
            if (!$classification) {
                return response()->json([]);
            }

            $classifications[] = $classification->parent->parent;
            return response()->json($classifications);

//            return view('store-backend::industry.value.classification-item', compact('classifications'));
        }
    }





}
