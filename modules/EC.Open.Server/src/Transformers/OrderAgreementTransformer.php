<?php

namespace GuoJiangClub\EC\Open\Server\Transformers;

class OrderAgreementTransformer extends BaseTransformer
{
    public static $excludeable = [
        'order_id',
    ];

    public function transformData($model)
    {
        return array_except($model->toArray(), self::$excludeable);
    }
}
