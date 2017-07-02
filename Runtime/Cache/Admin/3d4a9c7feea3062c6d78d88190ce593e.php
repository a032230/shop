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



<div class="main-div">
    <form name="main_form" method="POST" action="/index.php/Admin/Attr/edit/id/2/p/1/type_id/1.html" enctype="multipart/form-data" >
    	<input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
        <table cellspacing="1" cellpadding="3" width="100%">
            <tr>
                <td class="label">属性名：</td>
                <td>
                    <input  type="text" name="attr_name" value="<?php echo $data['attr_name']; ?>" />
                </td>
            </tr>
            <tr>
                <td class="label">属性类型：</td>
                <td>
                	<input type="radio" name="attr_type" value="唯一" <?php if($data['attr_type'] == '唯一') echo 'checked="checked"'; ?> />唯一 
                	<input type="radio" name="attr_type" value="可选" <?php if($data['attr_type'] == '可选') echo 'checked="checked"'; ?> />可选 
                </td>
            </tr>
            <tr>
                <td class="label">属性可选值：</td>
                <td>
                    <textarea cols="60" rows="10" name="attr_option_values"><?php echo $data['attr_option_values']; ?></textarea>
                </td>
            </tr>
            <tr>
                <td class="label">所属类型：</td>
                <td>
                    <!-- <input  type="text" name="type_id" value="<?php echo $data['type_id']; ?>" /> -->
                     <?php buildSelect('type','type_id','id','type_name',$data['type_id'])?>
                </td>
            </tr>
            <tr>
                <td colspan="99" align="center">
                    <input type="submit" class="button" value=" 确定 " />
                    <input type="reset" class="button" value=" 重置 " />
                </td>
            </tr>
        </table>
    </form>
</div>


<script>
</script>

<div id="footer">
共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。</div>
</body>
</html>