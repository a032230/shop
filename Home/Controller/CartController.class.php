<?php  
namespace Home\Controller;
use Think\Controller;

/*--------------------------
|         购物车控制器      |
--------------------------*/
class CartController extends Controller{

	//加入购物车
	public function add()
	{
		if(IS_POST){
			$cartModel = D('cart');
			// p($_POST);die;
			//接收并验证表单
			if($cartModel -> create(I('post.'),1))
			{
				if($cartModel -> add())
				{
					$this -> success('添加成功',U('lst'));
					exit;
				}
			}
			//添加失败获取错误信息
			$this -> error('添加失败：'. $this -> error($cartModel->getError()));
		}
	}

	//购物车列表
	public function lst()
	{
		
		$model = D('cart');
		$data = $model -> cartList();
		// dump($data);die;
		// echo $model -> getLastSql();die;
		//配置页面变量
    	$this -> assign(array(
    		'_page_title' => '购物车',
    		'_page_keywords' => '购物车',
    		'_page_description' => '购物车',
    		'data' => $data,
    	));
		$this -> display();
	}

}