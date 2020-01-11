<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        服务协议书
    </title>
</head>
<body>
<div class="section">
    <h5>
        服务协议书
    </h5>
    <div class="tableCon">
        <p class="num">协议编号：{{$agreement->agreement_no}}</p>
        <p class="titOne">
            当事人信息（必填）
        </p>
        <div class="infoCon">
            <div class="one">
                <div class=" name">
                    <p>甲方：<span>{{$agreement->order->applicant_data['applicant_name'] ?: $agreement->order->accept_name}}</span></p>
                </div>
                <div class="name concat">
                    <p class="phone">联系人：<span>{{$agreement->order->accept_name}}</span></p>
                    <p>电话：<span>{{$agreement->order->mobile}}</span></p>
                </div>
                <div class="name email">
                    <p>邮箱：<span>{{$agreement->order->email}}</span></p>
                </div>
                <div class=" name address">
                    <p>联系地址：<span>{{$agreement->order->address}}</span></p>
                </div>
            </div>
            <div class="one">
                <div class=" name">
                    <p>甲方：<span>上海百一知识产权代理有限公司</span></p>
                </div>
                <div class=" name concat">
                    <p class="phone">联系人：<span>百一</span></p>
                    <p>电话：<span> 021-64878081</span></p>
                </div>
                <div class=" name email">
                    <p>邮箱：<span>foridom@foridom.com</span></p>
                </div>
                <div class=" name address">
                    <p>联系地址：<span>上海徐汇区桂平路410号B区1楼</span></p>
                </div>
            </div>
        </div>
        <p class="titOne">
            服务项目信息
        </p>
        <table>
            <thead>
            <tr>
                <th class="num">序号</th>
                <th class="type">业务类型</th>
                <th class="name">业务名称</th>
                <th class="appType">申请类别</th>
                <th class="mark">备注</th>
                <th class="price">单价</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($agreement))
                @foreach($agreement->service_items as $key => $item)
                    <tr>
                        <td>
                            {{$key+1}}
                        </td>
                        <td>
                            {{$item['item_name']}}
                        </td>
                        <td>
                            {{$item['bussiness_name']}}
                        </td>
                        <td>
                            {{$item['selected_classification']}}
                        </td>
                        <td>
                            {{$item['remark']}}
                        </td>
                        <td>
                            {{$item['total']}}元
                        </td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <td colspan="5" class="total">
                    合计
                </td>
                <td>{{sprintf("%.2f",$agreement->order->total / 100)}}元</td>
            </tr>
            </tbody>
        </table>
        <p class="titOne">
            服务条款
        </p>
        <div class="clause">
            <p>
                鉴于甲方有商标/专利/版权等知识产权事务委托乙方代为办理，双方友好协商达成如下协议:
            </p>
        </div>
    </div>
</div>
</body>
</html>
<style type="text/css">
    .flex {
        display: flex;
        align-items: center;
    }

    * {
        margin: 0;
        padding: 0;
        font-weight: normal;
    }

    body,
    html {
        font-family: "Helvetica Neue", Helvetica, "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", "微软雅黑", Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        width: 100%;
        min-width: 1200px;
        min-height: 100%;
        height: 100%;
    }

    .section {
        width: 1000px;
        margin: auto;
    }

    .section h5 {
        text-align: center;
        color: #222222;
        font-size: 24px;
        line-height: 18px;
        padding: 30px 0 22px 0;
    }

    .section .tableCon {
        margin-left: 50px;
        width: 890px;
    }

    .section .tableCon .num {
        color: #222222;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .section .tableCon .titOne {
        line-height: 40px;
        height: 40px;
        background: #2d3e50;
        font-size: 14px;
        color: #fff;
        padding-left: 10px;
    }

    .section .tableCon .infoCon {
        flex-direction: row;
        overflow: hidden;
    }

    .section .tableCon .infoCon .one {
        box-sizing: border-box;
        float: left;
        width: 50%;
        border: 1px solid #E6EBF1;
        border-top: none;
        border-bottom: none;
        flex-direction: column;
    }

    .section .tableCon .infoCon .one:first-child {
        border-right: none;
    }

    .section .tableCon .infoCon .one .name {
        border-bottom: 1px solid #E6EBF1;
        width: 100%;
        height: 38px;
    }

    .section .tableCon .infoCon .one .name p {
        font-size: 14px;
        color: #000000;
        height: 100%;
        line-height: 38px;
        padding-left: 10px;
    }

    .section .tableCon .infoCon .one .name p span {
        color: #666666;
        font-size: 14px;
        margin-left: 0;
    }

    .section .tableCon .infoCon .one .name.concat p {
        float: left;
    }

    .section .tableCon .infoCon .one .name.concat .phone {
        width: 50%;
        box-sizing: border-box;
        border-right: 1px solid #E6EBF1;
    }

    .section .tableCon table {
        border-collapse: collapse;
        border: none;
    }

    .section .tableCon table th {
        font-weight: normal;
        border: 1px solid #E6EBF1;
        height: 40px;
        line-height: 20px;
        padding: 0;
        color: #909399;
        font-size: 14px;
    }

    .section .tableCon table th.num {
        color: #909399;
        width: 80px;
    }

    .section .tableCon table th.type {
        width: 120px;
    }

    .section .tableCon table th.name {
        width: 120px;
    }

    .section .tableCon table th.appType {
        width: 80px;
    }

    .section .tableCon table th.mark {
        width: 370px;
    }

    .section .tableCon table th.price {
        width: 120px;
    }

    .section .tableCon table td {
        font-size: 14px;
        font-weight: normal;
        border: solid #E6EBF1 1px;
        text-align: center;
        line-height: 14px;
        padding: 12px 0;
    }

    .section .tableCon table td.total {
        text-align: left;
        padding-left: 10px;
    }

    .section .tableCon .clause {
        border: 1px solid #e6ebf1;
        padding: 10px;
    }

    .section .tableCon .clause p {
        color: #222222;
        font-size: 14px;
        line-height: 20px;
    }

</style>