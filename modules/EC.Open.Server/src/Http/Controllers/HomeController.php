<?php
namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use GuoJiangClub\Component\Advert\Repositories\AdvertItemRepository;
use GuoJiangClub\Component\Category\Category;
use GuoJiangClub\Component\Category\Repository as CategoryRepository;
use GuoJiangClub\EC\Open\Core\Services\GoodsService;
use GuoJiangClub\Component\Advert\Models\MicroPage;
use GuoJiangClub\Component\Advert\Models\MicroPageAdvert;
use DB;

class HomeController extends Controller
{
    private $advertItem;

    protected $microPage;

    protected $microPageAdvert;

    protected $categoryRepository;

    public function __construct(
        AdvertItemRepository $advertItemRepository
        , CategoryRepository $categoryRepository
        , MicroPage $microPage
        , microPageAdvert $microPageAdvert
    )
    {
        $this->advertItem = $advertItemRepository;

        $this->microPage = $microPage;

        $this->microPageAdvert = $microPageAdvert;

        $this->categoryRepository = $categoryRepository;

    }

    public function index()
    {
        $carousels = $this->advertItem->getItemsByCode('home.carousel');
//        $categories = $this->advertItem->getItemsByCode('home.categories');

        $goodsService = app(GoodsService::class);
        $categories = $this->categoryRepository->getCategories()->where('status', Category::STATUS_OPEN);

        foreach ($categories as $category) {
            //获取分类下的商品
            if ($category->children->isNotEmpty()) {
                foreach ($category['children'] as $c) {
                    $c->items = $this->getProductItems($goodsService, $c->id);
                }
            } else {
                $category->items = $this->getProductItems($goodsService, $category->id);
            }
        }

        return $this->success(compact('carousels', 'categories'));
    }

    public function category()
    {

        $microPage = $this->microPage->where('page_type', 3)->first();

        if (!$microPage) return $this->success();

        $microPageAdverts = $this->microPageAdvert->where('micro_page_id', $microPage->id)
            ->with(['advert' => function ($query) {

                return $query = $query->where('status', 1);
            }])
            ->orderBy('sort')->get();

        if ($microPageAdverts->count()) {

            $i = 0;

            foreach ($microPageAdverts as $key => $item) {

                if ($item->advert_id > 0) {

                    if($item->advert->type=='micro_page_componet_category'){

                        $data['pages'][$i]['name'] = $item->advert->type;

                        $data['pages'][$i]['title'] = $item->advert->title;

                        $data['pages'][$i]['is_show_title'] = $item->advert->is_show_title;

                        $advertItem = $this->getAdvertItem($item->advert->code, []);

                        $data['pages'][$i]['value'] = array_values($advertItem);

                        $i++;
                    }

                }

            }

        }

        $data['micro_page'] = $microPage;

        return $this->success($data);


//        $items = $this->advertItem->getItemsByCode('home.categories');
//
//        return $this->success($items);
    }


    public function getAdvertItem($code, $associate_with)

    {
        $advertItem = $this->advertItem->getItemsByCode($code, $associate_with);

        if ($advertItem->count()) {

            $filtered = $advertItem->filter(function ($item)  {

                if (!$item->associate AND $item->associate_id) return [];

                switch ($item->associate_type) {

                    case 'category':

                        $prefix = config('ibrand.app.database.prefix', 'ibrand_');

                        $category_id = $item->associate_id;

                        $categoryGoodsIds = DB::table($prefix . 'goods_category')
                            ->where('category_id', $category_id)
                            ->select('goods_id')->distinct()->get()
                            ->pluck('goods_id')->toArray();

                        $goodsList = DB::table($prefix . 'goods')
                            ->whereIn('id', $categoryGoodsIds)
                            ->where('is_del', 0)
                            ->limit($item->meta['limit'])->get();

                        $item->goodsList = $goodsList;

                        return $item;

                        break;

                    default:

                        return $item;

                }

            });

            return $filtered->all();
        }

        return $advertItem;

    }

    /**
     * @param GoodsService $goodsService
     * @param int $category_id
     * @return array
     */
    protected function getProductItems($goodsService, $category_id)
    {
        return array_values($goodsService->getGoodsByCategoryId($category_id)->where('is_del', 0)->toArray());
    }

}
