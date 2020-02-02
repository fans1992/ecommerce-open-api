<?php

namespace GuoJiangClub\EC\Open\Server\Transformers;

use GuoJiangClub\Component\Order\Models\Agreement;

class OrderAgreementTransformer extends BaseTransformer
{
    public function transformData($agreement)
    {
        $order = $agreement->order;

        return [
            'id' => $agreement->id,
            'party_a_name' => $agreement->party_a_name,
            'accept_name'=> $order->accept_name,
            'mobile' => $order->mobile,
            'email' => $order->email,
            'address' => $order->address,
            'service_items' => $agreement->service_items,
            'total'=> $order->total / 100,
            'agreement_no' => $agreement->agreement_no,
        ];
    }
}
