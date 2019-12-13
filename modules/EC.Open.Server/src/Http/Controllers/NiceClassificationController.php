<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use Dingo\Api\Transformer\Factory;
use GuoJiangClub\Component\NiceClassification\Industry;
use GuoJiangClub\Component\NiceClassification\NiceClassification;
use GuoJiangClub\Component\NiceClassification\RepositoryContract as NiceClassificationRepository;
use GuoJiangClub\EC\Open\Server\Transformers\IndustryTransformer;
use GuoJiangClub\EC\Open\Server\Transformers\NiceClassificationTransformer;
use Illuminate\Http\Request;
use Validator;

class NiceClassificationController extends Controller
{
    protected $niceClassificationRepository;

    public function __construct(NiceClassificationRepository $niceClassificationRepository)
    {
        $this->niceClassificationRepository = $niceClassificationRepository;
    }

    /**
     * 商标分类列表
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request)
    {

        $niceClassificationList = NiceClassification::query()
            ->where('parent_id', $request->input('pid'))
            ->orderBy('classification_code')
            ->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);

        return $this->response()->collection($niceClassificationList, new NiceClassificationTransformer());
    }

    /**
     * 行业列表('保障申请')
     *
     * @param Industry $industry
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function industryIndex(Industry $industry, Request $request)
    {
        $query = $industry->query();

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        $industries = $query->whereIsRoot()->get();

        return $this->response()->collection($industries, new IndustryTransformer());
    }


    /**
     * 行业树(自助申请)
     *
     * @param Request $request
     * @param Factory $transformerFactory
     * @return \Dingo\Api\Http\Response
     */
    public function industryTree(Request $request, Factory $transformerFactory)
    {
        if ($request->input('include') == 'children') {
            $industries = Industry::defaultOrder()->get()->toTree();
            // 关闭 Dingo 的预加载
            $transformerFactory->disableEagerLoading();
        } else {
            $industries = Industry::whereIsRoot()->defaultOrder()->get();
        }

        return $this->response()->collection($industries, new IndustryTransformer());
    }


    /**
     * 行业推荐类别列表(保障申请)
     *
     * @param Industry $industry
     * @return \Dingo\Api\Http\Response
     */
    public function recommendationIndex(Industry $industry)
    {
        $classifications = NiceClassification::query()
            ->where('parent_id', 0)
            ->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);

        $recommendClassifications = $industry->recommendClassifications->pluck('pivot.alias', 'id')->all();
        foreach ($classifications as $classification) {
            if (array_key_exists($classification->id, $recommendClassifications)) {
                $classification->classification_name = $recommendClassifications[$classification->id] ?: $classification->classification_name;
                $classification->recommendation = true;
            } else {
                $classification->recommendation = false;
            }
        }

        return $this->response()->collection($classifications, new NiceClassificationTransformer());

    }

    /**
     * 行业推荐分类树
     *
     * @param Request $request
     * @param Industry $industry
     * @param Factory $transformerFactory
     * @return \Dingo\Api\Http\Response
     */
    public function recommendationTree(Request $request, Industry $industry, Factory $transformerFactory)
    {

        if ($request->include === 'children') {
            $niceClassifications = $industry->recommendClassifications->toTree();
            // 关闭 Dingo 的预加载
            $transformerFactory->disableEagerLoading();
        } else {
            $niceClassifications = NiceClassification::whereIsRoot()->defaultOrder()->get();
        }

        return $this->response->collection($niceClassifications, new NiceClassificationTransformer());
    }

    /**
     * 商标搜索(自助申请)
     *
     * @param Request $request
     * @param NiceClassification $niceClassification
     * @param Factory $transformerFactory
     * @return \Dingo\Api\Http\Response
     */
    public function search(Request $request, NiceClassification $niceClassification, Factory $transformerFactory)
    {
        // 创建一个查询构造器
        $builder = $niceClassification->query();

        // 判断是否有提交 search 参数模糊搜索，如果有就赋值给 $search 变量
        if ($search = $request->input('search', '')) {
            $like = '%' . $search . '%';

            // 模糊搜索商品
            $builder->where('classification_name', 'like', $like)
                ->where('level', 3);
//                ->orWhereHas('children', function ($query) use ($like) {
//                    $query->where('classification_name', 'like', $like)
//                        ->orWhereHas('children', function ($query) use ($like) {
//                            $query->where('classification_name', 'like', $like);
//                        });
//                });
        }

        //商品集合
        if ($request->include === 'children') {
            $classifications = $builder->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);
            // 关闭 Dingo 的预加载
            $transformerFactory->disableEagerLoading();
        } else {
            $classifications = NiceClassification::whereIsRoot()->defaultOrder()->get();
        }

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

        return $this->response->collection($classifications->toTree(), new NiceClassificationTransformer());

    }


}
