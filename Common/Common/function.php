<?php  

// 格式化输出
function p($var)
{
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}

/**
 * [makeAlipayBtn 生成支付宝支付按钮
 * @param  [type] $orderId [订单id]
 * @param  string $btnName [按钮名称]
 * @return [type]          [description]
 */
function makeAlipayBtn($orderId,$btnName = '去支付宝支付')
{
	return require("./alipay/alipayapi.php");
}



/**
 * [showImage 前台页面显示图片函数
 * @param  [string] $url    [数据库取出的图片路径]
 * @param  string $width  [图片的宽]
 * @param  string $height [图片的高
 * @return [type]         [description]
 */
function showImage($url,$width ='' ,$height = '')
{
	$ic = C('IMAGE_CONFIG');
	if($width)
		$width = "width='$width'";
	if($height)
		$height = "height ='$height'";
	echo "<img $width $height src='$ic[viewPath]$url' />";
}

/**
 * [buildSelect 生成select 下拉框
 * @param  [type] $tableName     [表名]
 * @param  [type] $selectName    [select表单中的name值]
 * @param  [type] $idFieldName   [option的value值]
 * @param  [type] $textFieldName [option的文本值]
 * @param  string $selectedValue [根据需求判断是否需要selected选中]
 * @return [type]                [description]
 */
function buildSelect($tableName,$selectName,$idFieldName,$textFieldName,$selectedValue = '')
{
	$model = D($tableName);
	$list = $model -> getField("$idFieldName,$textFieldName");
	$select = "<select name='$selectName' > <option value=''>请选择</option>";
		foreach($list as $k => $v)
		{
			$id = $k;
			$text = $v;
			if($selectedValue && $selectedValue == $id)
				$selected = 'selected';
			else
				$selected = '';

			$select .= "<option $selected value='$id'>$text</option>";
		}
	$select .= "</select>";

	echo $select;
}

/**
 * [delImage 循环删除图片函数
 * @param  array  $arr [传递进来的二维数组]
 * @return [type]      [description]
 */
function delImage($arr = array())
{
	$ic = C('IMAGE_CONFIG');
	foreach($arr as $k => $v)
	{
		unlink($ic['rootPath'] . $v);
	}
}

/**
 * 上传图片并生成缩略图
 * 用法：
 * $ret = uploadOne('logo', 'Goods', array(
			array(600, 600),
			array(300, 300),
			array(100, 100),
		));
	返回值：
	if($ret['ok'] == 1)
		{
			$ret['images'][0];   // 原图地址
			$ret['images'][1];   // 第一个缩略图地址
			$ret['images'][2];   // 第二个缩略图地址
			$ret['images'][3];   // 第三个缩略图地址
		}
		else 
		{
			$this->error = $ret['error'];
			return FALSE;
		}
 *
 */
function uploadOne($imgName, $dirName, $thumb = array())
{
	// 上传LOGO
		$ic = C('IMAGE_CONFIG');
		$upload = new \Think\Upload(array(
			'rootPath' => $ic['rootPath'],
			'maxSize' => $ic['maxSize'],
			'exts' => $ic['exts'],
		));// 实例化上传类
		$upload->savePath = $dirName . '/'; // 图片二级目录的名称
		// 上传文件 
		// 上传时指定一个要上传的图片的名称，否则会把表单中所有的图片都处理，之后再想其他图片时就再找不到图片了
		$info   =   $upload->upload(array($imgName=>$_FILES[$imgName]));
		if(!$info)
		{
			return array(
				'ok' => 0,
				'error' => $upload->getError(),
			);
		}
		else
		{
			$ret['ok'] = 1;
		    $ret['images'][0] = $logoName = $info[$imgName]['savepath'] . $info[$imgName]['savename'];
		    // 判断是否生成缩略图
		    if($thumb)
		    {
		    	$image = new \Think\Image();
		    	// 循环生成缩略图
		    	foreach ($thumb as $k => $v)
		    	{
		    		$ret['images'][$k+1] = $info[$imgName]['savepath'] . 'thumb_'.$k.'_' .$info[$imgName]['savename'];
		    		// 打开要处理的图片
				    $image->open($ic['rootPath'].$logoName);
				    $image->thumb($v[0], $v[1])->save($ic['rootPath'].$ret['images'][$k+1]);
		    	}
		    }
		    return $ret;
		}
}




//有选择性过滤Xss -> 性能低，尽量少用
function removeXss($data)
{
	require_once './HtmlPurifier/HTMLPurifier.auto.php';
	$_clean_xss_config = HTMLPurifier_Config::createDefault();
	$_clean_xss_config->set('Core.Encoding', 'UTF-8');
	//设置保留的标签
	$_clean_xss_config->set('HTML.Allowed','div,b,strong,i,em,a[href|title],ul,ol,li,p,br,span[style],img[width|height|alt|src]');
	$_clean_xss_config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
	$_clean_xss_config->set('HTML.TargetBlank', TRUE);
	$_clean_xss_obj = new HTMLPurifier($_clean_xss_config);
	//执行过滤
	return $_clean_xss_obj->purify($data);
}