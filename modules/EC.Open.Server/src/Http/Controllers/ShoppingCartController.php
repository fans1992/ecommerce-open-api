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

use Cart;
use GuoJiangClub\Component\Product\Models\AttributeValue;
use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Component\Product\Models\Product;

class ShoppingCartController extends Controller
{
    protected $discountService;
    protected $goodsUserLimit;
    protected $goodsLimit;

    public function __construct()
    {
    }

    public function index()
    {
        if (empty(request()->all())) {
            $carts = Cart::search(['channel' => 'normal']);
        } else {
            $carts = Cart::search(request()->all());
        }

        //返回购物车中目前所有商品的库存
        foreach ($carts as $item) {
            if ($item and $item->model) {
                $item['stock_qty'] = $item->model->stock_qty;
            } else {
                $item['stock_qty'] = 0;
            }

            //TODO 附加服务待优化
            $item['option_service'] = isset($item['attribute_value_ids']) ? $this->getOptionService($item['attribute_value_ids']) : null;
            $item['specs_text'] = $item->model->specs_text;
        }

        $cartsList = array_values($carts->all());
        return $this->success($cartsList);
    }

    public function store()
    {
        $carts = request()->all();

        if (0 == count($carts)) {
            return $this->success();
        }

        foreach ($carts as $cart) {
            //设置属性值
            $attributes = isset($cart['attributes']) ? $cart['attributes'] : [];

            if (isset($cart['attributes']) and !isset($cart['attributes']['sku'])) {
                Cart::associate(Goods::class);
                $attributes['type'] = 'spu';
            } else {
                Cart::associate(Product::class);
                $attributes['type'] = 'sku';
            }

            if (!isset($cart['id'])) {
                continue;
            }

            //TODO 设置商品单价
            $option_services = explode(',', $cart['attributes']['attribute_value_ids']);
            $option_services_price = AttributeValue::query()->whereIn('id', $option_services)->sum('name');
            $cart['price'] += $option_services_price;

            //商标保障申请,商标加急申请
            if ($cart['attributes']['classification_ids']) {
                $classificationIds = explode(',', $cart['attributes']['classification_ids']);
                $num = count(array_unique($classificationIds));
                $cart['price'] += $num * Goods::MARKUP_PRICE_TOTAL;
                $attributes['service_price'] += $num * Goods::MARKUP_PRICE_SERVICE;
                $attributes['official_price'] += $num * Goods::MARKUP_PRICE_OFFICIAL;
            }

            $item = Cart::add($cart['id'], $cart['name'], $cart['qty'], $cart['price'], $attributes);

            if (!$item || !$item->model) {
                return $this->failed('商品数据错误');
            }

            if (2 == $item->model->is_del) {
                //已下架，需要删除购物车数据
                Cart::remove($item->rawId());

                return $this->failed('商品已下架');
            }

            if (($qty = $this->getIsInSaleQty($item, $item->qty)) > 0) {
                Cart::update($item->rawId(), ['qty' => $qty]);
            } else {
                Cart::remove($item->rawId());

                return $this->failed('商品库存不足,请重新选择');
            }

            Cart::update($item->rawId(), [
                'status' => 'online',
                'channel' => 'normal',
                'service_price_total' => sprintf('%.2f', $item->service_price * $item->qty),
                'official_price_total' => sprintf('%.2f', $item->official_price * $item->qty),
            ]);
        }

        $cartsList = array_values(Cart::all()->all());
        return $this->success($cartsList);
    }

    public function update($id)
    {
        $item = Cart::get($id);

        if (!$item) {
            return $this->failed('购物车数据不存在');
        }

        $attributes = request('attributes');

        if ($attributes['qty'] <= $item->model->stock_qty) {
            $item = Cart::update($id, $attributes);

            return $this->success($item);
        }

        return $this->failed('库存不够');
    }

    public function delete($id)
    {
        return $this->success(Cart::remove($id));
    }

    public function clear()
    {
        Cart::destroy();

        return $this->success(Cart::all());
    }

    public function count()
    {
        return $this->success(Cart::count());
    }

    public function getIsInSaleQty($item, $qty)
    {
        if ($qty <= 0) {
            return 0;
        }
        if ($item->model->getIsInSale($qty)) {
            return $qty;
        }

        return $this->getIsInSaleQty($item, $qty - 1);
    }

    /**
     * 获取附加服务
     *
     * @param $ids
     * @return \Illuminate\Support\Collection
     */
    private function getOptionService($ids)
    {
        if (!$ids) {
            return null;
        }

        $optionServiceIds = explode(',', $ids);
        $optionService = [];
        foreach ($optionServiceIds as $id) {
            $attributeValue = AttributeValue::query()->find($id);
            $attribute = $attributeValue->attribute;
            $optionService[] = [
                'attribute_id' => $attribute->id,
                'attribute_value_id' => $id,
                'attribute_value' => $attributeValue['name'],
                'name' => $attribute['name'],
            ];
        }

        return collect($optionService);
    }

}
