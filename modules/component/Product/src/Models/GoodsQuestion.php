<?php

namespace GuoJiangClub\Component\Product\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsQuestion extends Model
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'goods_question');
    }

    public function goods()
    {
        return $this->belongsTo('App\Entities\Goods', 'goods_id', 'id');
    }
}
