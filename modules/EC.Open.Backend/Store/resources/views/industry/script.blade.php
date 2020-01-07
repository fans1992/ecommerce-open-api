    <script>
        $('#base-form').ajaxForm({
            success: function (result) {
                $("input[name='id']").val(result.data);
                swal({
                    title: "保存成功！",
                    text: "",
                    type: "success"
                }, function() {
                    location = '{{route('admin.industry.index')}}';
                });


            }
        });

        $(function () {
            var uploader = WebUploader.create({
                // 选完文件后，是否自动上传。
                auto: true,
                swf: '{{url(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
                server: '{{route('upload.image',['_token'=>csrf_token()])}}',
                pick: '#filePicker',
                fileVal: 'upload_image',
                accept: {
                    title: 'Images',
                    extensions: 'gif,jpg,jpeg,bmp,png',
                    mimeTypes: 'image/*'
                }
            });
            // 文件上传成功，给item添加成功class, 用样式标记上传成功。
            uploader.on('uploadSuccess', function (file, response) {
                $('.banner-image').attr('src', response.url).show();
                $("input[name='banner_pic']").val(response.file);
                $("input[name='image']").val(response.url);

            });
        })
    </script>


    <script type="text/html" id="page-temp">
        <tr>
            <td>
                <input style="width: 45px" id="s{#id#}" value="{#pivot.sort#}"
                       class="form-control" type="text" size="2"
                       onblur="toRecommendSort( '{#id#}');">
            </td>
            <td>
                {#classification_code#} - {#classification_name#}
            </td>
            <td>
                {#pivot.alias#}
            </td>
            <td>

                {{--<a class="btn btn-xs btn-success" id="chapter-create-btn" data-toggle="modal"--}}
                   {{--data-target="#spu_modal" data-backdrop="static" data-keyboard="false"--}}
                   {{--data-url="{{route('admin.industry.classification.editClassification')}}?industry_id={#pivot.industry_id#}&nice_classification_id={#pivot.nice_classification_id#}">--}}
                    {{--<i class="fa fa-pencil" data-toggle="tooltip"--}}
                       {{--data-placement="top" title="" data-original-title="编辑"></i></a>--}}


                <a href="javascript:;"   data-url="{{route('admin.industry.classification.delete')}}"
                   class="btn btn-xs btn-danger operator" data-industry_id="{#pivot.industry_id#}" data-nice_classification_id="{#pivot.nice_classification_id#}">
                    <i class="fa fa-trash" data-toggle="tooltip" data-placement="top" data-original-title="删除"></i>
                </a>
            </td>
        </tr>
    </script>
    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/common.js') !!}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/jquery.http.js') !!}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/page/jquery.pages.js') !!}
    <script>
        function initButton() {
            //功能操作按钮
            $('.operator').on('click',function () {
                var obj = $(this);
                swal({
                    title: "确定删除该类别推荐吗?",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "删除",
                    closeOnConfirm: false
                }, function () {
                    var url = obj.data('url');
                    var data = {
                        industry_id: obj.data('industry_id'),
                        nice_classification_id: obj.data('nice_classification_id'),
                        _token: _token
                    };

                    $.post(url, data, function (ret) {
                        if (ret.status) {
                            obj.parent().parent().remove();
                            swal("删除成功!", "", "success");
                        } else {
                            swal("改规格值下面存在商品，不能删除!", "", "warning");
                        }
                    });

                });
            });
        }

        function getList() {

            var postUrl = '{{route('admin.industry.classification.getRecommendData')}}';

            $('.pages').pages({
                page: 1,
                url: postUrl,
                get: $.http.post.bind($.http),
                body: {
                    industry_id: $('input[name="industry_id"]').val(),
                    _token: _token
                },
                marks: {
                    total: 'data.last_page',
                    index: 'data.current_page',
                    data: 'data'
                }
            }, function (data) {
                var html = '';
                data.data.forEach(function (item) {
                    html += $.convertTemplate('#page-temp', item, '');
                });

                $('#industry_value_box').html(html);
                initButton();
            });
        }

        $(document).ready(function () {
            getList();

        });


        //排序
        function toRecommendSort(id) {
            console.log(id);
            if (id != '') {
                var va = $('#s' + id).val();
                console.log(va);
                var part = /^\d+$/i;
                if (va != '' && va != undefined && part.test(va)) {
                    $.get("{{ route('admin.industry.recommend_sort') }}", {
                        'nice_classification_id': id,
                        'sort': va,
                        'industry_id': $('input[name="industry_id"]').val(),
                        _token: _token
                    }, function (data) {
                        if (data.status) {
                            swal({
                                title: "修改分类排序成功！",
                                text: "",
                                type: "success"
                            }, function() {
                                location.reload();
                            });
                        } else {
                            swal("修改分类排序失败!", "", "error");
                        }
                    });
                }
            }
        }


    </script>