<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Transformers;

use GuoJiangClub\Component\NiceClassification\NiceClassification;

class NiceClassificationTransformer extends BaseTransformer
{
    protected $availableIncludes = ['children'];

    public function transformData($model)
    {
        return $model->toArray();
    }

    public function includeChildren(NiceClassification $niceClassification)
    {
        $transformer = new self();
        $transformer->setDefaultIncludes(['children']);
        return $this->collection($niceClassification->children, $transformer);
    }
}
