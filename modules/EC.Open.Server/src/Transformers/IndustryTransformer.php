<?php

namespace GuoJiangClub\EC\Open\Server\Transformers;

class IndustryTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return $model->toArray();
    }
}
