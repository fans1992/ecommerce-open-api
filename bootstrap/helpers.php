<?php

function ngrok_url($routeName, $parameters = [])
{
    // 开发环境，并且配置了 NGROK_URL
    if (app()->environment('local') && $url = config('app.ngrok_url')) {
        // route() 函数第三个参数代表是否绝对路径
        //todo
        //return $url . \Dingo\Api\Facade\API::route($routeName, $parameters, false);
        return $url . '/api/payment/alipay/notify';
    }

    return route($routeName, $parameters);
}