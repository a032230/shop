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


<!-- 商品列表 -->
<form method="post" action="/index.php/Admin/Category/lst" name="listForm" class="subform">
    <div class="list-div" id="listDiv">
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th>分类名称</th>
                <th>操作</th>
            </tr>
            <?php if(is_array($data)): foreach($data as $key=>$val): ?><tr>
                <td ><?php echo str_repeat('-',4*$val['level']); echo ($val["cat_name"]); ?></td>
                <td align="center">
                <a href="<?php echo U('edit',array('id' => $val['id']));?>" title="上架">编辑</a>
				|
                <a class="subs" onclick="return  confirm('你确定要删除该分类吗？');" name="<?php echo U('del',array('id' => $val['id']),false);?>">删除</a>
                </td>
            </tr><?php endforeach; endif; ?>
        </table>

    <!-- 分页开始 -->
        <table id="page-table" cellspacing="0">
            <tr>
                <td width="80%">&nbsp;</td>
                <td align="center" nowrap="true">
                    <?php echo ($page); ?>
                </td>
            </tr>
        </table>
    <!-- 分页结束 -->
    </div>
</form>
<script type="text/javascript">
//ajax删除
$('.subs').click(function(){
    $url = $(this).attr('name');
    $.ajax({
        type : 'GET',
        url : $url,
        dataType : "json",
        success : function(data){
            if(data.status == 1){
                alert(data.info);
                location.href = data.url;
            }else{
                alert(data.info);
            }
        }
    });
})
</script>


<div id="footer">
共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。</div>
</body>
</html>