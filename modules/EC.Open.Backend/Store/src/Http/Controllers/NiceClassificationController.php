<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use GuoJiangClub\EC\Open\Backend\Store\Model\Category;
use GuoJiangClub\EC\Open\Backend\Store\Model\GoodsCategory;
use GuoJiangClub\EC\Open\Backend\Store\Model\NiceClassification;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\NiceClassificationRepository;
use iBrand\Backend\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class NiceClassificationController extends Controller
{
    protected $niceClassificationRepository;

    public function __construct(NiceClassificationRepository $niceClassificationRepository)
    {
        $this->niceClassificationRepository = $niceClassificationRepository;
    }

    public function index()
    {
//        $test = NiceClassification::descendantsAndSelf(1);
//        $test = $cat->getDescendants()->toArray();

        $parentClassifications = $this->niceClassificationRepository->getParentClassifications();
        $niceClassifications = $this->niceClassificationRepository->getLevelNiceClassification($parentClassifications->first());

        return LaravelAdmin::content(function (Content $content) use ($niceClassifications, $parentClassifications) {

            $content->header('商标分类列表');

            $content->breadcrumb(
                ['text' => '商标分类列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '商标分类']
            );

            $content->body(view('store-backend::classification.index', compact('niceClassifications', 'parentClassifications')));
        });
    }

    public function create()
    {
        $categories = $this->categoryRepository->getLevelCategory(0, '&nbsp;&nbsp;');
        foreach ($categories as $k => $c) {
            if ($c->level > 1) {
                unset($categories[$k]);
            }
        }
        $category = new Category();

        return LaravelAdmin::content(function (Content $content) use ($categories, $category) {

            $content->header('添加分类');

            $content->breadcrumb(
                ['text' => '添加分类', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分类管理']
            );

            $content->body(view('store-backend::category.create', compact('categories', 'category')));
        });
    }

    public function store(Request $request)
    {
        $input = $request->except('_token', 'file');
        if (!$input['name']) {
            return $this->ajaxJson(false, [], 500, '请填写分类名称');
        }

        $category = $this->categoryRepository->create($input);

        $this->categoryRepository->setCategoryLevel($category->id, $input['parent_id']);

        return $this->ajaxJson();
    }

    public function edit($id)
    {
        $category = $this->categoryRepository->find($id);
        $categories = $this->categoryRepository->getLevelCategory(0, '&nbsp;&nbsp;');
        foreach ($categories as $k => $c) {
            if ($c->level > 1) {
                unset($categories[$k]);
            }
        }

        return LaravelAdmin::content(function (Content $content) use ($categories, $category) {

            $content->header('修改分类');

            $content->breadcrumb(
                ['text' => '修改分类', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分类管理']
            );

            $content->body(view('store-backend::category.edit', compact('categories', 'category')));
        });
    }

    public function update(Request $request, $id)
    {
        $input = $request->except(['_token', 'file']);
        if (!$input['name']) {
            return $this->ajaxJson(false, [], 500, '请填写分类名称');
        }
        $category = $this->categoryRepository->update($input, $id);

        $this->categoryRepository->setCategoryLevel($category->id, $input['parent_id']);
        $this->categoryRepository->setSonCategoryLevel($category->id);

        return $this->ajaxJson();
    }

    public function check()
    {
        $status = true;
        $id = request('id');
        $ids = Category::where('parent_id', $id)->pluck('id')->toArray();
        array_push($ids, $id);
        $goods = GoodsCategory::whereIn('category_id', $ids);
        if ($goods->first()) {
            $status = false;
        }
        return $this->ajaxJson($status);
    }

    public function destroy()
    {
        $status = false;
        $id = request('id');
        if ($this->categoryRepository->delCategory($id)) {
            $status = true;
        }
        return $this->ajaxJson($status);
    }

    public function category_sort(Request $request)
    {
        $input = $request->except('_token');
        $id = $request->input('id');
        $this->categoryRepository->update($input, $id);
        return $this->ajaxJson();
    }

}
