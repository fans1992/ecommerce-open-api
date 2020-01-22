<table class="table table-hover table-striped">
    <tbody>
    @if($applicant = $order->applicant_data)
        <tr>
            <td>申请人类型</td>
            <td>{{$applicant['applicant_subject'] === 'enterprise' ? '企业' : '个人'}}</td>
        </tr>
        <tr>
            <td>申请人名称</td>
            <td>{{$applicant['applicant_name']}}</td>
        </tr>
        <tr>
            <td>统一社会信用代码</td>
            <td>
                @if(!empty($applicant['unified_social_credit_code']))
                    {{$applicant['unified_social_credit_code']}}
                @else
                    <b style="color: #7e7e7e">暂无</b>
                @endif
            </td>
        </tr>
        <tr>
            <td>身份证号</td>
            <td>
                @if(!empty($applicant['id_card_no']))
                    {{$applicant['id_card_no']}}
                @else
                    <b style="color: #7e7e7e">暂无</b>
                @endif
            </td>
        </tr>
        <tr>
            <td>营业执照/身份证 所在地</td>
            <td>
                @if(!empty($applicant['province'] && $applicant['city'] && $applicant['district'] && $applicant['address']))
                    {{$applicant['province'].$applicant['city'].$applicant['district'].$applicant['address']}}
                @else
                    <b style="color: #7e7e7e">暂无</b>
                @endif
            </td>
        </tr>
        <tr>
            <td>邮政编码</td>
            <td>{{$applicant['postcode']}}</td>
        </tr>
        <tr>
            <td>营业执照</td>
            <td>
                @if(!empty($applicant['business_license_picture']))
                    <a href="{{$applicant['business_license_picture']}}" target="_blank">查看</a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="{{$applicant['business_license_picture']}}" target="_blank" download="w3logo">
                        {{--<img border="10"  :src='{{$applicant['business_license_picture']}}' :alt="imgName" />--}}
                        下载</a>
                @else
                    <b style="color: #7e7e7e">暂无</b>
                @endif
            </td>
        </tr>
        <tr>
            <td>身份证</td>
            <td>
                @if(!empty($applicant['id_card_picture']))
                    <a href="{{$applicant['id_card_picture']}}" target="_blank">查看</a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="{{$applicant['id_card_picture']}}" target="_blank" download="w3logo">
                        {{--<img border="10"  :src='{{$applicant['business_license_picture']}}' :alt="imgName" />--}}
                        下载</a>
                @else
                    <b style="color: #7e7e7e">暂无</b>
                @endif
            </td>
        </tr>
        <tr>
            <td>委托书</td>
            <td>
                @if(!empty($applicant['attorney_picture']))
                    <a href="{{$applicant['attorney_picture']}}" target="_blank">查看</a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="{{$applicant['attorney_picture']}}" target="_blank" download="w3logo">
                        {{--<img border="10"  :src='{{$applicant['business_license_picture']}}' :alt="imgName" />--}}
                        下载</a>
                @else
                    <b style="color: #7e7e7e">暂无</b>
                @endif
            </td>
        </tr>
    @endif
    {{--<tr>--}}
        {{--<td>订单</td>--}}
        {{--<td>{{$orderCount=count($user->hasManyOrders()->where('status','>',0)->get())}}笔--}}
            {{--@if($orderCount>0)--}}
                {{--<a href="{{route('admin.orders.index',['status'=>'all','user_id'=>$user->id])}}"--}}
                   {{--target="_blank">点击查看</a>--}}
            {{--@endif--}}
        {{--</td>--}}
    {{--</tr>--}}

    </tbody>
</table>