<?php
namespace Admin\Model;
use Think\Model;

/*------------------------
 |       分类模型         |
 ------------------------*/
class CategoryModel extends Model{

	//定义添加时允许接收的字段
	protected $insertFields = 'parent_id,cat_name,is_floor';

	//定义修改时允许接收的字段
	protected $updateFields = 'id,parent_id,cat_name,is_floor';

	//定义验证规则
	protected $_validate = array(
		array('cat_name', 'require','分类不能为空',1),
	);


	/**
	 * [getChildren  获取该分类id下的所有子类id
	 * @param  [type] $catid [传递进来的分类id]
	 * @return [array]        [返回所有该分类下的所有子类id]
	 */
	public function getChildren($catid)
	{
		$data = $this -> select();

		return $this -> _getChildren($data,$catid,TRUE);
	}

	/**
	 * [_getChildren 递归查找所有子类
	 * @param  [array]  $data     [分类数据]
	 * @param  [int]  $catid    [分类id]
	 * @param  boolean $is_clean [是否清空$ret]
	 * @return [$ret]  array     [该分类id下的所有子类id]
	 */
	private function _getChildren($data,$catid,$is_clean = FALSE)
	{
		static $ret = array(); //定义静态数组用于存放子类id

		//首次调用，先清空数组，防止多次调用该方法时保存上次继承的数据
		if($is_clean)
		{
			$ret = array();
		}

		//遍历分类数据获取所有子类id
		foreach($data as $v){
			if($v['parent_id'] == $catid){
				$ret[] = $v['id'];

				//再查找该$v['id']下的子类
				$this -> _getChildren($data,$v['id']);
			}
		}
		return $ret;
	}

	/**
	 * [getTree 打印树状结构数据
	 * @return [array] [标记好分类等级的数据]
	 */
	public  function getTree()
	{
		$data = $this -> select();
		return $this -> _getTree($data);
	}


	/**
	 * [_getTree 为所有分类标记等级
	 * @param  [type]  $data      [分类数据]
	 * @param  integer $parent_id [父级id]
	 * @param  integer $level     [分类等级]
	 * @return [array] $ret       [所有标记好等级的分类数据]
	 */
	private function _getTree($data,$parent_id = 0 ,$level = 0)
	{
		static $ret = array();

		foreach($data as $v){
			//查找出所有的顶级id
			if($v['parent_id'] == $parent_id){
				//标记该分类等级
				$v['level'] = $level;
				$ret[] = $v;

				$this -> _getTree($data,$v['id'],$level + 1);
			}
		}

		return $ret;
	}

	protected function _before_delete(&$option)
	{
		// p($option);die;
		//获取该分类下所有子类id
		$childrens = $this -> getChildren($option['where']['id']);
		$childrens[] = $option['where']['id']; //将分类id压入数组
		//引用传值修改TP底层option,重组$option['where']['id']将分类id和其子类id一起传递回delete方法
		$option['where']['id'] = array(0 => 'IN',1 => implode(',', $childrens));
	}


	/**********************前台专用代码********************/

	public function getNavData()
	{	
		//判断数据是否缓存
		$cateData = S('cateData');
		if(!$cateData)
		{

			$all = $this -> select(); //取出所有分类

			$ret = array();
			//筛选出前三级
			foreach($all as $k => $v)
			{	
				//顶级
				if($v['parent_id'] == 0)
				{	//二级
					foreach($all as $k1 => $v1)
					{
						if($v1['parent_id'] == $v['id'])
						{
							foreach($all as $k2 => $v2)
							{
								if($v2['parent_id'] == $v1['id'])
								{
									$v1['children'][] = $v2;
								}
							}

							$v['children'][] = $v1;
						}
					}

					$ret[] = $v;
				}
			}
			/**
			 * 此处用到了三个foreach，当数据量较大的情况下，
			 * 会降低网站性能，所以此处当获得数据的同时把数据缓存起来，
			 * 时间为了一天，这样取数据的话一天只执行一次循环就可以了
			 */
			S('cateData',$ret,86400);
			return $ret;
		}
		else
		{
			return $cateData;
		}

		
	}



	/****************************前台方法*************************/

	/**
	 * [floorData  获取前台首页楼层中的数据
	 * @return [type] [description]
	 */
	public function floorData()
	{
		$floorData = S('floorData');
		//数据缓存过则直接返回
		if($floorData)
		{
			return $floorData;
		}
		else
		{
			//取出推荐到楼层的顶级分类
			$ret = $this -> where(array(
									'parent_id' => array('eq',0),
									'is_floor' => array('eq','是')
									)) 
			             ->select();

			$goodsModel = D('Admin/goods');
			foreach($ret as $k => $v)
			{	
				//获取该楼层中所有分类下商品的品牌
				$goods_id = $goodsModel -> getGoodsIdByCatId($v['id']);
				$ret[$k]['brand'] = $goodsModel -> alias('a')
												-> join('LEFT JOIN __BRAND__ b ON a.brand_id=b.id')
												-> field('DISTINCT b.*')
												-> where(array(
													'a.id' => array('IN',$goods_id),
													'a.brand_id' => array('neq',0),
													))
												-> select();
				//获取该顶级分类下未被推荐的二级分类
				$ret[$k]['subCat']=$this->where(array('parent_id'=>array('eq',$v['id']),'is_floor'=>array('eq','否')))->select();

				//获取该顶级分类下被推荐的二级分类
				$ret[$k]['recSubCat']=$this->where(array('parent_id'=>array('eq',$v['id']),'is_floor'=>array('eq','是')))->select();

				//获取被推荐的二级分类下的所有商品
				foreach($ret[$k]['recSubCat'] as $k1 => &$v1)
				{
					$gids = $goodsModel -> getGoodsIdByCatId($v1['id']);
					//引用传值$v1,将获得的商品放回数组中的goods字段
					$v1['goods'] = $goodsModel -> field('id,shop_price,mid_logo,goods_name')
							    -> where(array(
							    	'is_on_sale' => array('eq','是'),
							    	'is_floor' => array('eq', '是'),
							    	'id' => array('IN',$gids),	
							    )) 
							    -> order("sort_num ASC")
							    -> limit(8)
							    -> select();
				}
			}
			//将获得的数据缓存并返回
			S('floorData',$ret,86400);
			return $ret;
		}
	}

	/**
	 * [parentPath 取出一个分类的所有上级分类[制作面包屑导航]
	 * @param  [type] $catId [description]
	 * @return [type]        [description]
	 */
	public function parentPath($catId)
	{
		static $ret = array();
		//取出当前分类的信息
		$info = $this -> field("id,cat_name,parent_id") -> find($catId);
		$ret[] = $info;
		//取出该类所有的上级分类
		if($info['parent_id'] != 0)
		{
			$this -> parentPath($info['parent_id']);
		}

		return $ret;
	}


	/**
	 * [searchConditionByCatId 根据分类id显示搜索数据
	 * @param  [type] $catId [description]
	 * @return [type]        [description]
	 */
	public function searchConditionByGoodsId($goodsId)
	{	
		//返回搜索数据的数组
		$ret = array();

		//获得该分类下的所有商品id
		$goodsModel = D('Admin/goods');
		// $goodsId = $goodsModel -> getGoodsIdByCatId($catId);	
		// echo $goodsModel -> getLastSql();
		// p($goodsId);	
		/**************品牌**************/
		$ret['brand'] = $goodsModel -> alias('a')
					-> field('DISTINCT a.brand_id,b.brand_name')
					-> join("LEFT JOIN __BRAND__ b ON a.brand_id=b.id")
					-> where(array(
						'a.id' => array('IN',$goodsId),
						'a.brand_id' => array('neq',0),
					))
					-> select();


		/*****************搜索的价格区间******************/
		$sectionCount = 6;//默认分几段
		//取出这个分类下最大和最小的商品价格
		$priceInfo = $goodsModel -> field('MAX(shop_price) max_price,MIN(shop_price) min_price') 
		                            -> where(array('id'=>array('IN',$goodsId))) 
		                            -> find();
		//最大和最小价格的差
		$priceSection = $priceInfo['max_price'] - $priceInfo['min_price'];
		//分类下的商品数量
		$goodsCount = count($goodsId);

		//只有商品数量大于这些时才分段
		if($goodsCount > 1)
		{
			//根据最大价和最小价的差计算分几段
			if($priceSection < 100)
				$sectionCount = 2;
			elseif($priceSection < 1000)
				$sectionCount = 4;
			elseif($priceSection < 10000)
				$sectionCount = 6;
			else
				$sectionCount = 7;

			//根据这些段数以价格分段
			$pricePerSection = ceil($priceSection / $sectionCount); //每段的价格范围
			// p($pricePerSection /1000 * 1000 -1);
			//存放最终分段的价格
			$price = array();
			$firstPrice = 0;//第一个价格区间的开始价格
			for($i = 0 ;$i < $sectionCount; $i++)
			{
				//每段结束的价格
				$tmpEnd = $firstPrice + $pricePerSection ;
				//结束价格取整
				$tmpEnd = (ceil($tmpEnd / 1000) * 1000 -1);
				$price[] = $firstPrice . '-' . $tmpEnd;

				//计算下一个价格段的开始价格
				$firstPrice = $tmpEnd + 1;

			}
			$ret['price'] = $price;
		}

		/*******************商品属性****************/
		$gaModel = M('goods_attr');
		$_gaData = $gaModel -> alias('a')
				 -> field('DISTINCT a.attr_id,a.attr_value,b.attr_name')
				 -> join('LEFT JOIN __ATTR__ b ON a.attr_id=b.id')
				 -> where(array(
				 		'a.goods_id'=> array('IN',$goodsId),
				 		'a.attr_value' => array('neq', ''),
				 		))
				 -> select();
		//处理这个属性数组，将属性名相同的放到一起
		$gaData = array();
		foreach($_gaData as $k => $v)
		{	
			$gaData[$v['attr_name']][] =$v;

		}	
		// array_unique($gaData['颜色']);
		// p($gaData);
		$ret['gaData'] = $gaData;


		return $ret;
	}
}