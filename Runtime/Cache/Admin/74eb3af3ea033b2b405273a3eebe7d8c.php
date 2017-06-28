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
    .cat_list{
        padding: 0;
        margin: 0;
    }
    .cat_list li{
        margin: 5px 0;
        list-style: none;
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
        <form enctype="multipart/form-data" action="/index.php/Admin/Goods/edit/id/7.html" method="post">
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
            </table>
            <table style="display: none;" width="90%" class="tab-table"  align="center">
                <tr>
                    <td>
                        <textarea id="goods_desc" name="goods_desc" cols="40" rows="3"><?php echo ($data["goods_desc"]); ?></textarea>
                    </td>
                </tr>
            </table>
            <table style="display: none;" width="90%" class="tab-table"  align="center">
                <tr>
                    <td class="label"></td>
                    <td>
                        <?php if(is_array($mldata)): foreach($mldata as $k=>$v): echo ($v); ?>：￥<input type="text" name="member_price[<?php echo ($k); ?>]" value="<?php echo ($mpdata[$k]); ?>" size="10" /><br /><br /><?php endforeach; endif; ?>
                    </td>
                </tr>
            </table>
            <table style="display: none;" width="90%" class="tab-table"  align="center"></table>
            <table style="display: none;" width="90%" class="tab-table"  align="center"></table>
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
<script>
UM.getEditor('goods_desc', {
    initialFrameWidth : "100%",
    initialFrameHeight : 350
});


//tab切换
$('#tabbar-div p span').on('click',function(){
    var i = $(this).index();
    $('.tab-table').eq(i).show().siblings('.tab-table').hide();
});

//扩展分类复制
$('.add_cat').on('click',function(){
    $('.cat_list').append($('.cat_list').find('li').eq(0).clone());
})
</script>

<div id="footer">
共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。</div>
</body>
</html>