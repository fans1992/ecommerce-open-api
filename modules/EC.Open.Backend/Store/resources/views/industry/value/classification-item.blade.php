<style>
    .category-wrap{margin-top:15px}
    .category-content{margin-left:15px;padding: 0;}
    .major{width:100%;background-color:#fff;overflow:auto}
    .category-contents{background-color:#fff}
    .btn{margin-left:10px}
    .category_name{padding:5px;width:100%;background-color:#fafafa;overflow:auto}
    .category_name ul{list-style:none}
    /* 左边内容 */
    .category-content.titCon01 .category-wrap{
            margin-top: 5px;display: flex;align-items: center;
    }
    .category-content.titCon01 .btn-primary.btn-outline{
        padding: 6px 0;
        background-color: transparent;
        color: #000 !important;
        border: none;
        margin-left: 0;
    }
    .modal-dialog{
        width: 1200px !important;
    }
    .titCon02 {
        display: flex;
        flex-wrap: wrap;  
        height: auto;
        align-content: flex-start;
    }
    .titCon02  .btn-primary.btn-outline{
        color: #555 !important;
        font-size: 12px;
        background-color: transparent;
        padding: 6px 0;
        border: none;margin-left: 4px;
    }
    .titCon02 .category-wrap{
        display: flex;
        align-items: center;
        margin-top:6px;
        margin-right: 15px;
    }
    /* .category_name ul span{
        font-size:12px;
    } */
    .tit02{
        font-size:15px;
    }
    .category_ul li ul{
        display: flex;
        flex-direction: row; 
    }
    .category_ul li ul li{
        margin-right: 20px;
    }
</style>


<div class="major">
    <div id="hidden-category-id">
        @if(isset($cateIds))
            @foreach($cateIds as $classification_id)
                <input type="hidden" name="category_id[]" id=category_{{$classification_id}} value="{{$classification_id}}">
            @endforeach
        @endif
    </div>
    <div class="row category_name">
        <ul  class="category_ul">
            @if(isset($cateNames))
                @foreach($cateNames as $val)
                    <li class="" data-id="{{$val->id}}" data-parent="{{$val->parent_id | 0}}"><span class="tit02">{{$val->classification_name}}</span>
                        <ul></ul>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
    <div class="category-contents" style="display:flex;">
        <div class="category-content titCon01" data-position="left">
            @foreach($classifications as $key => $val)
                <div class="category-wrap">
                    <input data-id="{{$val->id}}" data-parent="{{$val->parent_id | 0}}" data-code="{{$val->classification_code}}" data-level="{{$val->level}}" data-name="{{$val->classification_name}}" data-uniqueId="categoryIds_{{$val->id}}" class="category_checks" type="checkbox" @if(isset($cateIds)) {{in_array($val->id, $cateIds) ? 'checked' : ''}} @endif />
                    &nbsp; <input class="btn btn-outline btn-primary category-btn" type="button" value="{{$val->classification_code . ' ' . $val->classification_name}}"/>
                </div>
            @endforeach
        </div>
        <div class="category-content titCon02" data-position="middle">
            @if(!empty($categoriesLevelTwo))
                @foreach($categoriesLevelTwo as $val)
                    @foreach($val as $v)
                        <div class="category-wrap">
                            <input data-id="{{$v->id}}" data-parent="{{$v->parent_id | 0}}" data-name="{{$v->classification_name}}" data-uniqueId="categoryIds_{{$v->id}}" class="category_checks" type="checkbox" @if(isset($cateIds)) {{in_array($v->id, $cateIds) ? 'checked' : ''}} @endif />
                            &nbsp;&nbsp;&nbsp;
                            <input class="btn btn-outline btn-primary category-btn" type="button" value="{{$v->classification_name}}"/>
                        </div>
                    @endforeach
                @endforeach
            @endif
        </div>
        <!-- <div class="category-content col-md-3" data-position="right">

        </div> -->
    </div>
</div>






