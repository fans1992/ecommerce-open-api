@extends('store-backend::layouts.bootstrap_modal')

@section('modal_class')
    modal-lg
@stop
@section('title')
    编辑商标信息
@stop

@section('body')
    <div class="row">

        <form method="POST" action="{{route('admin.orders.postBrandInfo')}}" accept-charset="UTF-8"
              id="base-form" class="form-horizontal">

            <input type="hidden" name="order_item_id" value="{{$orderItem->id}}">

            <div class="form-group">
                {!! Form::label('name','申请/注册号：', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{isset($orderItem->brand_data['application_no']) ? $orderItem->brand_data['application_no'] : ''}}" name="application_no" placeholder="" required>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('name','商标名称：', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{$orderItem->brand_data['brand_name']}}" name="brand_name" placeholder="" required>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('name','商标图样：', ['class' => 'col-md-3 control-label']) !!}
                <div class="col-md-9">
                    <input type="hidden" name="brand_image"
                           value="{{$orderItem->brand_data['brand_image']  ?: ''}}">
                    <img class="brand_image"
                         src="{{$orderItem->brand_data['brand_image']  ?: ''}}" alt=""
                         style="max-width: 100px;">
                    <div id="brandImagePicker">选择图片</div>
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

@section('footer')
    <button type="button" class="btn btn-link" data-dismiss="modal">取消</button>

    <button type="submit" class="btn btn-primary" data-style="slide-right" data-toggle="form-submit"
            data-target="#base-form">保存
    </button>

    <script>

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
            pick: '#brandImagePicker',
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
            $('input[name="brand_image"]').val(img_url);
            $('.brand_image').attr('src', img_url);
        });
    })
</script>





