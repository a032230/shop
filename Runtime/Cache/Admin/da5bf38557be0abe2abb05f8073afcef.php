<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ECSHOP 管理中心 - 商品列表 </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/Public/Admin/styles/general.css" rel="stylesheet" type="text/css" />
<link href="/Public/Admin/styles/main.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>
    <span class="action-span"><a href="__GROUP__/Goods/goodsAdd">添加新商品</a></span>
    <span class="action-span1"><a href="__GROUP__">ECSHOP 管理中心</a></span>
    <span id="search_id" class="action-span1"> - 商品列表 </span>
    <div style="clear:both"></div>
</h1>
<div class="form-div">
    <form action="/index.php/Admin/Goods/lst" name="searchForm" method="get">
        <p>商品名称：<input type="text" name="gn" value="<?=I('get.gn')?>" /></p>
    
        <p>
            价　　格：
            从 <input type="text" name="fp" value="<?=I('get.fp')?>" />
            到 <input type="text" name="tp" value="<?=I('get.tp')?>" />
        </p>
        
        <p>
            是否上架：
            <?php $ios = I('get.ios')?>
            <input type="radio" name="ios" value="" <?php echo $ios == '' ? 'checked' : '' ?> />全部
            <input type="radio" name="ios" value="是" <?php echo $ios == '是' ? 'checked' : '' ?>  />上架
            <input type="radio" name="ios" value="否" <?php echo $ios == '否' ? 'checked' : '' ?>  />下架
        </p>

        <p>
            添加时间:
            从 <input type="text" name="fa" value="<?=I('get.fa')?>"  />
            到 <input type="text" name="ta" value="<?=I('get.ta')?>"  />
        </p>

        <input type="submit" value=" 搜索 " class="button" />
    </form>
</div>

<!-- 商品列表 -->
<form method="post" action="/index.php/Admin/Goods/lst" name="listForm" onsubmit="">
    <div class="list-div" id="listDiv">
        <table cellpadding="3" cellspacing="1">
            <tr>
                <th>编号</th>
                <th>商品名称</th>
                <th>市场价格</th>
                <th>商品价格</th>
                <th>商品logo</th>
                <th>是否上架</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            <?php if(is_array($data)): foreach($data as $key=>$val): ?><tr>
                <td align="center"><?php echo ($val["id"]); ?></td>
                <td align="center" class="first-cell"><span><?php echo ($val["goods_name"]); ?></span></td>
                <td align="center"><span onclick=""><?php echo ($val["market_price"]); ?></span></td>
                <td align="center"><span><?php echo ($val["shop_price"]); ?></span></td>
                <td align="center"><span><img src="<?php echo ($val["sm_logo"]); ?>" alt="商品logo" /></span></td>
                <td align="center"><span><?php echo ($val["is_on_sale"]); ?></span></td>
                <td align="center"><span><?=date('Y-m-d',$val['addtime'])?></span></td>
                <td align="center">
                <a href="<?php echo U('edit',array('id' => $val['id']));?>" title="编辑"><img src="/Public/Admin/Images/icon_edit.gif" width="16" height="16" border="0" /></a>
                <a href="<?php echo U('del',array('id' => $val['id']));?>" title="回收站"><img src="/Public/Admin/Images/icon_trash.gif" width="16" height="16" border="0" /></a>
                </td>
            </tr><?php endforeach; endif; ?>
        </table>

    <!-- 分页开始 -->
        <table id="page-table" cellspacing="0">
            <tr>
                <td width="80%">&nbsp;</td>
                <td align="center" nowrap="true">
                    <?php echo ($showPage); ?>
                </td>
            </tr>
        </table>
    <!-- 分页结束 -->
    </div>
</form>

<div id="footer">
共执行 7 个查询，用时 0.028849 秒，Gzip 已禁用，内存占用 3.219 MB<br />
版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。</div>
</body>
</html>