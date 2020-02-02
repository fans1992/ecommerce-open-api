<?php

/*
 * This file is part of ibrand/backend.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    /*
    * Laravel-admin name.
    */
    'name' => 'iBrand 管理后台',

    /*
     * Logo in admin panel header.
     */
    'logo' => '<b>iBrand</b> 管理后台',

    /*
     * Mini-logo in admin panel header.
     */
    'logo-mini' => 'B',

    /*
     * Laravel-admin html title.
     */
    'title' => 'iBrand 管理后台',

    'disks' => [
        'admin' => [
            'driver' => 'qiniu',
            'root' => storage_path('app/public/backend'),
            'url' => env('APP_URL') . '/storage/backend',
            'visibility' => 'public',
        ],
    ],

    'sms_login' => env('BACKEND_SMS_LOGIN', true),

    'technical_support' => '百一知识产权：https://www.foridom.com',

    'copyright' => '百一知识产权',

    'scenario' => env('BACKEND_SCENARIO', 'normal')
];
