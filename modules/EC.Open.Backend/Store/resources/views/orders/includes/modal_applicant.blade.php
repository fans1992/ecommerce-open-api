@extends('store-backend::layouts.bootstrap_modal')

@section('modal_class')
    modal-lg
@stop
@section('title')
    修改申请人信息
@stop

@section('body')
    <div class="row">

        <form method="POST" action="{{route('admin.orders.postApplicant')}}" accept-charset="UTF-8"
              id="base-form" class="form-horizontal">

            <input type="hidden" name="order_id" value="{{$order->id}}">

            <div class="form-group">
                {!! Form::label('name','申请人类型：', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['applicant_subject']}}" name="applicant_subject" placeholder="" required>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('name','申请人名称：', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['applicant_name']}}" name="applicant_name" placeholder="" required>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('name','统一社会信用代码：', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['unified_social_credit_code']}}" name="unified_social_credit_code" placeholder="" required>
                </div>
            </div>


            <div class="form-group">
                {!! Form::label('name','身份证号：', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['id_card_no']}}" name="id_card_no" placeholder="" required>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('name','营业执照/身份证 所在地	：', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['address']}}" name="address" placeholder="" required>
                </div>
            </div>


            <div class="form-group">
                {!! Form::label('name','邮政编码：', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['postcode']}}" name="postcode" placeholder="" required>
                </div>
            </div>


            <div class="form-group">
                {!! Form::label('name','营业执照：', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['postcode']}}" name="postcode" placeholder="" required>
                </div>

            </div>

            <div class="form-group">
                {!! Form::label('avatar', '营业执照', ['class' => 'col-lg-2 control-label']) !!}
                <div class="col-md-9">
                    <div class="pull-left" id="userAvatar">
                        <img src="{{$order->applicant_data['business_license_picture']}}"
                             style="
                                         margin-right: 23px;
                                         width: 100px;
                                         height: 100px;
                                         border-radius: 50px;">
                        {!! Form::hidden('avatar', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="clearfix" style="padding-top: 22px;">
                        <div id="filePicker">添加图片</div>
                        <p style="color: #b6b3b3">温馨提示：图片尺寸建议为：图片小于4M</p>
                    </div>
                </div>
            </div><!--form control-->



            {{--<div class="form-group">--}}
                {{--{!! Form::label('name','地址：', ['class' => 'col-md-2 control-label']) !!}--}}
                {{--<div class="col-md-9" id="edit-address">--}}
                    {{--<div class="col-sm-4">--}}
                        {{--<select class="form-control" name="province"></select><!-- 省 -->--}}
                    {{--</div>--}}

                    {{--<div class="col-sm-4">--}}
                        {{--<select class="form-control" name="city"></select><!-- 市 -->--}}
                    {{--</div>--}}

                    {{--<div class="col-sm-4">--}}
                        {{--<select class="form-control" name="district"></select><!-- 区 -->--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        </form>
    </div>
@stop

{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/spin.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/ladda.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/ladda.jquery.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/loader/jquery.loader.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/distpicker.js') !!}

@section('footer')
    <button type="button" class="btn btn-link" data-dismiss="modal">取消</button>

    <button type="submit" class="btn btn-primary" data-style="slide-right" data-toggle="form-submit"
            data-target="#base-form">保存
    </button>

    <script>
        {{--$(function () {--}}
            {{--$('#edit-address').distpicker({--}}
                {{--province: '{{$address[0]}}',--}}
                {{--city: '{{$address[1]}}',--}}
                {{--district: '{{$address[2]}}'--}}
            {{--});--}}
        {{--});--}}


        $(document).ready(function () {
            $('#base-form').ajaxForm({
                success: function (result) {
                    if (result.status) {
                        swal({
                            title: "保存成功！",
                            text: "",
                            type: "success"
                        }, function () {
                            location.reload();
                        });
                    } else {
                        swal("保存失败!", result.message, "error")
                    }
                }
            });
        });
    </script>
@stop






