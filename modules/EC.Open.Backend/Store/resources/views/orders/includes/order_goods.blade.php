<table class="table table-hover table-striped">
    <tbody>

    <tr>
        <th>商品名称</th>
        <th>商品单价</th>
        <th>数量</th>
        <th>调整金额</th>
        <th>总价</th>
        <th>参数</th>
        <th>附加服务</th>
        <th>SKU</th>
        <th>发货状态</th>
    </tr>

    @foreach($order->items as $item)

        <tr>
            <td>
                <img width="50" height="50" src="{{$item->item_info['image']}}" alt="">&nbsp;&nbsp;&nbsp;
                &nbsp;
                {{$item->item_name}}
            </td>
            <td>{{$item->unit_price}}</td>
            <td>{{$item->quantity}}</td>
            <td>{{$item->adjustments_total > 0 ? '+ ' . $item->adjustments_total : $item->adjustments_total}}</td>
            <td>{{$item->total}}</td>

            <td>{{!empty($item->item_info['specs_text'])?$item->item_info['specs_text']:''}}</td>
            <td>
                @if($item->item_info['option_service'])
                    {{implode(' ', array_column($item->item_info['option_service'], 'name'))}}
                @endif
            </td>
            <td>
                {{$item->getModel() ? $item->getModel()->sku : ''}}
            </td>
            <td>
                @if($order->distribution_status==1)
                    已发货<br>
                    发货单号：{{$order->shipping->first()->tracking}}
                @else
                    未发货
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>