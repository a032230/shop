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


<div class="tab-div">
    <div id="tabbar-div">
        <p>
            <span class="tab-front" id="general-tab">通用信息</span>
        </p>
    </div>
    <div id="tabbody-div">
        <form enctype="multipart/form-data" action="/index.php/Admin/Goods/edit/id/1.html" method="post">
            <table width="90%" id="general-table" align="center">
                <input type="hidden" name='id' value="<?php echo ($data["id"]); ?>" />
                <tr>
                    <td class="label">分　　类：</td>
                    <td>
                        <select name="cat_id" id="">
                            <option value="">请选择分类</option>
                            <?php if(is_array($cats)): foreach($cats as $key=>$v): ?><option <?=$v['id'] == $data['cat_id'] ? 'selected' :'' ?> value="<?php echo ($v["id"]); ?>"><?php echo str_repeat('-',4*$v['level']) . $v['cat_name'] ?></option><?php endforeach; endif; ?>
                        </select>
                    <span class="require-field">*</span></td>
                </tr>
                <tr>
                    <td class="label">商品品牌：</td>
                    <td>

                        <!-- <select name="brand_id" id="">
                            <option value="">请选择品牌</option>
                            <?php if(is_array($list)): foreach($list as $k=>$v): ?><option value="<?php echo ($k); ?>" <?php echo $k==$data['brand_id'] ?'selected' : '' ?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
                        </select> -->
                        <?php buildSelect('brand','brand_id','id','brand_name',$data['brand_id']) ?>
                        <span class="require-field">*</span>
                    </td>
                </tr>
                <tr>
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
                    <td class="label">商品描述：</td>
                    <td>
                        <textarea id="goods_desc" name="goods_desc" ><?php echo ($data["goods_desc"]); ?></textarea>
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
<script>
UM.getEditor('goods_desc', {
    initialFrameWidth : "100%",
    initialFrameHeight : 350
});
</script>

<div id="footer">
共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。</div>
</body>
</html>