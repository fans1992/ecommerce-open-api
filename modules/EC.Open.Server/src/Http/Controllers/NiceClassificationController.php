<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
            ->get();

        return $this->response()->collection($niceClassificationList, new NiceClassificationTransformer());
    }

    /**
     * 行业列表
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
     * 行业树
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
     * 行业推荐类别列表
     *
     * @param Industry $industry
     * @return \Dingo\Api\Http\Response
     */
    public function recommendationIndex(Industry $industry)
    {
        $classifications = NiceClassification::query()->where('parent_id', 0)->get(['id', 'classification_name', 'classification_code', 'parent_id', 'level']);

        $recommendClassifications = $industry->recommendClassifications->pluck('pivot.alias','id')->all();
        foreach ($classifications as $classification) {
            if (array_key_exists($classification->id, $recommendClassifications)) {
                $classification->classification_name = $recommendClassifications[$classification->id] ?: $classification->classification_name  ;
                $classification->recommendation = true;
            } else {
                $classification->recommendation = false;
            }
        }

        return $this->response()->collection($classifications, new NiceClassificationTransformer());

    }



}
