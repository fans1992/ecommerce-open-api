<?php

namespace GuoJiangClub\EC\Open\Server\Transformers;


class UserClassificationTransformer extends BaseTransformer
{
    protected $availableIncludes = ['children'];

    public function transformData($model)
    {
        return $model->toArray();
    }

}
