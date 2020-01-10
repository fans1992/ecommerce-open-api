<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\EC\Open\Server\Http\Requests\OrderAgreementRequest;
use GuoJiangClub\EC\Open\Server\Transformers\OrderAgreementTransformer;
use GuoJiangClub\EC\Open\Server\Transformers\OrderTransformer;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * 订单列表
     *
     * @return \Dingo\Api\Http\Response
     */
    public function getOrders()
    {
//        $orderConditions['channel'] = \request('channel') ? \request('channel') : 'ec';

        if (request('order_no')) {
            $orderConditions['order_no'] = request('order_no');
        }

        if (request('status')) {
            $orderConditions['status'] = request('status');
        } else {
            $orderConditions['status'] = ['status', '<>', 0];
            $orderConditions['status2'] = ['status', '<>', 9];
        }

        $orderConditions['user_id'] = request()->user()->id;

        $itemConditions = [];

        $limit = request('limit') ?: 10;

        if ($criteria = request('criteria')) {
            $itemConditions['order_no'] = ['order_no', 'like', '%' . $criteria . '%'];
            $itemConditions['item_name'] = ['item_name', 'like', '%' . $criteria . '%'];
            $itemConditions['item_id'] = ['item_id', 'like', '%' . $criteria . '%'];

            $order = $this->orderRepository->getOrdersByCriteria($orderConditions, $itemConditions, $limit);
        } else {
            $order = $this->orderRepository->getOrdersByConditions($orderConditions, $itemConditions,
                $limit, ['items', 'shippings', 'adjustments', 'items.product', 'items.product.goods']);
        }

        $transformer = request('transformer') ?: 'list';

        return $this->response()->paginator($order, new OrderTransformer($transformer));
    }

    public function getOrderDetails($orderno)
    {
        $user = request()->user();

        $order = $this->orderRepository->getOrderByNo($orderno);

        if ($user->cant('update', $order)) {
            return $this->failed('无权操作');
        }

        return $this->response()->item($order, new OrderTransformer());
    }

    /**
     * 修改协议
     *
     * @param $orderNo
     * @param OrderAgreementRequest $request
     * @return \Dingo\Api\Http\Response|mixed
     */
    public function updateAgreement($orderNo, OrderAgreementRequest $request)
    {
        if (!$orderNo || !$order = $this->orderRepository->getOrderByNo($orderNo)) {
            return $this->failed('订单不存在');
        }

        $user = $request->user();
        if ($user->cant('update', $order)) {
            return $this->failed('无权操作');
        }

        $input = $request->all();
        $contact = $input['order_contact'];
        $order->update([
            'accept_name' => $contact['accept_name'],
            'mobile' => $contact['mobile'],
            'email' => $contact['email'],
            'address' => $contact['address'],
        ]);

        if ($request->has('invoice_title')) {
            $invoice = $input['invoice_title'];
            $order->agreement()->update([
                'invoice_type' => $invoice['invoice_type'],
                'tax_no' => $invoice['tax_no'],
                'opening_bank' => $invoice['opening_bank'],
            ]);
        }

        return $this->success();
    }

    /**
     * 查看协议
     *
     * @param $orderNo
     * @param OrderAgreementRequest $request
     * @return \Dingo\Api\Http\Response|mixed
     */
    public function getAgreement($orderNo, OrderAgreementRequest $request)
    {
        if (!$orderNo || !$order = $this->orderRepository->getOrderByNo($orderNo)) {
            return $this->failed('订单不存在');
        }

        $user = $request->user();
        if ($user->cant('update', $order)) {
            return $this->failed('无权操作');
        }

        if (!$agreement = $order->agreement) {
            return $this->failed('未找到相关协议');
        }

        $agreement = $this->generateAgreementItems($order, $agreement);

        return $this->response()->item($agreement, new OrderAgreementTransformer());
    }

    public function exportAgreement($orderNo)
    {
        if (!$orderNo || !$order = $this->orderRepository->getOrderByNo($orderNo)) {
            return $this->failed('订单不存在');
        }

        $user = request()->user();
        if ($user->cant('update', $order)) {
            return $this->failed('无权操作');
        }

        if (!$agreement = $order->agreement) {
            return $this->failed('未找到相关协议');
        }

        $agreement = $this->generateAgreementItems($order, $agreement);
        $pdf = \PDF::loadView('server::order.agreement', compact('agreement'))->setPaper('a4')->setOrientation('landscape')->setOption('margin-bottom', 0);

//        return $pdf->download('agreement.pdf');
//        //获取扩展名，上传OSS
        $path = 'order/agreement/' . date('Ymd') . '/' . generaterandomstring() . '.pdf';
        $url = upload_image($path, $pdf->output());

        return $this->success(['url' => $url]);

    }

    /**
     * 生成协议条目
     *
     * @param $order
     * @param $agreement
     * @return mixed
     */
    public function generateAgreementItems($order, $agreement)
    {
        if ($order->type == Order::TYPE_DEFAULT) {
            $service_items = [];
            $order->items->each(function ($item) use(&$service_items) {
                $service_items[] = [
                    'item_name' => $item->item_name,
                    'bussiness_name' => '',
                    'selected_classification' => '/',
                    'remark' => '/',
                    'total' => sprintf("%.2f",$item->total / 100),
                ];
//                $bussiness_name = '';
//                $item->selected_classification = '/';
//                $item->remark = '/';
//                $item->total /= 100;
            });

            if ($adjustments_total = $order->adjustments_total) {
                $service_items[] = [
                    'item_name' => '优惠抵扣',
                    'bussiness_name' => '/',
                    'selected_classification' => '/',
                    'remark' => '/',
                    'total' => sprintf("%.2f",$adjustments_total / 100),
                ];
            }

            $agreement->service_items = $service_items;
        }

        return $agreement;
    }
}
