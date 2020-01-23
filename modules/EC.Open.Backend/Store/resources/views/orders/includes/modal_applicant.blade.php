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
                {!! Form::label('name','申请人类型：', ['class' => 'col-md-3 control-label']) !!}
                {{--<div class="col-md-9">--}}
                    {{--<input type="text" class="form-control" value="{{$order->applicant_data['applicant_subject']}}" name="applicant_subject" placeholder="" required>--}}
                {{--</div>--}}

                <div class="col-md-9">
                    <label class="control-label">
                        <input type="radio" value="enterprise"
                               name="applicant_subject" {{$order->applicant_data['applicant_subject'] === 'enterprise' ? 'checked': ''}}>
                        企业
                        &nbsp;&nbsp;
                        <input type="radio" value="individual"
                               name="applicant_subject" {{$order->applicant_data['applicant_subject'] === 'individual' ? 'checked': ''}}>
                        个人
                    </label>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('name','申请人名称：', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['applicant_name']}}" name="applicant_name" placeholder="" required>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('name','统一社会信用代码：', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['unified_social_credit_code']}}" name="unified_social_credit_code" placeholder="" required>
                </div>
            </div>


            <div class="form-group">
                {!! Form::label('name','身份证号：', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['id_card_no']}}" name="id_card_no" placeholder="" required>
                </div>
            </div>


            <div class="form-group">
                {!! Form::label('name','省市区：', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-9" id="edit-address">
                    <div class="col-sm-4">
                        <select class="form-control" name="province"></select><!-- 省 -->
                    </div>

                    <div class="col-sm-4">
                        <select class="form-control" name="city"></select><!-- 市 -->
                    </div>

                    <div class="col-sm-4">
                        <select class="form-control" name="district"></select><!-- 区 -->
                    </div>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('name','营业执照/身份证 详细地址	：', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['address']}}" name="address" placeholder="" required>
                </div>
            </div>


            <div class="form-group">
                {!! Form::label('name','邮政编码：', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$order->applicant_data['postcode']}}" name="postcode" placeholder="" required>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('name','营业执照：', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <input type="hidden" name="business_license_picture"
                           value="{{$order->applicant_data['business_license_picture']  ?: ''}}">
                    <img class="business_license_picture"
                         src="{{$order->applicant_data['business_license_picture']  ?: ''}}" alt=""
                         style="max-width: 100px;">
                    <div id="businessLicensePicker">选择图片</div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('name','身份证：', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <input type="hidden" name="id_card_picture"
                           value="{{$order->applicant_data['id_card_picture']  ?: ''}}">
                    <img class="id_card_picture"
                         src="{{$order->applicant_data['id_card_picture']  ?: ''}}" alt=""
                         style="max-width: 100px;">
                    <div id="idCardPicker">选择图片</div>
                    <div class="clearfix"></div>
                </div>
            </div>


            <div class="form-group">
                {!! Form::label('name','委托书：', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <input type="hidden" name="attorney_picture"
                           value="{{$order->applicant_data['attorney_picture']  ?: ''}}">
                    <img class="attorney_picture"
                         src="{{$order->applicant_data['attorney_picture']  ?: ''}}" alt=""
                         style="max-width: 100px;">
                    <div id="attorneyPicker">选择图片</div>
                    <div class="clearfix"></div>
                </div>
            </div>

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
        $(function () {
            $('#edit-address').distpicker({
                province: '{{$order->applicant_data['province']}}',
                city: '{{$order->applicant_data['city']}}',
                district: '{{$order->applicant_data['district']}}'
            });
        });


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

<script>

    $(function () {
        var color_html = $('#color-template').html();
        $('#add-color').click(function () {
            var num = $('.colorList').length;
            $('#colorBody').append(color_html.replace(/{NUM}/g, num));
        });

        //营业执照上传
        var uploader = WebUploader.create({
            auto: true,
            swf: '{{url(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
            server: '{{route('file.upload',['_token'=>csrf_token()])}}',
            pick: '#businessLicensePicker',
            fileVal: 'file',
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            }
        });

        // 文件上传成功，给item添加成功class, 用样式标记上传成功。
        uploader.on('uploadSuccess', function (file, response) {
            var img_url = response.url;
            $('input[name="business_license_picture"]').val(AppUrl + img_url);
            $('.business_license_picture').attr('src', img_url);
        });

        //身份证上传
        var idCardPicker = WebUploader.create({
            auto: true,
            swf: '{{url(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
            server: '{{route('file.upload',['_token'=>csrf_token()])}}',
            pick: '#idCardPicker',
            fileVal: 'file',
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            }
        });

        // 文件上传成功，给item添加成功class, 用样式标记上传成功。
        idCardPicker.on('uploadSuccess', function (file, response) {
            var img_url = response.url;
            $('input[name="id_card_picture"]').val(img_url);
            $('.id_card_picture').attr('src', img_url);
        });


        //委托书上传
        var attorneyPicker = WebUploader.create({
            auto: true,
            swf: '{{url(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
            server: '{{route('file.upload',['_token'=>csrf_token()])}}',
            pick: '#attorneyPicker',
            fileVal: 'file',
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            }
        });

        attorneyPicker.on('uploadSuccess', function (file, response) {
            var img_url = response.url;

            $('input[name="attorney_picture"]').val(img_url);
            $('.attorney_picture').attr('src', img_url);
        });

    })
</script>





