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
