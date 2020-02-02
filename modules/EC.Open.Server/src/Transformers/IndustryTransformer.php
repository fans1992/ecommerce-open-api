<?php

namespace GuoJiangClub\EC\Open\Server\Transformers;

use GuoJiangClub\Component\NiceClassification\Industry;

class IndustryTransformer extends BaseTransformer
{
    protected $availableIncludes = ['children'];

    public function transformData($model)
    {
        return $model->toArray();
    }

    public function includeChildren(Industry $industry)
    {
        $transformer = new self();
        $transformer->setDefaultIncludes(['children']);
        return $this->collection($industry->children, $transformer);
    }
}
