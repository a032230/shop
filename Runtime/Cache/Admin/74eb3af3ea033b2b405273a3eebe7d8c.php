<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ECSHOP 管理中心 - 商品列表 </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/Public/Admin/styles/general.css" rel="stylesheet" type="text/css" />
<link href="/Public/Admin/styles/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/umeditor1_2_2-utf8-php/third-party/jquery.min.js"></script>

</head>
<body>

<h1>
	<?php if($_page_btn_name):?>
    <span class="action-span"><a href="<?php echo ($_page_btn_link); ?>"><?php echo ($_page_btn_name); ?></a></span>
    <?php endif?>
    <span class="action-span1"><a href="<?php echo U('lst');?>">ECSHOP 管理中心</a></span>
    <span id="search_id" class="action-span1"> - <?php echo ($_page_title); ?> </span>
    <div style="clear:both"></div>
</h1>



<style>
    ul{
        padding: 0;
        margin: 0;
    }
    .cat_list li{
        margin: 5px 0;
        
    }
    li{
        list-style: none;
    }
    .pic_list li{
        margin: 5px 0;
    }
    .old_list li{
        margin-right: 10px;
        float: left;
    }
     #attr_list li{
        margin-bottom: 5px;
    }
</style>
<div class="tab-div">
    <div id="tabbar-div">
        <p>
            <span class="tab-front" >基本信息</span>
            <span class="tab-back" >商品描述</span>
            <span class="tab-back" >会员价格</span>
            <span class="tab-back" >商品属性</span>
            <span class="tab-back">商品相册</span>
        </p>
    </div>
    <div id="tabbody-div">
        <form enctype="multipart/form-data" action="/index.php/Admin/Goods/edit/id/19.html" method="post">
        	<input type="hidden" name='id' value="<?php echo ($data["id"]); ?>" />

            <table width="90%" class="tab-table" align="center">
                <tr>
                    <td class="label">分　　类：</td>
                    <td>
                        <select name="cat_id" id="">
                            <option value="">请选择主分类</option>
                            <?php if(is_array($cats)): foreach($cats as $key=>$v): ?><option <?=$v['id'] == $data['cat_id'] ? 'selected' :'' ?>  value="<?php echo ($v["id"]); ?>">
                            <?php echo str_repeat('-',4*$v['level']) . $v['cat_name'] ?>
                            	
                            </option><?php endforeach; endif; ?>
                        </select>
                    <span class="require-field">*</span></td>
                </tr>
                <tr>
                    <td class="label">扩展分类： <input class="add_cat" type="button" value="增加一个"></td>
                    <td>
                    <?php if($ext):?>
                    	<?php if(is_array($ext)): foreach($ext as $key=>$val): ?><ul class="cat_list">
                            <li>
                                <select name="ext[]" id="">
                                <option value="">请选择扩展分类</option>
                                <?php if(is_array($cats)): foreach($cats as $key=>$v): ?><option <?=$v['id'] == $val['cat_id'] ? 'selected' : '' ?> value="<?php echo ($v["id"]); ?>">
                                	<?php echo str_repeat('-',4*$v['level']) . $v['cat_name'] ?>
                                	
                                </option><?php endforeach; endif; ?>
                                </select>
                            </li>
                        </ul><?php endforeach; endif; ?>
                    <?php else:?>
                    	<ul class="cat_list">
                            <li>
                                <select name="ext[]" id="">
                                <option value="">请选择扩展分类</option>
                                <?php if(is_array($cats)): foreach($cats as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"><?php echo str_repeat('-',4*$v['level']) . $v['cat_name'] ?></option><?php endforeach; endif; ?>
                                </select>
                            </li>
                        </ul>
                    <?php endif?>
                    </td>
                </tr>
                <tr>
                    <td class="label">品　　牌：</td>
                    <td>
                        <!-- <select name="brand_id" id="">
                            <option value="">请选择品牌</option>
                            <?php if(is_array($list)): foreach($list as $k=>$v): ?><option value="<?php echo ($k); ?>"><?php echo ($v); ?></option><?php endforeach; endif; ?>
                        </select> -->
                        <?php buildSelect('brand','brand_id','id','brand_name',$data['brand_id']) ?>
                    <span class="require-field">*</span></td>
                </tr>
                <tr>
                    <td class="label">商品名称：</td>
                    <td><input type="text" name="goods_name" value="<?php echo ($data["goods_name"]); ?>"size="30" />
                    <span class="require-field">*</span></td>
                <tr>
                    <td class="label">本店售价：</td>
                    <td>
                        <input type="text" name="shop_price" value="<?php echo ($data["shop_price"]); ?>" size="20"/>
                        <span class="require-field">*</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">市场售价：</td>
                    <td>
                        <input type="text" name="market_price" value="<?php echo ($data["market_price"]); ?>" size="20" />
                        <span class="require-field">*</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">商品logo：</td>
                    <td>
                        <p><?php showImage($data['mid_logo'],50) ?>  </p> 
                        <input type="file" name="logo" value="<?php echo ($data["mid_logo"]); ?>"  />
                    </td>
                </tr>
                <tr>
                    <td class="label">是否上架：</td>
                    <td>
                        <input type="radio" name="is_on_sale" value="是" <?php echo $data['is_on_sale']=='是'?'checked':''?> /> 是
                        <input type="radio" name="is_on_sale" value="否" <?php echo $data['is_on_sale']=='否'?'checked':''?> /> 否
                    </td>
                </tr>
                 <tr>
                    <td class="label">促销价格：</td>
                    <td>
                        价格：<input type="text" name="promote_price" value="<?php echo ($data["promote_price"]); ?>" size="20" />
                       开始时间：<input type="text" id="start" name="promote_start_date" value="<?php echo ($data["promote_start_date"]); ?>" size="20" />
                       结束时间：<input type="text" id="end" name="promote_end_date" value="<?php echo ($data["promote_end_date"]); ?>" size="20" />
                    </td>
                </tr>
                <tr>
                    <td class="label">是否新品：</td>
                    <td>
                        <input type="radio" name="is_new" value="是" <?php echo $data['is_new']=='是'?'checked':''?> /> 是
                        <input type="radio" name="is_new" value="否" <?php echo $data['is_new']=='否'?'checked':''?> /> 否
                    </td>
                </tr>
                <tr>
                    <td class="label">是否热卖：</td>
                    <td>
                        <input type="radio" name="is_hot" value="是" <?php echo $data['is_hot']=='是'?'checked':''?> /> 是
                        <input type="radio" name="is_hot" value="否" <?php echo $data['is_hot']=='否'?'checked':''?>/> 否
                    </td>
                </tr>
                <tr>
                    <td class="label">是否精品：</td>
                    <td>
                        <input type="radio" name="is_best" value="是" <?php echo $data['is_best']=='是'?'checked':''?> /> 是
                        <input type="radio" name="is_best" value="否" <?php echo $data['is_best']=='否'?'checked':''?> /> 否
                    </td>
                </tr>
                <tr>
                    <td class="label">是否推荐到楼层：</td>
                    <td>
                        <input type="radio" name="is_floor" value="是" <?php echo $data['is_floor']=='是'?'checked':''?> /> 是
                        <input type="radio" name="is_floor" value="否" <?php echo $data['is_floor']=='否'?'checked':''?>  /> 否
                    </td>
                </tr>
                <tr>
                    <td class="label">排　　序：</td>
                    <td>
                        <input type="text" name="sort_num" value="<?php echo ($data["sort_num"]); ?>" size="20" />
                    </td>
                </tr>
            </table>
            <!-- 商品描述 -->
            <table style="display: none;" width="90%" class="tab-table"  align="center">
                <tr>
                    <td>
                        <textarea id="goods_desc" name="goods_desc" cols="40" rows="3"><?php echo ($data["goods_desc"]); ?></textarea>
                    </td>
                </tr>
            </table>
            <!-- 会员价格 -->
            <table style="display: none;" width="90%" class="tab-table"  align="center">
                <tr>
                    <td class="label"></td>
                    <td>
                        <?php if(is_array($mldata)): foreach($mldata as $k=>$v): echo ($v); ?>：￥<input type="text" name="member_price[<?php echo ($k); ?>]" value="<?php echo ($mpdata[$k]); ?>" size="10" /><br /><br /><?php endforeach; endif; ?>
                    </td>
                </tr>
            </table>
            <!-- 商品属性 -->
            <table style="display: none;" width="90%" class="tab-table"  align="center">
                <tr>
                    <td class="label">商品属性:</td>
                    <td> <?php buildSelect('type','type_id','id','type_name',$data['type_id']) ?></td>
                </tr>   
                <tr>
                    <td class="label"></td>
                    <td>
                        <ul id="attr_list">
                            <?php
 $attrId = array(); foreach($gadata as $v){ if(in_array($v['id'],$attrId)){ $opt = '[-]'; }else{ $attrId[] = $v['id']; $opt = '[+]'; } ?>
                            <li>
                                <input type="hidden" name="goods_attr_id[]" value="<?php echo ($v["goods_attr_id"]); ?>">
                                <?php if($v['attr_type'] == '可选'):?>
                                    <a onclick="addNewAttr(this)" href="javascript:;"><?php echo ($opt); ?></a>
                                <?php endif?>
                                <?php echo ($v["attr_name"]); ?> :
                                <?php  if($v['attr_option_values']): $attr = explode(',',$v['attr_option_values']); ?>
                                    <select name="attr_value[<?php echo ($v["id"]); ?>][]" id="">
                                        <option value="">请选择...</option>
                                        <?php if(is_array($attr)): foreach($attr as $key=>$val): ?><option <?=$val == $v['attr_value'] ? 'selected' : ''?> value="<?php echo ($val); ?>"><?php echo ($val); ?></option><?php endforeach; endif; ?>
                                    </select>
                                <?php else:?>
                                    <input name="attr_value[<?php echo ($v["id"]); ?>][]" type="text" value="<?php echo ($v["attr_value"]); ?>">
                                <?php endif?>
                            </li>
                            <?php } ?>
                        </ul>
                    </td>
                </tr>
            </table>
            <!-- 商品相册 -->
            <table style="display: none;" width="90%" class="tab-table"  align="center">
                <tr>
                    <td class="label" style="text-align: center">
                        <input class="add_pic" type="button" value="添加一张">
                        <hr>
                         <ul class="pic_list">
                         </ul>
                    </td>
                    <td>
                        <ul class="old_list">
                            <?php if(is_array($gpdata)): foreach($gpdata as $key=>$v): ?><li>
                                 <?php showImage($v['mid_pic'],150) ?>
                                 <br>
                                 <button class="delpic" alt="<?php echo ($v["id"]); ?>" style="margin: 5px 0 0 60px">删除</button>
                             </li><?php endforeach; endif; ?>
                         </ul>
                    </td>
                </tr>
            </table>
            <div class="button-div">
                <input type="submit" value=" 确定 " class="button"/>
                <input type="reset" value=" 重置 " class="button" />
            </div>
        </form>
    </div>
</div>

<!--导入在线编辑器 -->
<link href="/Public/umeditor1_2_2-utf8-php/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
<script type="text/javascript" charset="utf-8" src="/Public/umeditor1_2_2-utf8-php/umeditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/Public/umeditor1_2_2-utf8-php/umeditor.min.js"></script>
<script type="text/javascript" src="/Public/umeditor1_2_2-utf8-php/lang/zh-cn/zh-cn.js"></script>
<!-- 引入时间插件 -->
<link href="/Public/datetimepicker/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="/Public/datetimepicker/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/Public/datetimepicker/datepicker-zh_cn.js"></script>
<link rel="stylesheet" media="all" type="text/css" href="/Public/datetimepicker/time/jquery-ui-timepicker-addon.min.css" />
<script type="text/javascript" src="/Public/datetimepicker/time/jquery-ui-timepicker-addon.min.js"></script>
<script type="text/javascript" src="/Public/datetimepicker/time/i18n/jquery-ui-timepicker-addon-i18n.min.js"></script>

<script>
UM.getEditor('goods_desc', {
    initialFrameWidth : "100%",
    initialFrameHeight : 350
});
// 添加时间插件
$.timepicker.setDefaults($.timepicker.regional['zh-CN']);  // 设置使用中文 

$("#start").datetimepicker();
$("#end").datetimepicker();


//tab切换
$('#tabbar-div p span').on('click',function(){
    var i = $(this).index();
    $('.tab-table').eq(i).show().siblings('.tab-table').hide();
    $(this).addClass('tab-front').siblings().removeClass('tab-front');
});

//相册图片
$('.add_pic').on('click',function(){
    var file = "<li><input name='pic[]' type='file' /></li>";
    $('.pic_list').append(file);
});

//扩展分类复制
$('.add_cat').on('click',function(){
    $('.cat_list').append($('.cat_list').find('li').eq(0).clone());
})

//ajax删除相册图片
$('.delpic').on('click',function(){
    if(confirm('确定要删除吗？')){
    //获取删除对象
    var li = $(this).parent('li');
    //获取商品id
    var pid = $(this).attr('alt');
    $.ajax({
        type : 'GET',
        url : "<?php echo U('ajaxDelPic','',FALSE);?>/pid/"+pid,
        success: function(data)
        {
            li.remove();
        }
    });

    }
    return false;
})
//选择商品类型获取属性ajax
$('select[name=type_id]').change(function(){
    //获取类型id
    var typeId = $(this).val();
    //id大于0才执行ajax
    if(typeId > 0){
        $.ajax({
            type : 'get',
            url : "<?php echo U('ajaxGetAttr','',false);?>/type_id/"+typeId,
            dataType : 'json',
            success : function(data)
            {
                /******将服务器返回的属性循环遍历成一个li的字符串显示在页面******/
                var li ='';
                $(data).each(function(k,v){
                    li += '<li>';
                    //属性类型为可选时,前面加上'[+]'；
                    if(v.attr_type == '可选')
                    {
                        li += "<a onclick='addNewAttr(this)' href='javascript:;'>[+]</a>";
                    }
                    //属性名
                    li += v.attr_name +' : ';
                    // 如何没有可选值就用文本框
                    if(v.attr_option_values == '')
                    {
                        li += "<input name='attr_value["+v.id+"][]' type='text' name='' />"
                    }else{
                        //有可选值就用下拉框
                        li += "<select name='attr_value["+v.id+"][]'><option value=''>请选择...</option>";
                        //再将可选值以,分割成数组
                        var _attr = v.attr_option_values.split(',');

                        //循环每个值制作option
                        for(var i = 0; i < _attr.length;i++)
                        {
                            li += '<option value="'+_attr[i]+'">'+_attr[i]+'</option>';
                        }
                        li += '</select>';
                    }
                    li += '</li>';
                    // console.log(li);
                })
                //放到页面
                $('#attr_list').html(li);
            }
        })
    }else{
        $('#attr_list').html('');
    }

})

//可选属性有多个可以进行添加下拉框
function addNewAttr(a)
{
    var li = $(a).parent('li');
    if($(a).text() == '[+]')
    {
        //克隆一份
        var newLi = li.clone();
        //去掉选中状态
        newLi.find('option').removeAttr('selected');
        //情况隐藏域的值
        newLi.find('input[type=hidden]').val('');
        // 变-
        newLi.find('a').text('[-]');
        //放到li的后面
        li.after(newLi);
    }
    else
    {
        //删除
        //获取属性id
        var gaid = li.find("input[type=hidden]").val();
        //判断gaid是否存在以区别是新添加的还是存在的原属性
        if(gaid == '')
        {
            //新添加的直接删除
            li.remove();
        }
        else
        {   //删除数据库对应的商品属性id的记录
            if(confirm('如果删除这个属性,相关的库存量也会被删除，确定要删除吗？'))
            {
                $.ajax({
                    type : 'get',
                    url : "<?php echo U('ajaxDelAttr','',false);?>/gaid/" + gaid + '/goods_id/'+ '<?php echo ($data['id']); ?>',
                    success : function(data)
                    {
                        //同时删除dom节点
                        li.remove()
                    }
                });
            }  
        }
    }
}
</script>

<div id="footer">
共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。</div>
</body>
</html>