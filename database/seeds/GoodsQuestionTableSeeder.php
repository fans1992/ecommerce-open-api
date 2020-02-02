<?php

use GuoJiangClub\Component\Product\Models\Goods;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class GoodsQuestionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('ibrand_goods_question')->truncate();

        Goods::query()->each(function ($good) {
                $good->questions()->createMany([
                    [
                        'sort' => 9,
                        'code' => Uuid::uuid4()->getHex(),
                        'question' => '注册商标多久能下来？',
                        'answer' => '正常且顺利的流程如下：递交次日（非工作日延后）出申请号；3-5个月左右受通，9-12个月左右初审公告或驳回；初审公告后3个月是异议期，这个期间无人异议，3个月后进入注册公告，注册公告2个月后拿到商标注册证书。',
                    ],
                    [
                        'sort' => 9,
                        'code' => Uuid::uuid4()->getHex(),
                        'question' => '商标注册证书有有效期吗？过期了怎么办？',
                        'answer' => '商标有效期为10年，自注册之日起到期满前12个月内进行续展，续展官费2000元/件，每续展一次为10年，如果想让商标持续有效，可以每10年续展一次；期满未续展的，宽展期为6个月，需要额外承担宽展官费500元/件。'
                    ],
                    [
                        'sort' => 9,
                        'code' => Uuid::uuid4()->getHex(),
                        'question' => '自己的商品注册商标有什么好处？',
                        'answer' => '1、商标具有独占性。使用商标的目的就是为了区别与他人的商品或服务，便于消费者识别。
                        2、注册商标所有人对其商标具有专用权、受到法律的保护，未经商标权所有人的许可，任何人不得擅自使用与该注册商标相同或相类似的商标，否则将承担相应的法律责任。
                        3、商标是一种无形资产。商标所有人通过商标的创意、设计、申请注册、广告宣传及使用，使商标具有了价值，也增加了商品的附加值，使商标在有偿转让时价格更高。
                        4、商标是商品信息的载体，是参与市场竞争的工具。生产经营者的竞争就是商品或服务质量与信誉的竞争，其表现形式就是商标知名度的竞争，商标的知名度越高，其商品或服务的竞争力就越强。',
                    ]
                ]);
            });
    }
}
