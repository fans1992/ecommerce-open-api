<?php

namespace GuoJiangClub\Component\NiceClassification\Models;

use GuoJiangClub\Component\User\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed recommendClassifications
 */
class UserClassification extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'content' => 'json',
    ];

    /**
     * UserClassification constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable( 'user_classifications');

        parent::__construct($attributes);

    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
