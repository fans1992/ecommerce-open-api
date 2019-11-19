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
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use iBrand\Component\Pay\Facades\Charge;
use iBrand\Component\Pay\Facades\PayNotify;
use GuoJiangClub\Component\Payment\Services\PaymentService;
use Yansongda\Pay\Pay;

class PaymentController extends Controller
{
    private $payment;
    private $orderRepository;

    public function __construct(PaymentService $paymentService, OrderRepository $orderRepository)
    {
        $this->payment = $paymentService;
        $this->orderRepository = $orderRepository;
    }

    /**
     * 支付宝网页支付
     *
     * @return \Dingo\Api\Http\Response|mixed
     * @throws \Exception
     */
    public function createCharge()
    {
        $user = request()->user();

        $order_no = request('order_no');

        if (!$order_no || !$order = $this->orderRepository->getOrderByNo($order_no)) {
            return $this->failed('订单不存在');
        }

        if ($user->cant('pay', $order)) {
            return $this->failed('无权操作此订单');
        }

        if (Order::STATUS_INVALID == $order->status) {
            return $this->failed('无法支付');
        }

        if (0 === $order->getNeedPayAmount()) {
            return $this->failed('无法支付，需支付金额为零');
        }

        //发起一次支付请求时需要创建一个新的 charge 对象，获取一个可用的支付凭据用于客户端向第三方渠道发起支付请求。
        $charge = Charge::create([
            'channel' => request('channel'),
            'order_no' => $order_no,
            'amount' => $order->getNeedPayAmount(),
            'client_ip' => \request()->getClientIp(),
            'subject' => $order->getSubject(),
            'body' => $order->getSubject(),
            'extra' => ['openid' => \request('openid')],
        ]);

        return $this->success(compact('charge'));
    }

    /**
     * 支付宝服务器回调
     *
     * @return string
     */
    public function alipayNotify()
    {
        $config = config('ibrand.pay.default.alipay.default');
//        dd($config);
        $pay = Pay::alipay($config);

        $data = $pay->verify();

        \Log::debug('Alipay notify', $data->all());

        if ($data['trade_status'] == "TRADE_SUCCESS" || $data['trade_status'] == "TRADE_FINISHED") {

            $charge = \iBrand\Component\Pay\Models\Charge::ofOutTradeNo($data['out_trade_no'])->first();

            if (!$charge) {
                return response('支付失败', 500);
            }

            $charge->transaction_meta = json_encode($data);
            $charge->transaction_no = $data['trade_no'];
            $charge->time_paid = Carbon::createFromTimestamp(strtotime($data['gmt_payment']));
            $charge->paid = 1;
            $charge->save();

            if ($charge->amount !== intval($data['total_amount'] * 100)) {
                return response('支付失败', 500);
            }

            PayNotify::success($charge->type, $charge);

            return $pay->success();
        }
        return response('alipay notify fail.', 500);


        $payment = EasyWeChat::payment();

        if ('SUCCESS' === $message['return_code']) { // return_code 表示通信状态，不代表支付状态
            // 用户是否支付成功
            if ('SUCCESS' === array_get($message, 'result_code')) {
                $charge['metadata']['order_no'] = $message['out_trade_no'];
                $charge['amount'] = $message['total_fee'];
                $charge['transaction_no'] = $message['transaction_id'];
                $charge['time_paid'] = strtotime($message['time_end']);
                $charge['details'] = json_encode($message);
                $charge['channel'] = 'wx_lite';

                $this->payment->success($charge);

                return true; // 返回处理完成

                // 用户支付失败
            } elseif ('FAIL' === array_get($message, 'result_code')) {
                return $fail('支付失败');
            }
        } else {
            return $fail('通信失败，请稍后再通知我');
        }

        return $fail('支付失败');
    }



    public function paidSuccess()
    {
        $user = request()->user();
        $order_no = request('order_no');

        if (!$order_no || !$order = $this->orderRepository->getOrderByNo($order_no)) {
            return $this->failed('订单不存在');
        }

        if ($user->cant('update', $order)) {
            return $this->failed('无权操作.');
        }

        //在pay_debug=true 状态下，可以调用此接口直接更改订单支付状态
        if (config('ibrand.app.pay_debug')) {

            $charge = \iBrand\Component\Pay\Models\Charge::query()->where('order_no', $order_no)->orderBy('created_at', 'desc')->first();
            $charge->transaction_no = '';
            $charge->time_paid = Carbon::now();
            $charge->paid = 1;
            $charge->channel = 'test';
            $charge->amount = $order->total;
            $charge->save();

            $order = PayNotify::success($charge->type, $charge);

        } else {
            //同步查询订单状态，防止异步通信失败导致订单状态更新失败

            $charge = \iBrand\Component\Pay\Models\Charge::query()->find(request('charge_id'));

            $order = PayNotify::success($charge->type, $charge);


//            $payment = EasyWeChat::payment();
//            $result = $payment->order->queryByOutTradeNumber($order_no);
//
//            if ('FAIL' == $result['return_code']) {
//                return $this->failed($result['return_msg']);
//            }
//
//            if ('FAIL' == $result['result_code']) {
//                return $this->failed($result['err_code_des']);
//            }
//
//            if ('SUCCESS' != $result['trade_state']) {
//                return $this->failed($result['trade_state_desc']);
//            }
//
//            $charge['metadata']['order_no'] = $result['out_trade_no'];
//            $charge['amount'] = $result['total_fee'];
//            $charge['transaction_no'] = $result['transaction_id'];
//            $charge['time_paid'] = strtotime($result['time_end']);
//            $charge['details'] = json_encode($result);
//            $charge['channel'] = 'wx_lite';
//
//            $order = $this->payment->paySuccess($charge);
        }

        if (Order::STATUS_PAY == $order->status) {
            return $this->success(['order' => $order, 'payment' => '微信支付']);
        }

        return $this->failed('支付失败');
    }
}
