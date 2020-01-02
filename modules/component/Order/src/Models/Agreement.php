<?php

namespace GuoJiangClub\Component\Order\Models;

use GuoJiangClub\Component\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Agreement extends Model
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'order_agreement');
        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->agreement_no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->agreement_no = static::getAvailableAgreementdNo();
                // 如果生成失败，则终止创建订单
                if (!$model->agreement_no) {
                    return false;
                }
            }
        });
    }

    /**
     * 生成协议号
     * @return string
     * @throws \Exception
     */
    public static function getAvailableAgreementdNo()
    {
        // 订单流水号前缀
        $prefix = 'SC' . date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 3 位的数字
            $no = $prefix.str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);
        }

        return $no;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
