<?php


namespace GuoJiangClub\Component\User\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFeedback extends Model
{
    protected $guarded = ['id'];

    /**
     * CustomerFeedback constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable( 'customer_feedback');
    }
}
