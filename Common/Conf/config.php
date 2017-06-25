<?php
return array(
	//'配置项'=>'配置值'
    'DB_TYPE' =>  'mysql',  
    'DB_DSN'    => 'mysql:host=localhost;dbname=shop;charset=utf8',
	'DB_USER' => 'root',
	'DB_PWD'  => '',
	'DB_PROT' => '3306',
	'DEFAULT_FILTER'        =>  'trim,htmlspecialchars', // 默认参数过滤方法 用于I函数...
	// 'DB_PREFIX' => ''
	

	/*********图片相关配置***********/
	'IMAGE_CONFIG' => array(
		'maxSize' => 1024*1024,//限制大小为1M
		'exts' => array('jpg','jpeg','png','gif'), //限制文件类型
		'rootPath' => './Public/Upload/', //文件保存的路径 ->相对与index.php
		'viewPath' => '/Public/Upload/', //前端显示图片的路径 ->相对于网站
	),
);