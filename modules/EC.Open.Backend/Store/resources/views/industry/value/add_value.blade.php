@extends('store-backend::layouts.bootstrap_modal')

@section('modal_class')
    modal-lg
@stop
@section('title')
    添加行业推荐类别
@stop

@section('after-styles-end')
    {!! Html::style(env("APP_URL").'/assets/backend/libs/ladda/ladda-themeless.min.css') !!}
@stop


@section('body')
    <div class="row">
        {!! Form::open( [ 'route' => ['admin.industry.classification.store'], 'method' => 'POST', 'id' => 'spec-value-form','class'=>'form-horizontal'] ) !!}

        <input type="hidden" name="industry_id" value="{{$industry_id}}">
        <div class="col-md-50 clearfix">
            <div class="form-group col-md-50=25">
                <table class='border_table table table-bordered'>
                    <thead>
                    <tr>
                        <th>商标类别</th>
                        <th>类别别名</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody id='spec_box'>
                    <tr class="td_c">
                        {{--<td><input type="text" class="form-control" name="add_value[0][name]"></td>--}}
                        <td>
                            <select class="form-control type-s"  name="add_value[0][nice_classification_id]" >
                                <option value="">请选择</option>
                                @foreach($classifications as $item)
                                    <option value="{{$item->id}}">{{$item->classification_code. '-' .$item->classification_name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" class="form-control" name="add_value[0][alias]"></td>
                        <td><a href="javascript:;" class="btn btn-xs btn-primary operatorPhy">
                                <i class="fa fa-trash" data-toggle="tooltip" data-placement="top"
                                   data-original-title="删除"></i></a>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>

            <div class="form-group col-md-25">
                {{--<button id="specAddButton" type="button" class="btn btn-w-m btn-primary">继续添加</button>--}}
                <label class="col-sm-2 control-label">所属分类：</label>
                <div class="col-sm-20" id="category-box">

                </div>
            </div>

            {{--<div class="form-group">--}}
                {{--<button id="specAddButton" type="button" class="btn btn-w-m btn-primary">继续添加</button>--}}
            {{--</div>--}}
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
            data-target="#spec-value-form">保存
    </button>

    @include('store-backend::industry.value.script')

@stop






