    <input type="hidden" name="id" value="{{$industry->id}}">
    <div class="form-group">
        {!! Form::label('name','行业名称：', ['class' => 'col-lg-2 control-label']) !!}
        <div class="col-lg-9">
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => '请填写行业名称']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('description','行业描述：', ['class' => 'col-lg-2 control-label']) !!}
        <div class="col-lg-9">
            {!! Form::text('description', null, ['class' => 'form-control', 'placeholder' => '请填写行业描述']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('name','上级行业：', ['class' => 'col-lg-2 control-label']) !!}
        <div class="col-lg-9">
            <div class="col-lg-9" style="padding-left: 0">
                <select class="form-control m-b " name="parent_id">
                    <option value="0">-无-</option>
                    @foreach($industries as $key => $val)
                        @if($industry->id != $val->id AND !str_contains($val->dep,$industry->path))
                            <?php $select = $industry->parent_id == $val->id ? 'selected' : ''; ?>
                            <option {{$select}} value="{{$val->id}}">{{ $val->html }} @if($val->parent_id !=0 )
                                    ﹂@endif {{ $val->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <label class="col-lg-3">顶级行业请选择无</label>
        </div>
    </div>


    <div class="form-group">
        {!! Form::label('sort','排序：', ['class' => 'col-lg-2 control-label']) !!}
        <div class="col-lg-9">
            {!! Form::text('sort', 0, ['class' => 'form-control', 'placeholder' => '0']) !!}
        </div>
    </div>

    <div class="hr-line-dashed"></div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-8 controls">
            <button type="submit" class="btn btn-primary">保存</button>
        </div>
    </div>

    {!! Html::script(env("APP_URL").'/vendor/libs/jquery.form.min.js') !!}
    {!! Html::script(env("APP_URL").'/vendor/libs/webuploader-0.1.5/webuploader.js') !!}
    @include('store-backend::category.script')


