<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use GuoJiangClub\EC\Open\Backend\Store\Model\Category;
use GuoJiangClub\EC\Open\Backend\Store\Model\GoodsCategory;
use GuoJiangClub\EC\Open\Backend\Store\Model\Industry;
use GuoJiangClub\EC\Open\Backend\Store\Model\NiceClassification;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\IndustryRepository;
use iBrand\Backend\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class IndustryController extends Controller
{
    protected $industryRepository;

    public function __construct(IndustryRepository $industryRepository)
    {
        $this->industryRepository = $industryRepository;
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

            $content->header('行业推荐类别列表');

            $content->breadcrumb(
                ['text' => '行业推荐类别管理', 'url' => 'store/specs', 'no-pjax' => 1],
                ['text' => '编辑行业推荐类别', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '行业管理']

            );

            $content->body(view('store-backend::industry.value.edit', compact('industry')));
        });
    }


    public function addClassification($industry_id)
    {
        $classifications = NiceClassification::query()->whereNull('parent_id')->orderBy('classification_code')->get();
        return view('store-backend::industry.value.add_value', compact('industry_id', 'classifications'));
    }


    public function classifictionStore(Request $request)
    {
        $input = $request->except('_token');

        if (isset($input['value'])) {
            $updateData = $input['value'];
            foreach ($updateData as $item) {
                SpecsValue::find($item['id'])->update($item);
            }
        }


        if (isset($input['delete_id'])) {
            $deleteData = $input['delete_id'];
            foreach ($deleteData as $item) {
                SpecsValue::find($item)->update(['status' => 0]);
            }
        }

        if (isset($input['add_value'])) {
            $createData = $input['add_value'];
            $industry = Industry::find($input['industry_id']);
            foreach ($createData as &$item) {
//                if (count(SpecsValue::judge($item['name'], $input['spec_id'])) OR !$item['name']) {
//                    return $this->ajaxJson(false);
//                }
                $item['nice_classification_parent_id'] = 0;
            }
            $industry->recommendClassifications()->sync($createData);
        }

        return $this->ajaxJson();

    }






}
