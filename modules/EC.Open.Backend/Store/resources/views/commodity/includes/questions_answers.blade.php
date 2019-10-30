<style type="text/css">
</style>
<div class="tab-pane" id="tab_7">
    <div class="panel-body">

        <div class="table-responsive">
            <table class="table table-bordered table-stripped" id="question_menu_table">
                <thead>
                <tr>
                    <th>
                        问题
                    </th>
                    <th>
                        答案
                    </th>

                    <th>
                        排序(数字越大排在越前)
                    </th>
                    <th>
                        操作
                    </th>
                </tr>
                </thead>
                <tbody id='qustion_box'>
                @if(isset($goods_info))
                    @foreach($goods_info->GoodsQuestions as $key => $val)
                        <tr data-id="{{$val['code']}}" class="top_menu" id="menu_id_{{$val['code']}}">
                            <td>
{{--                                <input type="text" class="form-control" name="_questionlist[{{$val['code']}}][sort]" value="{{$val['sort']}}">--}}
                                <textarea class="form-control" name="_questionlist[{{$val['code']}}][question]" placeholder="" rows="4">
                                    {{$val['question']}}
                                </textarea>
                            </td>
                            <td>
                                <textarea class="form-control" name="_questionlist[{{$val['code']}}][answer]" placeholder="" rows="4">
                                    {{$val['answer']}}
                                </textarea>
                                {{--<input type="hidden" name="_imglist[{{$val['code']}}][url]" value="{{$val['url']}}">--}}
                                {{--<input type="text" class="form-control" disabled="" value="{{$val['url']}}">--}}
                            </td>
                            <td>
                                <input type="text" class="form-control" name="_questionlist[{{$val['code']}}][sort]" value="{{$val['sort']}}">
                            </td>
                            <td>
                                <a href="javascript:;" class="btn btn-white" onclick="delQuestion(this)"><i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                {{--<div id="upload" class="btn btn-primary">选择图片</div>--}}
                <button id="questionAddButton" type="button" class="btn btn-w-m btn-primary">继续添加</button>

                <div class="clearfix"></div>
            </div>
        </div>
        <script type="text/x-template" id="question_menu_template">
            <tr data-id="{MENU_ID}" class="top_menu" id="menu_id_{MENU_ID}">
                <td>
                    <textarea class="form-control" name="_questionlist[{MENU_ID}][question] placeholder="" rows="4">
                                </textarea>
                </td>
                <td>
                    <textarea class="form-control" name="_questionlist[{MENU_ID}][answer] placeholder="" rows="4">
                    </textarea>
                </td>
                <td>
                    <input type="text" class="form-control" name="_questionlist[{MENU_ID}][sort]" value="9">
                </td>
                <td>
                    <a href="javascript:;" class="btn btn-white"
                       onclick="delQuestion(this)"><i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>
        </script>
    </div>
    <div class="app-actions">
        <a data-id="6" data-action="next" class="btn btn-success app-action-prev">«上一步</a>
        <a data-id="1" data-action="next" class="btn btn-success app-action">下一步»</a>
        <input type="submit" class="btn btn-success app-action-save" data-toggle="form-submit" data-target="#base-form"
               value="保存">
    </div>
</div><!-- /.tab-pane -->