<?php  
namespace Admin\Model;
use Think\Model;

/*--------------------------
|         订单模型          |
--------------------------*/
class OrdersModel extends Model{
	//定义添加时允许接收的字段
	protected $insertFields = "shr_name,shr_tel,shr_province,shr_city,shr_area,shr_address";

	//验证表单
	protected $_validate = array(
		array('shr_name','require','收货人不能为空',1),
		array('shr_tel','require','收货人电话不能为空',1),
		array('shr_province','require','收货人所在省份不能为空',1),
		array('shr_city','require','收货人所在城市不能为空',1),
		array('shr_area','require','收货人所在地区不能为空',1),
		array('shr_address','require','收货人详细地址不能为空',1),
		);

	//自动完成
	protected $_auto = array(
		array('addtime','time',1,'function'),
		);


	// 添加之前
	protected function _before_insert(&$data,&$option)
	{
		$memberId = session('m_id');
		/**************下单前检查**************/
		//是否登陆
		if(!$memberId){
			$this -> error = '请先登陆！';
			return false;
		}
		//购物车是否有商品
		$cartModel = D('Home/cart');
		//将数据保存到option,添加后直接调用，避免再次调用该方法-->提升性能
		$option['goods'] = $ret = $cartModel ->cartList();
		if(!$ret){
			$this -> error = '请先选择商品！';
			return false;
		}
		//读库存前加锁，防止高并发下单出错
		//将锁赋给属性,一直保存到下单完成才释放锁，局部变量在方法befor_insert执行完就释放
		$this -> fp = fopen('./order.lock');
		flock($this -> fp, LOCK_EX);


		//检查库存量是否足够并计算购物车总价
		$total_price = 0;
		$gnModel = M('goods_number');
		foreach($ret as $k => $v)
		{
			//检查库存量
			$gn = $gnModel -> field('goods_number') 
					 -> where(array(
					 		'goods_id' => array('eq',$v['goods_id']),
					 		'goods_attr_id' => array('eq',$v['goods_attr_id']),
					 	))
					 ->find();
			if($gn['goods_number'] < $v['goods_number'])
			{
				$this -> error = '下单失败,商品:' . $v['goods_name'] . '的库存量不足';
				return false;
			}
			//统计总价
			$total_price += $v[goods_number] * $v['price'];
		}

		//把其他信息补到订单
		$data['member_id'] = $memberId;
		$data['total_price'] = $total_price;

		//为了确保三张表[订单基本信息表，订单商品表，商品库存量表]的操作都能成功
		//在添加订单基本信息时开启事务
		$this -> startTrans();
	}

	//订单生成后
	protected function _after_insert($data,$option)
	{
		//把购物车的商品写入到订单商品表
		
		//获得添加前在option保存购物车的商品数据
		$goods = $option['goods'];		
		$ogModel = M('order_goods');
		$gnModel = M('goods_number');
		$cartModel = D('Home/cart');
		foreach($goods as $k => $v)
		{
			$ok = $ogModel -> add(array(
					'order_id' => $data['id'],
					'goods_id' => $v['goods_id'],
					'goods_attr_id' => $v['goods_attr_id'],
					'goods_number' => $v['goods_number'],
					'price' => $v['price'],
				));
			//操作失败回滚事务
			if(!$ok){
				$this -> rollback();
				return false;
			}

			//在库存量表中减少对应商品的库存
			$ok = $gnModel -> where(array(
					'goods_id' => array('eq',$v['goods_id']),
					'goods_attr_id' => array('eq',$v['goods_attr_id']),
				))-> setDec('goods_number',$v['goods_number']);

			//操作失败回滚事务
			if($ok === FALSE){
				$this -> rollback();
				return false;
			}
			//清空该会员购物车中对应的商品	
			 $cartModel -> clear($v['goods_id'],$v['goods_attr_id']);
		}

		//所以操作都成功提交事务
		$this -> commit();
		//释放锁
		flock($this -> fp,LOCK_UN);
		fclose($this -> fp);
	}

	/**
	 * [setPaid description 设置已支付状态
	 * @param [type] $orderId [订单id]
	 */
	public function setPaid($orderId)
	{
		//更新该订单的支付状态
		$this -> where(array('id' => array('eq',$orderId))) 
		      -> save(array('pay_status'=>'是','pay_time'=>time()));

		//更新会员积分[1元=1积分]
		$tp = $this -> field('total_price,member_id') -> find($orderId);
		$memberModel = M('member');
		$memberModel -> where(array('id'=> array('eq',$tp['member_id']))) 
		             -> setInc('jifen',$tp['total_price']);
	}
}