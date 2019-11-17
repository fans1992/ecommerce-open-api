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
        // 校验输入参数
        $data  = app('alipay')->verify();
        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        // 所有交易状态：https://docs.open.alipay.com/59/103672
        if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::where('no', $data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if (!$order) {
            return 'fail';
        }
        // 如果这笔订单的状态已经是已支付
        if ($order->paid_at) {
            // 返回数据给支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
        ]);

        return app('alipay')->success();
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

            $charge = \GuoJiangClub\Component\Pay\Models\Charge::where('order_no', $order_no)->orderBy('created_at', 'desc')->first();
            $charge->transaction_no = '';
            $charge->time_paid = Carbon::now();
            $charge->paid = 1;
            $charge->channel = 'test';
            $charge->amount = $order->total;
            $charge->save();

            $order = PayNotify::success($charge->type, $charge);

        } else {
            //同步查询微信订单状态，防止异步通信失败导致订单状态更新失败

            $charge = Charge::find(request('charge_id'));

            $order = PayNotify::success($charge->type, $charge);


            /*$payment = EasyWeChat::payment();
            $result = $payment->order->queryByOutTradeNumber($order_no);

            if ('FAIL' == $result['return_code']) {
                return $this->failed($result['return_msg']);
            }

            if ('FAIL' == $result['result_code']) {
                return $this->failed($result['err_code_des']);
            }

            if ('SUCCESS' != $result['trade_state']) {
                return $this->failed($result['trade_state_desc']);
            }

            $charge['metadata']['order_no'] = $result['out_trade_no'];
            $charge['amount'] = $result['total_fee'];
            $charge['transaction_no'] = $result['transaction_id'];
            $charge['time_paid'] = strtotime($result['time_end']);
            $charge['details'] = json_encode($result);
            $charge['channel'] = 'wx_lite';

            $order = $this->payment->paySuccess($charge);*/
        }

        if (Order::STATUS_PAY == $order->status) {
            return $this->success(['order' => $order, 'payment' => '微信支付']);
        }

        return $this->failed('支付失败');
    }
}
