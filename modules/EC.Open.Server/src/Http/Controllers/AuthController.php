<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use Carbon\Carbon;
use GuoJiangClub\Component\User\Repository\UserBindRepository;
use GuoJiangClub\Component\User\Repository\UserRepository;
use GuoJiangClub\EC\Open\Core\Auth\User;
use GuoJiangClub\Component\User\UserService;
use iBrand\Sms\Facade as Sms;
use Illuminate\Http\Request;
use Validator;
use Zend\Diactoros\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\AuthorizationServer;
use Auth;

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

    public function smsLogin(Request $request)
    {
        $mobile = $request->input('mobile');
        $code = $request->input('code');

        if (!Sms::checkCode($mobile, $code)) {
            return $this->failed('验证码错误');
        }

        $is_new = false;

        if (!$user = $this->userRepository->getUserByCredentials(['mobile' => $mobile])) {
            $user = $this->userRepository->create(['mobile' => $mobile]);
            $is_new = true;
        }

        //微信登录绑定手机号
        if ($request->has('socialite_key')) {
            $open_id = decrypt($request->input('socialite_key'));
            $this->userBindRepository->bindToUser($open_id, $user->id);
        }

        if (User::STATUS_FORBIDDEN == $user->status) {
            return $this->failed('您的账号已被禁用，联系网站管理员或客服！');
        }

        //1. create user token.
        $tokenResult = $user->createToken($mobile);

        //2. bind user bind data to user.
//        $this->userService->bindPlatform($user->id, request('open_id'), config('wechat.mini_program.default.app_id'), 'miniprogram');

        return $this->success([
            'token_type' => 'Bearer',
            'access_token' => $tokenResult->accessToken,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'is_new_user' => $is_new,
        ]);
    }

    /**
     * 用户注册
     *
     * @return \Dingo\Api\Http\Response|mixed
     */
    public function signup(Request $request)
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
            'password' => $password,
        ]);

        $tokenResult = $user->createToken($mobile);
        $tokenResult->token->save();

        return $this->success([
            'token_type' => 'Bearer',
            'access_token' => $tokenResult->accessToken,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ], 201);

    }

    /**
     * 账号密码登录
     *
     * @param AuthorizationServer $server
     * @param ServerRequestInterface $serverRequest
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function login(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'mobile' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors());
        }

        $credentials = request(['mobile', 'password']);

        if (!Auth::attempt($credentials)) {
            return $this->failed('手机号或者密码不正确, 请重新输入', 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $tokenResult->token->save();

        return $this->success([
            'token_type' => 'Bearer',
            'access_token' => $tokenResult->accessToken,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]);
    }

    /**
     * 刷新token
     *
     * @param AuthorizationServer $server
     * @param ServerRequestInterface $serverRequest
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function update(AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response);
        } catch (OAuthServerException $e) {
            return $this->failed($e->getMessage());
        }
    }

    /**
     * 删除token(退出登录)
     *
     * @return mixed
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!empty($user)) {
            $user->token()->revoke();
            return $this->success();
        } else {
            return $this->failed('The token is invalid.');
        }
    }

    /**
     * 用户找回密码
     *
     * @return \Dingo\Api\Http\Response|mixed
     */
    public function resetPassword()
    {
        $validator = Validator::make(request()->all(), [
            'mobile' => 'required|regex:/^1[3456789]\d{9}$/',
            'code' => 'required|string',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors());
        }

        if (!Sms::checkCode(request('mobile'), request('code'))) {
            return $this->failed('验证码错误');
        }

        if (!$user = $this->userRepository->getUserByCredentials(['mobile' => request('mobile')])) {
            return $this->failed('该手机号暂未注册');
        }

        $user->update(['password' => request('new_password')]);

        return $this->success();
    }


}
