{!! Html::script(env("APP_URL").'/assets/backend/libs/pop.js?v=20180807') !!}
<script type="text/html" id="template">
    <div class="category-wrap">
        <input data-id="{#id#}" data-parent="{#parent_id#}"  data-name="{#value#}" data-level="{#level#}"
               data-uniqueId="categoryIds_{#id#}" class="category_checks" type="checkbox"/>
        <input class="btn btn-outline btn-primary category-btn" type="button" value="{#value#}"/>
    </div>
</script>

<script>
// 点击select下拉框
    var industry_id = $('input[name="industry_id"]').val();

    $("body .td_c").on("change", ".type-s", function () {
        var that = $(this);
        var val = that.find("option:selected").val();

        // var category_checked = [];
        var category_ids = [];
        // 初始化
        function initCategory() {
            // category_checked = [];
            category_ids = [];
            var data = {
                parentId:val,
                industryId:industry_id,
                "type-select-category-button": true,
                _token: _token
            };
            $.get('{{route('admin.industry.get_classification')}}', data, function (html) {
                $('#category-box').children().remove();
                $('#category-box').append(html);
                $('#category-box').find("input").iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                    increaseArea: '20%'
                });
            });
        }

        @if(!isset($goods_info))
        initCategory();
        @endif

        $("#hidden-category-id input").each(function () {
          let parID  = $(this).data("val");
            category_ids.push([parseInt(parID), parseInt($(this).val())]);
        });
        // category_checked = $(".category_name").text().split("/");

        initTheOrderCheckedCats();

        function moveTheOrderCat($parentObject, template) {
            if ($parentObject.length == 1) {
                $parentObject.children('ul').append(template);
            } else {
                $(".category_name").children('ul').append(template);
            }
        }

        function initTheOrderCheckedCats() {
            $(".category_name li").each(function () {
                var parentId = $(this).data('parent');
                var $parentObject = $(".category_name").find('[data-id=' + parentId + ']');
                moveTheOrderCat($parentObject, $(this));
            });
        }

        function addTheOrderCheckedCat(dataId, dataParentId, dataName, dataCode='') {

            var whetherExistNode = $(".category_name").find('[data-id=' + dataId + ']').length;
            if (0 == whetherExistNode) {
                var template = " <li data-id=" + dataId + " data-parent=" + dataParentId + "><span>" + dataCode + " " + dataName +
                    "</span><ul></ul>" +
                    " </li>";
                var $parentObject = $(".category_name").find('[data-id=' + dataParentId + ']');
                moveTheOrderCat($parentObject, template);
            }
        }
        // 移除已经选中的二级与三级选项
        function removeTheOrderCheckedCat(dataId) {
            var $node = $(".category_name").find('[data-id=' + dataId + ']');
            var $childrenNode = $node.children('ul').children();
            if ($childrenNode.length > 0) {
                var $nodeParent = $node.parents('li').first();
                // moveTheOrderCat($nodeParent, $childrenNode);
                $nodeParent.find('ul').html("");
            }
            $node.remove();
        }

        function operator($object, id, parentId, level, flag) {
            // $flag =1 表示checked操作， $flag=2 表示unchecked操作， $flag=3表示点击钮
            // $object 表示 category-content类对象

            // 首先 写unchecked操作
            if (flag == 2) {
                // 在category_ids里面找parentId
                category_ids.forEach(function(item, i){
                    if (item[1] == id) {
                        category_ids.splice(i, 1);        
                    }
                })
                // var positionIndex = category_ids.indexOf(id);
                // category_ids.splice(positionIndex, 1);

                // 同上， 将parentName从category_checked中移除
                // positionIndex = category_checked.indexOf(parentName);
                // category_checked.splice(positionIndex, 1);

                //将表单中的hidden 某个category_id移除
                $("#hidden-category-id").find("#category_" + id).remove();
            } else {
                // html
                var html = "";

                if (level == 3) {
                    //3级分类直接处理
                    handle($object, id, parentId, flag, html);
                    return;
                }

                // 1级2级分类 在flag =1 或者 flag=3时 一定会向后台请求数据
                //var groupId = $("select[name=category_group]").children('option:selected').val();
                var data = {
                    "parentId": id,
                    //"groupId": groupId,
                    "type-click-category-button": true
                };
                outID = id;
                $.get(
                    "{{route('admin.industry.get_classification')}}", data,
                    function (json) {
                        for (var i = 0; i < json.length; i++) {
                            var data = {
                                id: json[i].id,
                                value: json[i].classification_name,
                                parent_id: json[i].parent_id,
                                classification_code:json[i].classification_code,
                                level:json[i].level,
                            }
                            html = html + $.convertTemplate('#template', data, '');
                        }
                        // 点击左边标题， 将右边内容清空  重新复制
                        var $nextObject = $object.next();
                        // 首先将 $nextObject里面的内容清空
                        $nextObject.children().remove();
                        $nextObject.append(html);
                        // debugger;
                        $(".category_checks").iCheck({checkboxClass: 'icheckbox_square-green'});
                        //将id存在于 category_ids里的 checkbox checked
                        for (var i = 0; i < category_ids.length; i++) {
                            $("input[data-uniqueId=categoryIds_" + category_ids[i][1] + "]").iCheck('check');
                        }
                        handle($object, id, parentId, flag, html);
                    });
            }
        }

        function handle($object, id, parentId, flag, html) {
            // 异步请求后， 模板数据全都存在于var html中 下一步获得 类为 category-content的位置 这里有个bug,  应该要放进 ajax里面
            if (1 == flag) {
                id = parseInt(id);
                if (category_ids.length == 0) {
                    category_ids.push([parentId,id]);
                    $("#hidden-category-id").append("<input  type=\"hidden\" name=\"category_id[]\" id=category_" + id + " data-val=" +parentId + "  value=" + id + ">");
                }
                else{
                    $.each(category_ids,function(i,item){
                        console.log('item', item);
                        if (item[1]  == id) {
                            return false;
                        }
                        if (item[1] != id &&  i == category_ids.length - 1) {
                            category_ids.push([parentId,id]);
                            $("#hidden-category-id").append("<input  type=\"hidden\" name=\"category_id[]\" id=category_" + id + " data-val=" +parentId + "  value=" + id + ">");
                        }
                    })
                }
               console.log('flag,1 category_ids',  category_ids);
            }

        }
        $('body').on('click', '.category-btn', function () {
            // 获得相邻的checkbox
            var $checkbox = $(this).prev().find(':checkbox');
            var id = $checkbox.data('id');
            var name = $checkbox.data('name');
            var level = $checkbox.data('level');
            var $parentCategoryContent = $checkbox.closest('.category-content');
            operator($parentCategoryContent, id, name, level, 3);
        });
        $('body').on('ifChanged', '.category_checks', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var parentId = $(this).data('parent');
            var level = $(this).data('level');
            var code = $(this).data('code');
            var $parentCategoryContent = $(this).closest('.category-content');
            console.log("$(this).is(':checked')", $(this).is(':checked'));
            if ($(this).is(':checked')) { // 选中状态
                operator($parentCategoryContent, id, parentId, level, 1);
                addTheOrderCheckedCat(id, parentId, name, code);
                // 右边但凡有一个复选框选中，左边对应 复选框也自动选中
                if($(".titCon02").find(".checked").length ==  1){
                    console.log('input', $("input[data-uniqueId=categoryIds_" + parentId + "]").is(':checked'));
                    if (!$("input[data-uniqueId=categoryIds_" + parentId + "]").is(':checked')) {
                        $("input[data-uniqueId=categoryIds_" + parentId + "]").iCheck('check');    
                    }
                    
                }
            } else {
                console.log('outID', outID, "id", id , "id == outID", id == outID);
                // 点击右边的复选框
                operator($parentCategoryContent, id, parentId, level, 2); 
                removeTheOrderCheckedCat(id);
                 if (parseInt(level) == 2 ){
                    // 判断当前选中的二级标题 跟 最后一次发送后台请求的二级标题 是否一样
                    if (id == outID ) {
                         // 移除右边已经选中的所有复选框 
                        $('.titCon02 .category-wrap .icheckbox_square-green').each(function(item,i) {
                            if ($(this).is(".checked")) {
                                $(this).removeClass("checked");
                                var rightID  = $(this).find("input").data('id');
                                console.log('rightID', rightID);
                                // var rightName  = $(this).find("input").data('name');
                                // 在category_ids里面找parentId
                                $.each(category_ids,function(i,item){
                                    console.log('item', item);
                                    if (item[1] == rightID) {
                                        console.log('移除右侧i', rightID);
                                        category_ids.splice(i, 1);
                                        $("#hidden-category-id").find("#category_" + rightID).remove();
                                        return false;
                                    }
                                })
                            }
                        }) 
                    } else{
                        // 从数组category_ids中删除 右边的复选框已选中状态的id 与 左边复选框的id
                        var iArr=[];
                        var category_idsNew = []
                        $.each(category_ids,function(i,item){
                            console.log('item', item);
                            if (item.indexOf(id) > -1) {
                                console.log('移除右侧item', item);
                                $("#hidden-category-id").find("#category_" + item[1]).remove();
                            } else {
                                category_idsNew.push(item)  
                            }
                        })
                        
                        category_ids = category_idsNew 
                        
                    }
                    console.log('level =2, category_ids', category_ids);
                 } 
                // 右边的复选项取消选中
                else{
                    console.log('category_ids', category_ids,"id", id);
                    //将表单中的hidden 某个category_id移除
                    $("#hidden-category-id").find("#category_" + id).remove();
                }
            }
            console.log('category_ids', category_ids);
        });
    });
    // 点击 保存 按钮
    $('#spec-value-form').ajaxForm({
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
    // 点击 搜索按钮 实现查询
    $(".search button").click(function(){
        var inputVal = $(".search input").val();
        console.log('inputVal', inputVal);
        var data = {
                parentId:0,
                search:inputVal,
                _token: _token
            };
            $.get('{{route('admin.industry.get_classification')}}', data, function (html) {
                console.log('后台返回的字段', html);
                // select class="form-control type-s" name="top_nice_classification_id">
                //                 <option value="">请选择</option>
                //                 @foreach($classifications as $item)
                //                     <option value="{{$item->id}}">{{'第' . $item->classification_code .'类'}}</option>
                //                 @endforeach
                //             </select>
                // $(".type-s").children().remove();
                // $(".type-s").append(html);

             
            });
    })


    //根据显示类型返回格式
    function getTr() {
        //数据
        var specRow = '<tr class="td_c"><td>' + '<select class="form-control"  name="top_nice_classification_id">'
            + '<option value="">请选择</option>' + @foreach($classifications as $item)' + <option value="{{$item->id}}">{{$item->classification_code. '-' .$item->classification_name}}</option>' +@endforeach' + </select>'
                +'<td><input type="text" class="form-control" name="alias" />' +
                '</td>' +
                        '<td><a href="javascript:;" class="btn btn-xs btn-primary operatorPhy">' +
                '<i class="fa fa-trash" data-toggle="tooltip" data-placement="top" data-original-title="删除"></i></a></td></tr>';

        return specRow;
    }

    //预定义

    $('#spec_box tr').each(
            function (i) {
                initButton(i);
            }
    );


    //添加规格按钮(点击绑定)
    $('#specAddButton').click(
            function () {

                var specRow = getTr();

                $('#spec_box').append(specRow);

            initButton(specSize);
            }
    );

    //按钮(点击绑定)
    function initButton(indexValue) {
        //功能操作按钮
        $('#spec_box tr:eq(' + indexValue + ') .operator').click(function () {
            var obj = $(this);
            swal({
                title: "确定删除该规格值吗?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "删除",
                closeOnConfirm: false
            }, function () {
                var url = obj.data('url');
                var data = {
                    id: obj.data('id'),
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

        $('#spec_box tr:eq(' + indexValue + ') .operatorPhy').click(function () {
            $(this).parent().parent().remove();
        });
    }
</script>
