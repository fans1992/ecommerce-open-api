<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iBrand\EC\Open\Server\Http\Controllers;

use iBrand\Component\Advert\Repository\AdvertItemRepository;
use iBrand\EC\Open\Core\Services\GoodsService;

class HomeController extends Controller
{
    private $advertItem;

    public function __construct(AdvertItemRepository $advertItemRepository)
    {
        $this->advertItem = $advertItemRepository;
    }

    public function index()
    {
        $carousels = $this->advertItem->getItemsByCode('home.carousel');
        $categories = $this->advertItem->getItemsByCode('home.categories');

        $goodsService = app(GoodsService::class);

        $boysGoods = $goodsService->getGoodsByCategoryId(1)->where('is_del', 0)->take(8);

        $boyCategory = ['name' => '商标业务', 'link' => '/pages/store/list/list?c_id=1', 'items' => array_values($boysGoods->toArray())];

        $brandGoods = $goodsService->getGoodsByCategoryId(1)->where('is_del', 0)->take(8);

        $brandCategory = ['name' => '商标业务', 'link' => '/pages/store/list/list?c_id=1', 'items' => array_values($brandGoods->toArray())];

        $copyrightGoods = $goodsService->getGoodsByCategoryId(2)->where('is_del', 0)->take(8);

        $copyrightCategory = ['name' => '版权业务', 'link' => '/pages/store/list/list?c_id=2', 'items' => array_values($copyrightGoods->toArray())];

//        $girlGoods = $goodsService->getGoodsByCategoryId(2)->where('is_del', 0)->take(8);
//
//        $girlCategory = ['name' => '商标工具', 'link' => '/pages/store/list/list?c_id=2', 'items' => array_values($girlGoods->toArray())];

        return $this->success(compact('carousels', 'categories', 'boyCategory', 'brandCategory', 'copyrightCategory'));
    }

    public function category()
    {
        $items = $this->advertItem->getItemsByCode('home.categories');

        return $this->success($items);
    }
}
