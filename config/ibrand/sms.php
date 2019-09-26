<?php

/*
 * This file is part of ibrand/laravel-sms.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'route' => [
        'prefix' => 'api/sms',
        'middleware' => ['api'],
    ],

    'easy_sms' => [
        'timeout' => 5.0,

        // 默认发送配置
        'default' => [
            // 网关调用策略，默认：顺序调用
            'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

            // 默认可用的发送网关
            'gateways' => [
                'yunpian',
                'errorlog',
            ],
        ],

        // 可用的网关配置
        'gateways' => [
            'errorlog' => [
                'file' => storage_path('logs/laravel-sms.log'),
            ],

            'yunpian' => [
                'api_key' => env('YUNPIAN_API_KEY'),
            ],

            'aliyun' => [
                'access_key_id' => 'xxxx',
                'access_key_secret' => 'xxxx',
                'sign_name' => '阿里云短信测试专用',
                'code_template_id' => 'SMS_802xxx',
            ],

            'alidayu' => [
                //...
            ],
        ],
    ],

    'code' => [
        'length' => 5,
        'validMinutes' => 5,
        'maxAttempts' => 0,
    ],

    'data' => [
        'product' => '',
    ],

    'dblog' => true,

    'content' => '【星视度】您正在使用星视度广告系统，短信验证码：%s ， 有效期为600秒，感谢您的使用！',

    'storage' => \iBrand\Sms\Storage\CacheStorage::class,
];
