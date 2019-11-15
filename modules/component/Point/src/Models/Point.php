<?php

/*
 * This file is part of ibrand/point.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Point\Models;

use GuoJiangClub\Component\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    const POINT_ACTION_ORDER_ITEM = 'order_item';
    const POINT_ACTION_ORDER_DISCOUNT = 'order_discount';
    const POINT_ACTION_ORDER_CANCELED = 'order_canceled';
    const POINT_ACTION_COUPON_EXCHANGE = 'coupon_exchange';

    public static $pointActionMap = [
        self::POINT_ACTION_ORDER_ITEM => '订单商品获得积分',
        self::POINT_ACTION_ORDER_DISCOUNT => '订单折扣使用积分',
        self::POINT_ACTION_ORDER_CANCELED => '取消订单返还积分',
        self::POINT_ACTION_COUPON_EXCHANGE => '兑换优惠券消耗积分',
    ];

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $prefix = config('ibrand.app.database.prefix', 'ibrand_');

        $this->setTable($prefix . 'point');
    }

    public function scopeValid($query)
    {
        $current = date('Y-m-d H:i:s', time());

        return $query->where('valid_time', '>=', $current)->orWhere('valid_time', null)->where('status', 1);
    }

    public function scopeOverValid($query)
    {
        $current = date('Y-m-d H:i:s', time());

        return $query->where('valid_time', '<', $current)->where('valid_time', '!=', null)->where('status', 1);
    }

    public function scopeWithinTime($query)
    {
        $current = date('Y-m-d H:i:s', time());

        return $query->whereRaw('valid_time>\'' . $current . '\' or valid_time = null');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function point_order()
    {
        return $this->hasOne(Point::class, 'id')
            ->where('item_type', 'GuoJiangClub\Component\Order\Models\Order')->with('order');
    }

    public function point_order_item()
    {

        return $this->hasOne(Point::class, 'id')
            ->where('item_type', 'GuoJiangClub\Component\Order\Models\OrderItem')->with('order_item.order');
    }
}
