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

        $industries = $query->get();

        return $this->response()->collection($industries, new IndustryTransformer());
    }

    public function recommendationIndex()
    {
        
    }



}
