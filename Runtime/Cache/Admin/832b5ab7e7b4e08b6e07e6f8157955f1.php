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
    <form name="main_form" method="POST" action="/index.php/Admin/Category/edit/id/22.html" enctype="multipart/form-data">
        <table cellspacing="1" cellpadding="3" width="100%">
            <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>">
             <tr>
                <td class="label">上级分类：</td>
                <td>
                    <select name="parent_id">
                            <option value="">顶级分类</option>
                        <?php if(is_array($tree)): foreach($tree as $key=>$v): if($v['id'] == $data['id'] || in_array($v['id'],$children)){ continue; } ?>
                        <option <?php echo $v['id'] == $data['parent_id'] ? 'selected' : ''?> value="<?php echo ($v["id"]); ?>"><?=str_repeat('-',4*$v['level']); echo ($v["cat_name"]); ?></option><?php endforeach; endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label">分类名称：</td>
                <td>
                    <input  type="text" name="cat_name" value="<?php echo ($data["cat_name"]); ?>" />
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
                <td colspan="99" align="center">
                    <input type="submit" class="button" value=" 确定 " />
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