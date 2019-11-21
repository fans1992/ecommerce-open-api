    <input type="hidden" name="id" value="{{$category->id}}">
    <div class="form-group">
        {!! Form::label('name','分类名称：', ['class' => 'col-lg-2 control-label']) !!}
        <div class="col-lg-9">
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => '请填写分类名称']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('description','分类描述：', ['class' => 'col-lg-2 control-label']) !!}
        <div class="col-lg-9">
            {!! Form::text('description', null, ['class' => 'form-control', 'placeholder' => '请填写分类描述']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('image','分类图片：', ['class' => 'col-lg-2 control-label']) !!}
        <div class="col-md-9">
            <input type="hidden" name="image" value="{{$category->image}}"/>
            <img class="banner-image" src="{{$category->image}}">
            <div id="filePicker">选择图片</div>

        </div>
    </div>

    <div class="form-group">
        {!! Form::label('name','上级分类：', ['class' => 'col-lg-2 control-label']) !!}
        <div class="col-lg-9">
            <div class="col-lg-9" style="padding-left: 0">
                <select class="form-control m-b " name="parent_id">
                    <option value="0">-无-</option>
                    @foreach($categories as $key => $val)
                        @if($category->id != $val->id AND !str_contains($val->dep,$category->path))
                            <?php $select = $category->parent_id == $val->id ? 'selected' : ''; ?>
                            <option {{$select}} value="{{$val->id}}">{{ $val->html }} @if($val->parent_id !=0 )
                                    ﹂@endif {{ $val->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <label class="col-lg-3">顶级分类请选择无</label>
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


