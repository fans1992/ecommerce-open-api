@extends('store-backend::layouts.bootstrap_modal')

@section('modal_class')
    modal-lg
@stop
@section('title')
    编辑别名
@stop

@section('after-styles-end')
    {!! Html::style(env("APP_URL").'/assets/backend/libs/ladda/ladda-themeless.min.css') !!}
@stop

@section('body')
    <div class="row">
        {!! Form::open( [ 'route' => ['admin.industry.classification.storeClassification'], 'method' => 'POST', 'id' => 'edit_spec_value_form','class'=>'form-horizontal'] ) !!}
        <input type="hidden" name="industry_id" value="{{$classification->pivot->industry_id}}">
        <input type="hidden" name="nice_classification_id" value="{{$classification->pivot->nice_classification_id}}">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('name','类别别名：', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-9">
                    <input type="text" value="{{$classification->pivot->alias}}" class="form-control" name="alias" placeholder=""
                           required>
                </div>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
@stop



@section('footer')

    {!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/spin.min.js') !!}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/ladda.min.js') !!}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/ladda.jquery.min.js') !!}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/loader/jquery.loader.min.js') !!}
    {!! Html::script(env("APP_URL").'/vendor/libs/jscolor.js') !!}

    <button type="button" class="btn btn-link" data-dismiss="modal">取消</button>

    <button type="submit" class="ladda-button btn btn-primary" data-style="slide-right" data-toggle="form-submit"
            data-target="#edit_spec_value_form">保存
    </button>


    <script>
        $(document).ready(function () {
             {{--@if($specValue->spec_id==2)--}}
            {{--var input = document.createElement('input');--}}
            {{--input.setAttribute('name', 'rgb');--}}
            {{--input.setAttribute('value', '{{$specValue->rgb}}');--}}
            {{--input.setAttribute('class', 'form-control');--}}
            {{--var picker = new jscolor(input);--}}
            {{--picker.fromString('{{$specValue->rgb}}');--}}
            {{--$('#color').append(input);--}}
            {{--@endif--}}

          $('#edit_spec_value_form').ajaxForm({
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
                        swal(result.message, '', 'error');
                    }

                }
            });
        });

    </script>
@stop






