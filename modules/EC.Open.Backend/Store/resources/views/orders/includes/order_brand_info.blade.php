@foreach($order->items as $item)
    @if($item->item_info['specs_text'])
        <h4>商品项: {{$item->item_name . ' ---- ' . ($item->item_info['specs_text'])}}</h4>
    @else
        <h4>商品项: {{$item->item_name}}</h4>
    @endif

    <table class="table table-hover table-striped">
        @if($brandInfo = $item->brand_data)
            <tbody>
            <tr>
                <td>申请/注册号</td>
                <td>
                    @if(isset($brandInfo['application_no']) && $brandInfo['application_no'])
                        {{$brandInfo['application_no']}}
                    @else
                        <b style="color: #7e7e7e">暂无</b>
                    @endif
                </td>
            </tr>
            <tr>
                <td>商标名称</td>
                <td>{{$brandInfo['brand_name']}}</td>
            </tr>
            <tr>
                <td>商标图样</td>
                <td>
                    <div class="form-group">
                            <div class="pull-left" id="userAvatar">
                                <img src="{{$brandInfo['brand_image']?: '/assets/backend/images/default_head_ico.png'}}"
                                     style="
                                         margin-right: 23px;
                                         width: 100px;
                                         height: 100px;">
                            </div>
                    </div><!--form control-->
                </td>
            </tr>

            </tbody>
        @endif
    </table>

    {{--@if($order->pay_status==1)--}}
    <a data-toggle="modal" class="btn btn-primary"
       data-target="#modal" data-backdrop="static" data-keyboard="false"
       data-url="{{route('admin.orders.editBrandInfo',['id'=>$item->id])}}"
       href="javascript:;">{{$brandInfo ? '修改' : '添加'}}商标信息</a>
    {{--@endif--}}
    <hr/>
@endforeach

