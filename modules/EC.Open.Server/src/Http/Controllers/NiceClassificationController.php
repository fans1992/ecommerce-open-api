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

use GuoJiangClub\Component\NiceClassification\NiceClassification;
use GuoJiangClub\Component\NiceClassification\RepositoryContract as NiceClassificationRepository;
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
//            ->orderBy('classification_code')
            ->get();

        return $this->response()->collection($niceClassificationList, new NiceClassificationTransformer());
    }

}
