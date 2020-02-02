<?php

namespace GuoJiangClub\EC\Open\Core\Applicators;

use GuoJiangClub\Component\Order\Models\Adjustment;
use GuoJiangClub\Component\Discount\Distributors\PercentageIntegerDistributor;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Product\Models\Goods;

class TaxApplicator
{
    private $distributor;

    public function __construct(PercentageIntegerDistributor $distributor)
    {
        $this->distributor = $distributor;
    }

    public function apply(Order $order)
    {
        $uid = $order->user_id;

        $adjustment = new Adjustment([
            'type' => 'order_tax',
            'label' => '自助申请发票税费',
            'origin_type' => 'self_application_tax',
            'origin_id' => $uid
        ]);

        $amount = $order->total * Goods::TAX_RATE_SELF_APPLICATION / 100 ;

        if ($amount == 0) {
            return;
        }

        $adjustment->amount = $amount;

        $order->addAdjustment($adjustment);

        $splitDiscountAmount = $this->distributor->distribute($order->getItems()->pluck('total')->toArray(), $amount);

        $i = 0;

        foreach ($order->getItems() as $item) {
            $splitAmount = $splitDiscountAmount[$i++];
            $item->divide_order_discount += $splitAmount;
            $item->recalculateAdjustmentsTotal();
        }

    }

}