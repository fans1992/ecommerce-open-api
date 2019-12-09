<?php

namespace GuoJiangClub\Component\NiceClassification;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Industry extends Model
{
    use NodeTrait;

    /**
     * @var array
     */

    protected $guarded = ['id'];

    /**
     * Address constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable( 'industry');

        parent::__construct($attributes);

    }

    public function recommendClassifications()
    {
        return $this->belongsToMany(NiceClassification::class, 'industry_recommend_classifications', 'industry_id', 'nice_classification_id')
            ->withPivot('nice_classification_parent_id', 'alias', 'sort')->withTimestamps();
    }
}
