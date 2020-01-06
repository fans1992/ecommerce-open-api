<?php

namespace GuoJiangClub\Component\Order\Models;

use GuoJiangClub\Component\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class BrandApplicant extends Model
{

    protected $guarded = ['id'];

    const BRAND_APPLICANT_ENTERPRISE = 'enterprise';
    const BRAND_APPLICANT_INDIVIDUAL = 'individual';

    public static $brandApplicantMap = [
        self::BRAND_APPLICANT_ENTERPRISE => '企业',
        self::BRAND_APPLICANT_INDIVIDUAL => '个体工商户',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setTable('user_brand_applicants');
        parent::__construct($attributes);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //创建地址访问器
    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }

}
