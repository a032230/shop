<?php  
namespace Home\Controller;
use Think\Controller;
/*--------------------------
|         订单控制器        |
--------------------------*/
class OrdersController extends Controller{
	//订单页
	public function add()
	{	
		//如果会员没有登陆就跳到登陆页，登陆成功后跳转回来
		$memberId = session('m_id');
		if(!$memberId){
			//登陆成功后要跳回的地址存session
			session('returnUrl',U('Orders/add'));
			$this -> redirect('Member/login');
		}

		//执行添加
		if(IS_POST){
			$model = D('Admin/orders');
			if($model -> create(I('post.'),1)){
				if($order_id = $model -> add()){
					$this -> success('下单成功！',U('order_success',array('order_id'=> $order_id)));
					exit;
				}
			}
			$this -> error($model -> getError());
		}
		$cartModel = D('cart');
		$data = $cartModel -> cartList();
		//配置页面信息
    	$this -> assign(array(
    		'_page_title' => '订单确认页',
    		'_page_keywords' => '订单确认页',
    		'_page_description' => '订单确认页',
    		'data' => $data,
    	));
		$this -> display();
	}

	//下单成功页
	public function order_success()
	{


		$this -> display();
	}
}