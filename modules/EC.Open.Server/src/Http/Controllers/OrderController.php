<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use App\Mail\OrderAgreement;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Models\OrderItem;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\EC\Open\Server\Http\Requests\OrderAgreementRequest;
use GuoJiangClub\EC\Open\Server\Transformers\OrderAgreementTransformer;
use GuoJiangClub\EC\Open\Server\Transformers\OrderTransformer;
use Mail;

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

    /**
     * 下载协议
     *
     * @param $orderNo
     * @return \Dingo\Api\Http\Response|mixed
     */
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
     * 邮箱发送协议
     *
     * @param $orderNo
     * @return \Dingo\Api\Http\Response|mixed
     */
    public function sendAgreement($orderNo)
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

        Mail::to(request('send_email'))->send(new OrderAgreement($pdf->output()));

        return $this->success();
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
        switch ($order->type) {
            case Order::TYPE_DEFAULT:
                $service_items = [];
                $order->items->each(function ($item) use (&$service_items) {
                    $service_items[] = [
                        'item_name' => $item->item_name,
                        'bussiness_name' => '',
                        'selected_classification' => '/',
                        'remark' => '/',
                        'total' => sprintf("%.2f", $item->total / 100),
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
                        'total' => sprintf("%.2f", $adjustments_total / 100),
                    ];
                }

                break;
            case Order::TYPE_SELF_APPLICATION:
                $orderItem = $order->items->first();
                $selfApplyClassifications = $orderItem->item_meta['self_apply_classifications'];

                $service_items = [];

                foreach ($selfApplyClassifications['selected_classifications'] as $classification) {
                    $remark = '';
                    $i = 0;

                    foreach ($classification['children']['data'] as $group) {
                        foreach ($group['children']['data'] as $key => $product) {
                            $i += 1;
                            $remark .= $key+1 . '.' . $product['classification_name'] . ' ';
                        }
                    }

                    //大类下超过10个叠加附加费用
                    $classificationPrice = $i <= 10 ? 300 : 300 + ($i - 10) * 30;

                    $service_items[] = [
                        'item_name' => $orderItem->item_name,
                        'bussiness_name' => $selfApplyClassifications['brand_name'] ?: '/',
                        'selected_classification' => $classification['classification_code'],
                        'remark' => $remark,
                        'total' => sprintf("%.2f", $classificationPrice),
                    ];
                }

                if ($adjustments_total = $order->adjustments_total) {
                    $service_items[] = [
                        'item_name' => '发票税费',
                        'bussiness_name' => '/',
                        'selected_classification' => '/',
                        'remark' => '/',
                        'total' => sprintf("%.2f", $adjustments_total / 100),
                    ];
                }

                break;
            default:
                return $this->failed('illegal params');
        }

        $agreement->service_items = $service_items;

        return $agreement;
    }

    public function test()
    {
        $data = [
            'brand_data' =>[
                'application_no' => '23231',
                'brand_name' => '测试商标',
                'brand_image' => 'http://aliyuncdn.foridom.com/brand/create/20200115/oMWZYiUyac.png',
                'registration_category'=>['01', '10', '45'],
            ],
            'company_progress' => [
                [
                    'date' => '2020-01-06',
                    'progress' => '待处理',
                ],
                [
                    'date' => '2020-01-06',
                    'progress' => '已处理',
                ],
            ],
            'applicant_data' => [
                'applicant_subject' => 'enterprise',
                'applicant_name' => '百一知识产权有限公司',
                'unified_social_credit_code' => '422323223232323',
                'id_card_no'=> '4223232423111323',
                'province' => '上海',
                'city' => '浦东新区',
                'district' => '三林镇',
                'address' => '上南路100号',
                'postcode' => '021000',
                'id_card_picture' => 'http://aliyuncdn.foridom.com/brand/business_license/20200108/0fDy5MCA0F.jpg',
                'business_license_picture' => 'http://aliyuncdn.foridom.com/brand/business_license/20200108/glnczQZpBE.jpeg',
                'attorney_picture' => 'http://aliyuncdn.foridom.com/brand/business_license/20200108/7KzQNvHoge.jpg',
            ],
            'official_progress' => [
                [
                    'date' => '2020-01-06',
                    'progress' => '文件整理中',
                ],
                [
                    'date' => '2020-01-06',
                    'progress' => '文件撰写中',
                ],

                [
                    'date' => '2020-01-06',
                    'progress' => '材料已提交',
                ],
            ],
        ];
        OrderItem::find(request('order_item_id'))->update($data);
    }
}
