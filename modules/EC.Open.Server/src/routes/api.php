<?php

/************************************************************* PC注册登录 ************************************************/
//手机号短信登录
$router->post('oauth/sms', 'AuthController@smsLogin')->name('api.oauth.sms');
//用户注册
$router->post('oauth/signup', 'AuthController@signup')->name('api.oauth.signup');
//用户账号密码登录
$router->post('oauth/login', 'AuthController@login')->name('api.oauth.login');
//用户找回密码
$router->post('oauth/password/reset', 'AuthController@resetPassword')->name('api.oauth.password.reset');

/************************************************************* 小程序 ****************************************************/
//小程序快捷登陆
$router->post('oauth/MiniProgramLogin', 'MiniProgramLoginController@login')->name('api.oauth.miniprogram.login');
//小程序手机号授权登录
$router->post('oauth/MiniProgramMobileLogin', 'MiniProgramLoginController@mobileLogin')->name('api.oauth.miniprogram.mobile.login');
//小程序获取openId
$router->get('oauth/miniprogram/openid', 'MiniProgramLoginController@getOpenIdByCode');

/*********************************************************** 公众号扫码登录 ************************************************/
//扫码关注公众号登录
$router->get('oauth/qrcode', 'WechatController@getWxPic');
//微信消息接入
$router->any('wechat', 'WechatController@serve');
//微信登录绑定手机号
$router->post('oauth/qrcode/check', 'WechatController@loginCheck');

/************************************************************ 商品详情 ****************************************************/
$router->get('store/list', 'GoodsController@index')->name('api.goods.list');
$router->get('store/detail/{id}', 'GoodsController@show')->name('api.goods.detail');
$router->get('store/detail/{id}/stock', 'GoodsController@getStock')->name('api.goods.detail.stock');
$router->get('store/detail/{id}/share/img', 'GoodsController@shareImg')->name('api.goods.detail.share.img');
$router->get('store/question/list', 'GoodsController@questionIndex')->name('api.goods.question.list');        //热门问答列表

/*********************************************************** 商标保障注册 ****************************************************/
$router->get('industries', 'NiceClassificationController@industryIndex')->name('api.industry.list');                                //行业列表
$router->get('industries/{industry}/classifications', 'NiceClassificationController@recommendationIndex')->name('api.classification.recommendation.list');  //行业推荐分类列表(保障申请)

/*********************************************************** 商标自助注册 ****************************************************/
$router->get('industries/tree', 'NiceClassificationController@industryTree')->name('api.industry.tree');                            //行业树


/************************************************************ 首页数据 ****************************************************/
$router->get('home', 'HomeController@index')->name('api.home.index');
$router->get('category', 'HomeController@category')->name('api.home.category');
$router->get('micro/page/{code}', 'MicroPageController@index')->name('api.micro.page.index');

/************************************************************ 尼斯分类 ****************************************************/
$router->get('classification', 'NiceClassificationController@index')->name('api.classification.index'); //尼斯分类列表


/************************************************************* 其他 **********************************************************/
//客户留言
$router->post('guest/feedbacks', 'CustomerServiceController@store');
//微信服务器回调
$router->post('wechat/notify', 'WechatPayNotifyController@notify')->name('api.pay.wechat.notify');
//支付宝服务器回调
$router->post('alipay/notify', 'PaymentController@alipayNotify')->name('api.pay.alipay.notify');

$router->post('shoppingCart/discount', 'DiscountController@shoppingCartDiscount')->name('api.shopping.cart.discount');

$router->group(config('ibrand.ec-open-api.routeAuthAttributes'), function ($router) {
    //手机登录扫码绑定微信
    $router->post('oauth/qrcode/binding', 'WechatController@wechatBind');

    /********************************************** 商标自助注册 ****************************************************/
    $router->post('application/brand/images', 'SelfApplicationController@createBrandImage')->name('api.application.brand.image');       //生成商标图样
    $router->post('application/brand/upload', 'SelfApplicationController@uploadBrandImage')->name('api.application.brand.upload');      //手动上传商标图样
    $router->get('industries/{industry}/classifications/tree', 'NiceClassificationController@recommendationTree')->name('api.classification.recommendation.tree'); //行业推荐分类树(自助申请)
    $router->get('classifications/search', 'NiceClassificationController@search')->name('api.classification.search.tree');              //尼斯分类关键词搜索
    $router->post('classifications/export', 'SelfApplicationController@getClassificationsExportData')->name('api.classification.getClassificationsExportData'); //尼斯分类导出
    $router->get('classifications/record', 'SelfApplicationController@userRecordIndex')->name('api.classifications.user.record');       //历史申请类别列表
    $router->post('credentials', 'UploadController@credentialsStore')->name('api.upload.credentials.store');                            //上传证件
    $router->get('credentialsInfo', 'SelfApplicationController@queryCredentialsInfo')->name('api.application.credentialsInfo');         //证件识别
    $router->post('application/brand/applicants', 'SelfApplicationController@storeBrandApplicants')->name('api.application.brand.applicantStore');   //提交申请人信息
    $router->post('application/brand/applicants/confirm', 'SelfApplicationController@confirmBrandApplicants')->name('api.application.brand.applicantConfirm');   //确认申请人信息
    $router->get('application/brand/applicants', 'SelfApplicationController@getBrandApplicantsList')->name('api.application.brand.applicantList');   //申请人列表


    /************************************************* 购物车 ****************************************************/
    $router->post('shopping/cart', 'ShoppingCartController@store')->name('api.shopping.cart.store');
    $router->get('shopping/cart', 'ShoppingCartController@index')->name('api.shopping.cart');
    $router->put('shopping/cart/{id}', 'ShoppingCartController@update')->name('api.shopping.cart.put');
    $router->delete('shopping/cart/{id}', 'ShoppingCartController@delete')->name('api.shopping.cart.delete');
    $router->post('shopping/cart/clear', 'ShoppingCartController@clear')->name('api.shopping.cart.clear');
    $router->get('shopping/cart/count', 'ShoppingCartController@count')->name('api.shopping.cart.count');

    /************************************************ 购物流程 ****************************************************/
    $router->post('shopping/order/checkout', 'ShoppingController@checkout')->name('api.shopping.order.checkout');    //购物车结算||直接下单
    $router->post('shopping/order/confirm', 'ShoppingController@confirm')->name('api.shopping.order.confirm');       //提交订单
    $router->post('shopping/order/charge', 'PaymentController@createCharge')->name('api.shopping.order.charge');     //创建支付请求
//    $router->post('shopping/order/charge', 'WechatPayController@createCharge')->name('api.shopping.order.charge');
    $router->post('shopping/order/paid', 'PaymentController@paidSuccess')->name('api.shopping.order.paid');          //验证支付状态
    $router->post('shopping/order/cancel', 'ShoppingController@cancel')->name('api.shopping.order.cancel');          //取消订单
    $router->post('shopping/order/received', 'ShoppingController@received')->name('api.shopping.order.received');    //确认收货
    $router->post('shopping/order/delete', 'ShoppingController@delete')->name('api.shopping.order.delete');          //删除订单
    $router->post('shopping/order/review', 'ShoppingController@review')->name('api.shopping.order.review');          //订单评价
    $router->post('shopping/order/delivery', 'ShoppingController@delivery')->name('api.order.delivery');             //发货


    /************************************************* 订单联系人 **************************************************/
    $router->get('contacts', 'ContactsController@index')->name('api.contact.list');
    $router->post('contacts', 'ContactsController@store')->name('api.contact.store');
    $router->patch('contacts/{id}', 'ContactsController@update')->name('api.contact.update');
    $router->get('contacts/{id}', 'ContactsController@show')->where('id', '[0-9]+')->name('api.contact.show');
    $router->delete('contacts/{id}', 'ContactsController@delete')->name('api.contact.delete');
    $router->get('contacts/default', 'ContactsController@default')->name('api.contact.default');


    /*************************************************** 我的收藏 **************************************************/
    $router->get('favorite', 'FavoriteController@index')->name('api.favorite');
    $router->post('favorite', 'FavoriteController@store')->name('api.favorite.store');
    $router->delete('favorite', 'FavoriteController@delete')->name('api.favorite.delete');
    $router->post('favorite/delFavs', 'FavoriteController@delFavs')->name('api.favorite.delFavs');
    $router->get('favorite/isfav', 'FavoriteController@getIsFav')->name('api.favorite.isFav');

    /**************************************************** 用户 ****************************************************/
    $router->get('me', 'UserController@me')->name('api.me');
    //刷新token
    $router->put('oauth/authorizations/current', 'AuthController@update')->name('api.oauth.update');
    //用户退出登录
    $router->delete('oauth/authorizations/current', 'AuthController@logout')->name('api.oauth.logout');

    $router->get('users/ucenter', 'UserController@ucenter')->name('api.user.ucenter');
    $router->post('users/update/info', 'UserController@updateInfo')->name('api.user.update.info');
    $router->post('users/update/password', 'UserController@updatePassword')->name('api.user.update.password');
    $router->post('users/update/mobile', 'UserController@updateMobile')->name('api.user.update.mobile');
    $router->post('users/upload/avatar', 'UserController@uploadAvatar')->name('api.user.upload.avatar');

    /************************************************* 订单及协议 **********************************************/
    $router->get('order/list', 'OrderController@getOrders')->name('api.order.list');
    $router->get('order/{order_no}', 'OrderController@getOrderDetails')->name('api.order.show');
    $router->get('order/{order_no}/agreement', 'OrderController@getAgreement')->name('api.order.agreement.show');       //查看协议
    $router->patch('order/{order_no}/agreement', 'OrderController@updateAgreement')->name('api.order.agreement.update');//修改协议
    $router->post('order/{order_no}/agreement/export', 'OrderController@exportAgreement')->name('api.order.agreement.export'); //下载协议
    $router->post('order/{order_no}/agreement/send', 'OrderController@sendAgreement')->name('api.order.agreement.send'); //发送邮箱



    $router->get('coupon', 'CouponController@index')->name('api.coupon.list');
    $router->get('coupon/{id}', 'CouponController@show')->name('api.coupon.show');

    /************************************************* 促销活动和优惠券 **********************************************/
    $router->post('discount', 'DiscountController@create')->name('api.discount.create');
    $router->post('coupon', 'CouponController@create')->name('api.coupon.create');
    $router->post('coupon/take', 'CouponController@take')->name('api.coupon.take');

    /************************************************** 其他 ****************************************************/
    $router->post('test', 'OrderController@test');

});
