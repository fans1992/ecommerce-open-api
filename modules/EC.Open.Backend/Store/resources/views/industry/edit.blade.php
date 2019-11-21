    <div class="ibox float-e-margins">
        <div class="ibox-content" style="display: block;">

            {!! Form::model($industry,['route' => ['admin.industry.update',$industry->id]
            , 'class' => 'form-horizontal'
            , 'role' => 'form'
            , 'method' => 'post'
            ,'id'=>'Industry_form']) !!}

            @include('store-backend::industry.form')

            {!! Form::close() !!}
                    <!-- /.tab-content -->
        </div>
    </div>

    <script>
        $('#Industry_form').ajaxForm({
            success: function (result) {
                if(!result.status)
                {
                    swal("保存失败!", result.message, "error")
                }else{
                    swal({
                        title: "保存成功！",
                        text: "",
                        type: "success"
                    }, function() {
                        location = '{{route('admin.industry.index', ['id' => $industry->group_id])}}';
                    });
                }

            }
        });

    </script>