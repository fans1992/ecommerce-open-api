<?php

namespace GuoJiangClub\Component\Order\Models;

use Illuminate\Database\Eloquent\Model;

class BrandApplicant extends Model
{

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable('brand_applicant');
        parent::__construct($attributes);
    }

    const BRAND_APPLICANT_ENTERPRISE = 'enterprise';
    const BRAND_APPLICANT_INDIVIDUAL = 'individual';

    public static $brandApplicantMap = [
        self::BRAND_APPLICANT_ENTERPRISE => '企业',
        self::BRAND_APPLICANT_INDIVIDUAL => '个体工商户',
    ];



}
