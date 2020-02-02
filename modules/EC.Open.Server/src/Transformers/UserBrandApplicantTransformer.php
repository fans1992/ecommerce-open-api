<?php

namespace GuoJiangClub\EC\Open\Server\Transformers;

class UserBrandApplicantTransformer extends BaseTransformer
{
    public static $excludeable = [
        'user_id',
    ];

    public function transformData($model)
    {
        return array_except($model->toArray(), self::$excludeable);
    }
}
