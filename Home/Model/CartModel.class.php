<?php  
namespace Home\Model;
use Think\Model;

/*--------------------------
|          购物车模型       |
--------------------------*/
class CartModel extends Model{

	//定义添加时允许接收的字段
	protected $insertFields = "goods_id,goods_attr_id,goods_number";

	//定义表单定义规则
	protected $_validate = array(
		array('goods_id','require','请先选择商品',1),
		// array('goods_attr_id','require','请先选择商品属性！',1),
		array('goods_number','chkGoodsNumber','库存量不足！',1,'callback')
	);

	//检查库存量
	public function chkGoodsNumber($goodsNumber)
	{
		$gnModel = M('goods_number');
		$gaid = I('post.goods_attr_id');
		sort($gaid,SORT_NUMERIC);
		//即使是空也要返回空字符串，用来判断没有属性值的商品；
		$gaid = (string)implode(',', $gaid); 
		//获得该商品属性的库存量
		$gn = $gnModel ->field('goods_number') -> where(array(
							'goods_id' => array('eq',I('post.goods_id')),
							'goods_attr_id' => array('eq',$gaid),
						)) -> find();
		//返回库存量对比结果的bool
		return ($gn['goods_number'] >= $goodsNumber);
	}


	//重写父类add方法,判断如果登陆存入数据库，否则存cookie
	public function add()
	{
		$memberId = session('m_id');
		//将商品属性id升序排列并转换成字符串
		sort($this -> goods_attr_id);
		$this -> goods_attr_id = (string)implode(',', $this -> goods_attr_id);
		if($memberId){
			
				
			/**
			 * 注： find()方法是从数据库取出数据并保存再模型中[会覆盖原数据]
			 * 所以先把原先模型中的数据存起来,
			 * 注2：find()方法如果没有取到数据不会覆盖模型
			 */
				// echo $this -> goods_id . '-';
				// echo $this -> goods_attr_id . '-';
				// echo $this -> goods_number . '<br>';
			//将模型中的原数据保存，否则调用了find方法后会被find方法的数据覆盖模型
			$goods_number = $this -> goods_number;
			//检查数据库是否有该商品
			$has = $this -> field('id')
						 -> where(array(
						 	'goods_id' => $this -> goods_id,
							'goods_attr_id' =>  $this -> goods_attr_id,
							'member_id' => $memberId,
						 ))
						 -> find();
				// echo $this -> goods_id . '-';
				// echo $this -> goods_attr_id . '-';
				// echo $this -> goods_number;die;
			if($has){
				//如果该商品已经存在，直接修改商品数量
				$this -> where(array('id'=>array('eq',$has['id']))) -> setInc('goods_number',$goods_number);
				// echo $this -> getLastSql();die;
			}else{
				//否则调用父类add方法直接执行添加
				parent::add(array(
					'goods_id' => $this -> goods_id,
					'goods_attr_id' =>  $this -> goods_attr_id,
					'goods_number' => $this -> goods_number,
					'member_id' => $memberId,
					));
			}
		}else{
			//未登陆存入cookie
			//取出cookie中的购物车数组
			$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
			//拼接商品数据放入数组
			$key = $this -> goods_id . '-' . $this -> goods_attr_id;
			//检查数组中是否有该商品
			if(isset($cart[$key])){
				$cart[$key] += $this -> goods_number;
			}else{
				$cart[$key] = $this -> goods_number;
			}
			//再存回cookie
			setcookie('cart',serialize($cart),time() + 86400 * 30, '/');
		}

		return true;
	}

	/**
	 * [moveDataToDb  在会员登陆后将cookie中的数据写入数据库
	 * @return [type] [description]
	 */
	public function moveDataToDb()
	{
		$memberId = session('m_id');
		// p($memberId);die;
		//判断是否登陆
		if($memberId){
			//取出cookie中的购物车数组
			$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
			// p($cart);die;
			//循环购物车中每件商品
			foreach($cart as $k => $v)
			{
				$key = explode('-', $k);
				//检查数据库是否有该商品
				$has = $this -> field('id')
							 -> where(array(
							 	'goods_id' => $key[0],
								'goods_attr_id' => $key[1],
								'member_id' => $memberId,
							 ))
							 -> find();

				if($has){
					//如果该商品已经存在，直接修改商品数量
					$this -> where(array('id'=>array('eq',$has['id']))) -> setInc('goods_number',$v);
					// echo $this -> getLastSql();die;
				}else{
					//否则调用父类add方法直接执行添加
					parent::add(array(
						'goods_id' => $key[0],
						'goods_attr_id' =>  $key[1],
						'goods_number' => $v,
						'member_id' => $memberId,
						));
				}
			}
			//清空cookie
			setcookie('cart','',time()-1,'/');
		}
	}

	//获取购物车中商品的详细信息
	public function cartList()
	{
		/**************取出购物车的商品id***************/
		$memberId = session('m_id');
		if($memberId){
			$data = $this -> where(array("member_id"=> array('eq',$memberId)))->select();
		}else{
			//取出保存在cookie中的购物车数据
			$_data = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
			$data = array();
			foreach($_data as $k => $v)
			{
				$_k = explode('-', $k);
				//转换成二维数组,和数据库保持一致
				$data[] = array(
						'goods_id' => $_k[0],
						'goods_attr_id' => $_k[1],
						'goods_number' => $v,
					);
			}
		}

		/****************根据商品id取出商品详细信息**************/
		$gModel = D('Admin/goods');
		$gaModel = M('goods_attr');
		foreach($data as $k => &$v)
		{
			//取出商品名称和logo
			$info = $gModel ->field('goods_name,sm_logo') -> find($v['goods_id']);

			//再返回该数组[引用传值]
			$v['goods_name'] = $info['goods_name'];
			$v['sm_logo'] = $info['sm_logo'];

			//获得该商品的实际价格
			$v['price'] = $gModel -> getMemberPrice($v['goods_id']);

			//根据商品属性id获得商品属性名和属性值[属性名:属性值]
			if($v['goods_attr_id'])
			{
				$v['gaData'] = $gaModel -> alias('a')
										-> field('a.attr_value,b.attr_name')
										-> join("LEFT JOIN __ATTR__ b ON a.attr_id = b.id")
										-> where(array('a.id' => array('IN',$v['goods_attr_id'])))
										-> select();
			}
		}
		return $data;
	}

	//清空购物车
	public function clear($goods_id,$goods_attr_id)
	{
		$this -> where(array(
						'member_id'=> array('eq',session('m_id')),
						'goods_id' => array('eq',$goods_id),
						'goods_attr_id' => array('eq',$goods_attr_id),
						)) 
		      -> delete();
	}
}