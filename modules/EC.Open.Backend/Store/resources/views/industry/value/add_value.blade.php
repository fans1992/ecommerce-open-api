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

<style>
    .row {
        padding-top: 20px;
    }

    .search {
        position: absolute;
        right: 10px;
        top: 0;
        display: flex;
    }

    button {
        margin-left: 10px;
        background-color: #fff;
        outline: none;
        border: none;
        background-color: #2bc0be;
        color: #fff;
        padding: 5px 15px;
    }
</style>
@section('body')
    <div class="row">
        <!-- 搜索框 -->
        <div class="search">
            <input type="text" placeholder="输入商品/服务名称" class="searchVal">
            <button>搜索</button>
        </div>

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
                        <td>
                            <select class="form-control type-s" name="top_nice_classification_id">
                                <option value="">请选择</option>
                                @foreach($classifications as $item)
                                    <option value="{{$item->id}}">{{'第' . $item->classification_code .'类'}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" class="form-control" name="alias"></td>
                        <td><a href="javascript:;" class="btn btn-xs btn-primary operatorPhy">
                                <i class="fa fa-trash" data-toggle="tooltip" data-placement="top"
                                   data-original-title="删除"></i></a>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>

            <div class="form-group col-md-25">
                <label class="control-label" style="text-align: left;padding-left: 40px;">所属分类：</label>
                <div class="col-sm-20" id="category-box">

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
            data-target="#spec-value-form">保存
    </button>

    <script>
        $(".search button").click(function () {
            let searchVal = $(".searchVal").val();
            // $.post("",{suggest:txt},function(result){

            // });

        })
    </script>
    @include('store-backend::industry.value.script')

@stop






