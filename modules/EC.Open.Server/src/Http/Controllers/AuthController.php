<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iBrand\EC\Open\Server\Http\Controllers;

use iBrand\Component\User\Repository\UserBindRepository;
use iBrand\Component\User\Repository\UserRepository;
use iBrand\EC\Open\Core\Auth\User;
use iBrand\Component\User\UserService;
use iBrand\Sms\Facade as Sms;
use Illuminate\Support\Facades\Auth;
use Validator;
use Zend\Diactoros\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\AuthorizationServer;

class AuthController extends Controller
{

    protected $userRepository;
    protected $userBindRepository;
    protected $userService;

    public function __construct(UserRepository $userRepository, UserBindRepository $userBindRepository
        , UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->userBindRepository = $userBindRepository;
        $this->userService = $userService;
    }

    public function smsLogin()
    {
        $mobile = request('mobile');
        $code = request('code');

        if (!Sms::checkCode($mobile, $code)) {
            return $this->failed('验证码错误');
        }

        $is_new = false;

        if (!$user = $this->userRepository->getUserByCredentials(['mobile' => $mobile])) {
            $user = $this->userRepository->create(['mobile' => $mobile]);
            $is_new = true;
        }

        if (User::STATUS_FORBIDDEN == $user->status) {
            return $this->failed('您的账号已被禁用，联系网站管理员或客服！');
        }

        //1. create user token.
        $token = $user->createToken($mobile)->accessToken;

        //2. bind user bind data to user.
//        $this->userService->bindPlatform($user->id, request('open_id'), config('wechat.mini_program.default.app_id'), 'miniprogram');

        return $this->success(['token_type' => 'Bearer', 'access_token' => $token, 'is_new_user' => $is_new]);
    }

    /**
     * 用户注册
     * @return \Dingo\Api\Http\Response|mixed
     */
    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'mobile' => 'required|regex:/^1[3456789]\d{9}$/',
            'password' => 'required|string|min:6',
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors());
        }

        $mobile = request('mobile');
        $password = request('password');
        $code = request('code');

        if (!Sms::checkCode($mobile, $code)) {
            return $this->failed('验证码错误');
        }

        if ($user = $this->userRepository->getUserByCredentials(['mobile' => $mobile])) {
            return $this->failed('该手机号已经注册, 请直接登录');
        }

        $user = $this->userRepository->create([
            'mobile' => $mobile,
            'password' => bcrypt($password),
        ]);

        $token = $user->createToken($mobile)->accessToken;

        return $this->success([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'is_new_user' => true,
        ]);

    }

    public function store(AuthorizationRequest $originRequest, AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        $validator = Validator::make(request()->all(), [
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors());
        }
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response)->withStatus(201);
        } catch(OAuthServerException $e) {
            return $this->response->errorUnauthorized($e->getMessage());
        }
    }

}
