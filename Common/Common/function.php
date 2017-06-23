<?php  

// 格式化输出
function p($var)
{
	echo '<pre>';
	print_r($var);
	echo '</pre>';
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