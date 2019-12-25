<?php

/*
 * This file is part of ibrand/user.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\User\Models;

use GuoJiangClub\Component\NiceClassification\Models\UserClassification;
use GuoJiangClub\Component\Order\Models\BrandApplicant;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User.
 */
class User extends Authenticatable
{
    use  Notifiable;

    /**
     * User Status.
     */
    const STATUS_FORBIDDEN = 2;
    const STATUS_ENABLED = 1;

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Address constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'user');
    }

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->nick_name) {
                // 调用 findAvailableNo 生成昵称
                $model->nick_name = static::getAvailableNickname();
            }
        });
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        if (\Hash::needsRehash($value)) {
            $value = bcrypt($value);
        }

        return $this->attributes['password'] = $value;
    }

    /**
     * 支持手机号和邮箱登录
     * @param string $username
     * @return mixed
     */
    public function findForPassport($username)
    {
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['mobile'] = $username;

        return self::where($credentials)->first();
    }

    /**
     * 生成昵称
     * @return string
     * @throws \Exception
     */
    public static function getAvailableNickname()
    {
        // 随机生成 6 位的数字
        return  '用户' . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }


    /**
     * 用户提交的尼斯分类
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classifications()
    {
        return $this->hasMany(UserClassification::class, 'user_id', 'id');
    }

    /**
     * 商标申请主题列表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applicants()
    {
        return $this->hasMany(BrandApplicant::class, 'user_id', 'id');
    }



}
