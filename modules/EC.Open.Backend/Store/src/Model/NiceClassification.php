<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class NiceClassification extends Model implements Transformable
{
    use TransformableTrait;
    use NodeTrait;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        // 监听 NiceClassification 的创建事件，用于初始化 path 和 level 字段值
        static::creating(function (NiceClassification $niceClassification) {
            // 如果创建的是一个根类目
            if (!$niceClassification->parent_id) {
                // 将层级设为 1
                $niceClassification->level = 1;
                // 将 path 设为 /
                $niceClassification->path  = '/';
            } else {
                // 将层级设为父类目的层级 + 1
                $niceClassification->level = $niceClassification->parent->level + 1;
                // 将 path 值设为父类目的 path 追加父类目 ID 以及最后跟上一个 - 分隔符
                $niceClassification->path  = $niceClassification->parent->path.$niceClassification->parent_id.'/';
            }
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable( 'nice_classification');
    }

    public function parent()
    {
        return $this->belongsTo(NiceClassification::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(NiceClassification::class, 'parent_id', 'id');
    }

}
