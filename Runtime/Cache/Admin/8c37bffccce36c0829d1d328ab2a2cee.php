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
    a{
        text-decoration: none !important;
    }
</style>
<!-- 商品列表 -->
<form method="post" action="/index.php/Admin/Goods/goods_number/id/18.html" name="listForm">
    <div class="list-div" id="listDiv">
        <table cellpadding="3" cellspacing="1">
            <tr>
            	<?php if(is_array($gadata)): foreach($gadata as $k=>$v): ?><th><?php echo ($k); ?></th><?php endforeach; endif; ?>
                <th>库存量</th>
                <th>操作</th>
            </tr>
          <?php if($gndata):?>
            <?php if(is_array($gndata)): foreach($gndata as $k1=>$v1): ?><tr>
            	<?php if(is_array($gadata)): foreach($gadata as $k=>$v): ?><td>
            		<select name="goods_attr_id[]">
            			<option value="">请选择</option>
            			<?php if(is_array($v)): foreach($v as $key=>$val): $attr = explode(',',$v1['goods_attr_id']); if(in_array($val['id'],$attr)){ $selected = "selected='selected'"; } else { $selected = ''; } ?>
            			<option <?=$selected?> value="<?php echo ($val["id"]); ?>"><?php echo ($val["attr_value"]); ?></option><?php endforeach; endif; ?>
            		</select>
            	</td><?php endforeach; endif; ?>
                <td align="center" ><input type="text" name="goods_number[]" value="<?php echo ($v1["goods_number"]); ?>" size="10"></td>
                <td align="center" ><input onclick="addNewTr(this)" type="button" value="<?php echo ($k1 == 0 ? '+' : '-'); ?>"></td>
            </tr><?php endforeach; endif; ?>
           	<?php else :?>
           	<tr>
           		<?php if($gadata):?>
            	<?php if(is_array($gadata)): foreach($gadata as $k=>$v): ?><td>
            		<select name="goods_attr_id[]">
            			<option value="">请选择</option>
            			<?php if(is_array($v)): foreach($v as $key=>$val): ?><option value="<?php echo ($val["id"]); ?>"><?php echo ($val["attr_value"]); ?></option><?php endforeach; endif; ?>
            		</select>
            	</td><?php endforeach; endif; ?>
                <td align="center" ><input type="text" name="goods_number[]" size="10" ></td>
                <td align="center" ><input onclick="addNewTr(this)" type="button" value="+"></td>
           	 <?php endif?>
            </tr>
       		<?php endif?>
            <tr id="sub">
            	<td  align="center" colspan="<?=count($gadata)+2?>"><input type="submit" value="提交"></td>
            </tr>
        </table>
    </div>
</form>

<script type="text/javascript">
	//克隆tr
	function addNewTr(btn)
	{
		var tr = $(btn).parent().parent();
		if($(btn).val() == '+')
		{
			var newTr = tr.clone();
			newTr.find('input').val('');
			newTr.find('input[type=button]').val('-');
			$('#sub').before(newTr);
		}
		else
		{
			tr.remove();
		}
		
	}
</script>



<div id="footer">
共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。</div>
</body>
</html>