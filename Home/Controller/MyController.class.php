<?php  
namespace Home\Controller;
use Think\Controller;
/*--------------------------
|      我的订单-控制器      |
--------------------------*/
class MyController extends NavController{


	public function __construct()
	{	
		//调用父类 
		parent::__construct();
		//判断是否登陆
		$memberId = session('m_id');
		if(!$memberId)
		{
			session('returnUrl',U('My/'.ACTION_NAME));
			redirect(U('Member/login'));
		}
	}

	public function order()
	{	
		$orderModel = D('Admin/Orders');

		//返回数据，分页
		$data = $orderModel -> search();
		$this -> assign($data);
		// p($data);
		//配置页面变量
    	$this -> assign(array(
    		'_show_nav' => 0,
    		'_page_title' => '个人中心-我的订单',
    	));
		$this -> display();
	}

}