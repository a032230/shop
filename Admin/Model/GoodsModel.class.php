<?php  
namespace Admin\Model;
use Think\Model;

/*--------------------------
|         商品模型          |
--------------------------*/
class GoodsModel extends Model{

	//新增时允许接收的字段
	protected $insertFields = "cat_id,type_id,brand_id,goods_name,shop_price,market_price,is_on_sale,goods_desc,promote_price,promote_start_date,promote_end_date,is_hot,is_new,is_best,sort_num,is_floor";

	//修改时允许接收的字段
	protected $updateFields = "id,cat_id,type_id,brand_id,goods_name,shop_price,market_price,is_on_sale,goods_desc,promote_price,promote_start_date,promote_end_date,is_hot,is_new,is_best,sort_num,is_floor";

	//定义表单验证规则
	protected $_validate = array(
		array('goods_name','require','商品名称不能为空',1),
		array('market_price','currency','市场价格必须为货币类型',1),
		array('shop_price','currency','本店价格必须为货币类型',1),
		array('brand_id','require','商品品牌不能为空',1),
		array('cat_id','require','必须选择一个主分类',1),
	);

	//自动完成
	protected $_auto = array(
		array('addtime','time',1,'function'),
	);

	/*-------------------------------分割线---------------------------------*/
	//添加之前执行操作
	protected function _before_insert(&$data,$option)
	{	
		//判断是否上传图片
		if($_FILES['logo']['error'] == 0){

			// $upload = new \Think\Upload(); 
			// $upload -> maxSize = 1024 *1024; //限制上传文件的大小为1M
			// $upload -> exts = array('jpeg','gif','png','jpg'); //设置允许上传的文件类型
			// $upload -> rootPath = "./Public/Upload/";//文件上传的根目录
			// $upload -> savePath = 'Goods/'; //文件上传的子目录

			// //执行上传
			// $info = $upload -> upload();

			// if(!$info){
			// 	//上传失败获取错误信息并保持到模型
			// 	$this -> error = $upload -> getError();
			// 	return false;
			// }else{
			// 	/*******************生成缩略图******************/
			// 	// 拼接原图路径
			// 	$logo = $info['logo']['savepath'] . $info['logo']['savename'];
			// 	//拼接缩略图的路径和名称
			// 	$mbiglogo =  $info['logo']['savepath'] .'mbig_'. $info['logo']['savename'];
			// 	$biglogo =  $info['logo']['savepath'] .'big_'. $info['logo']['savename'];
			// 	$midlogo =  $info['logo']['savepath'] .'mid_'. $info['logo']['savename'];
			// 	$smlogo =  $info['logo']['savepath'] .'sm_'. $info['logo']['savename'];
			// 	//实例化图片类
			// 	$img = new \Think\Image();
			// 	//打开原图
			// 	$img -> open('./Public/Upload/' . $logo);

			// 	//生成缩略图
			// 	$img -> thumb(700,700) -> save('./Public/Upload/' . $mbiglogo); //特大图
			// 	$img -> thumb(350,350) -> save('./Public/Upload/' . $biglogo); //大图
			// 	$img -> thumb(130,130) -> save('./Public/Upload/' . $midlogo); //中图
			// 	$img -> thumb(50,50) -> save('./Public/Upload/' . $smlogo); //小图

			// 	/***********将图片路径存入表单**************/
			// 	$data['logo'] =  $logo;
			// 	$data['mbig_logo'] = $mbiglogo;
			// 	$data['big_logo'] = $biglogo;
			// 	$data['mid_logo'] =  $midlogo;
			// 	$data['sm_logo'] =  $smlogo;
			// }
			
			// 优化上传和缩略
			$ret = uploadOne('logo','Goods',array(
				array(700,700),
				array(350,350),
				array(130,130),
				array(50,50),
			));
			//将图片路径存放入表单，
			$data['logo'] =  $ret['images'][0]; 
			$data['mbig_logo'] = $ret['images'][1]; 
			$data['big_logo'] = $ret['images'][2];  
			$data['mid_logo'] =  $ret['images'][3]; 
			$data['sm_logo'] =  $ret['images'][4];  
		}

		//选择性过滤Xss
		$data['goods_desc'] = removeXss($_POST['goods_desc']);
	}

		/*-------------------------------分割线---------------------------------*/
		/**
		 * getGoodsIdByCatId 取出分类下所以的商品ID[分类及其子类和扩展分类]
		 * @param  [int] $catId [被搜索的分类]
		 * @return [array] $ids [该分类及其子类和扩展分类的商品id]
		 */
		public function getGoodsIdByCatId($catId)
		{
			//取出该分类所有子类的id
			$cateModel = D('Admin/category');
			$childrens = $cateModel -> getChildren($catId);
			//ba搜索的分类id和其子类id放在一起
			$childrens[] = $catId;

			/*******取出主分类和扩展分类下的商品id*******/
			//去主分类下的商品
			$gid = $this -> field('id') ->where(array('cat_id' => array('IN',$childrens))) -> select();

			//取扩展分类下的商品id
			$gcModel = M('goods_cat');
			//考虑该类作为扩展分类时 该类和其子类都拥有这件商品，所以去掉重复的商品id
			$gid1 = $gcModel -> field('DISTINCT goods_id AS id') -> where(array('cat_id' => array('IN',$childrens)))->select();

			//将两次取出的商品id合并
			//注： 合并时两个数组中有一个为NULL，合并的结果是NULL,坑爹！
			if($gid && $gid1){
				$gids = array_merge($gid,$gid1);
			}elseif($gid1){
				$gids = $gid1;
			}else{
				$gids = $gid;
			}

			//将二维的商品id数组转一维
			$ids = array();
			foreach($gids as $v)
			{	
				//去除两次结果重复的商品id
				if(!in_array($v['id'],$ids)){
					$ids[] = $v['id'];
				}
			}

			return $ids;
		}

	/*-------------------------------分割线---------------------------------*/
	//搜索，分页，排序，获取数据
	public function search($perPage = 10)
	{	

		/*********搜索*********/
		$where = array();
		$where['is_delete'] = '否';

		//商品分类
		$cat_id = I('get.cat_id');
		//搜索商品分类时，该类的所有子类商品也搜索出
		if($cat_id)
		{	
			//取出被搜索的分类及其子类和其作为扩展类下的所以商品id
			$gids = $this -> getGoodsIdByCatId($cat_id);
			$where['a.id'] = array('IN',$gids);
		}

		//商品品牌
		$brand_id = I('get.brand_id');
		if($brand_id)
			$where['a.brand_id'] = array('eq',$brand_id);

		//商品名
		$gn = I('get.gn');
		if($gn)
			$where['a.goods_name'] = array('like',"%$gn%"); //where goods_name like %$gn%;
		//价格
		$fp = I('get.fp');
		$tp = I('get.tp');
		if($fp && $tp)
			$where['a.shop_price'] = array('between',array($fp,$tp));
		else if($fp)
			$where['a.shop_price'] = array('egt',$fp);
		else if($tp)
			$where['a.shop_price'] = array('elt',$tp);

		// 是否上架
		$ios = I('get.ios');
		if($ios)
			$where['is_on_sale'] = array('eq',$ios);

		//添加时间
		$fa = strtotime(I('get.fa'));
		$ta = strtotime(I('get.ta'));
		if($fa && $ta)
			$where['addtime'] = array('between',array($fa,$ta));
		else if($fa)
			$where['addtime'] = array('egt',$fa);
		else if($ta)
			$where['addtime'] = array('elt',$ta);


		/*****排序**********/
		$orderby = 'a.id'; //默认排序字段
		$orderway = 'desc'; //默认排序方式
		$odby = I('get.odby');

		if($odby == 'id_asc')
			$orderway = 'asc';
		elseif($odby == 'a.price_desc')
			$orderby = 'shop_price';
		elseif($odby == 'price_asc')
		{
			$orderby = 'a.shop_price';
			$orderway = 'asc';
		}




		/*******取数据******/
		$data = $this -> field('a.*,b.brand_name,c.cat_name,GROUP_CONCAT(e.cat_name SEPARATOR "<br />") AS ext_name')
					  -> alias('a')
		              -> join("LEFT JOIN __BRAND__ b ON a.brand_id = b.id
		              		   LEFT JOIN __CATEGORY__ c ON a.cat_id = c.id
		              		   LEFT JOIN __GOODS_CAT__ d ON a.id = d.goods_id
		              		   LEFT JOIN __CATEGORY__ e ON d.cat_id = e.id")
		              -> where($where) 
		              -> order("$orderby $orderway")
		              -> limit($pageObj->firstRow . ',' . $pageObj ->listRows)
		              -> group('a.id')
		              -> select();

		   // echo $this -> getLastSql();
		 

		 /*********分页*********/
		$count = $this ->alias('a') -> where($where)->count();//取出总记录数

		//生成分页类对象
		$pageObj = new \Think\Page($count,$perPage);

		//设置样式
		$pageObj -> setConfig('prev' , '上一页');
		$pageObj -> setConfig('next' , '下一页');

		//生成在页面显示的分页字符串
		$pageString = $pageObj -> show();
		//返回分页字符串和数据
		return  array('data' => $data,'page' => $pageString);
	}


	/*-------------------------------分割线---------------------------------*/

	//修改前执行操作
	protected function _before_update(&$data,$option)
	{
		// p($option);die;
		$id = $option['where']['id'];//获取要修改商品的id

		/***********处理logo*************/
		//判断是否重新上传图片
		if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0){
		
				// 优化上传和缩略
				$ret = uploadOne('logo','Goods',array(
					array(700,700),
					array(350,350),
					array(130,130),
					array(50,50),
				));
				if($ret['ok'] === 0)
				{
					$this->error = $ret['error'];
					return FALSE;
				}
				//将图片路径存放入表单，
				$data['logo'] =  $ret['images'][0]; 
				$data['mbig_logo'] = $ret['images'][1]; 
				$data['big_logo'] = $ret['images'][2];  
				$data['mid_logo'] =  $ret['images'][3]; 
				$data['sm_logo'] =  $ret['images'][4];  


				//删除硬盘上存放的未修改之前的图片资源
				$oldlogo = $this -> field('logo,mbig_logo,big_logo,mid_logo,sm_logo') -> find($id);
				delImage($oldlogo);
		}

		/***********处理相册图**************/
		if(isset($_FILES['pic']))
		{
			//将重组相册信息再调用uplodeOne循环添加
			$pics = array();

			//遍历图片，重组图片信息
			foreach($_FILES['pic']['name'] as $k => $v)
			{
				$pics[] = array(
					'name' => $v,
					'type' => $_FILES['pic']['type'][$k],
					'tmp_name' => $_FILES['pic']['tmp_name'][$k],
					'error' => $_FILES['pic']['error'][$k],
					'size' => $_FILES['pic']['size'][$k],

				);

			}

			//把处理好的数组赋回给$_FILES,因为封装的uploadOne是再$_FILES中寻找图片
			$_FILES = $pics;
			// p($_FILES);die;
			$gpModel = M('goods_pic');
			//循环添加
			foreach($pics as $k => $v)
			{
				// 如果图片错误代码为0才执行添加
				if($v['error'] == 0)
				{
					$ret = uploadOne($k,'Pic',array(
								array(650,650),
								array(350,350),
								array(50,50)
							));

					if($ret['ok'] == 1)
					{
						$gpModel -> add(array(
							'pic' => $ret['images'][0],
							'big_pic' => $ret['images'][1],
							'mid_pic' => $ret['images'][2],
							'sm_pic' => $ret['images'][3],
							'goods_id' => $id,
						));
					}
				}
			}

		}
		/***********处理会员价格*************/
		$mp = I('post.member_price');
		//先删除该商品原先的会员价格
		$mpModel = M('member_price');
		$mpModel -> where("goods_id=$id") -> delete();

		//重添post过来的会员价格
		foreach($mp as $k=>$v){
			//防止会员价格输入非数字
			$_v = (float)$v;
			//价格大于0才放入数据库
			if($_v > 0){
				$mpModel -> add(array(
					'level_id' => $k,
					'price' => $_v,
					'goods_id' => $id,
				));
			}
		}

		/*********处理扩展分类************/
		$cats = I('post.ext');
		//先删除该商品原先的会员价格
		$gcModel = M('goods_cat');
		$gcModel -> where("goods_id=$id") -> delete();

		//有扩展分类时才执行
		if($cats){
			//循环添加
			foreach($cats as $v)
			{	
				//id为空的不写入
				if(empty($v))continue;
				$gcModel -> add(array(
					'cat_id' => $v,
					'goods_id' => $id,
					));
			}

		}

		/**************处理商品属性**************/
		
		$gaid = I('post.goods_attr_id'); //接收商品属性id
		$attrValue = I('post.attr_value'); //商品属性值
		$gaModel = M('goods_attr');
		$_i = 0; //循环次数

		//遍历商品属性，根据商品属性的id来判断是添加还是修改
		foreach($attrValue as $k => $v)
		{	
			foreach($v as $k1 => $v1)
			{	
				//replace into ： 如果记录存在就修改，不存在则添加,以主键判断一条记录是否存在
				$gaModel -> execute("REPLACE INTO goods_attr VALUES('$gaid[$_i]','$v1','$k','$id')");
				/**
				//没有商品属性ID就执行添加
				if($gaid[$_i] == ''){
					$gaModel -> add(array(
						'attr_value' => $v1,
						'attr_id' => $k,
						'goods_id' => $id,
					));
				}else{
					//有id则执行修改
					$gaModel -> where("id = $gaid[$_i]") -> setField('attr_value',$v1);
				}
				**/
				//记录循环次数
				$_i++;
			}
		}


		//有选择性过滤XSS
		$data['goods_desc'] = removeXss($_POST['goods_desc']);
	}

	/*-------------------------------分割线---------------------------------*/
	//删除前执行操作
	protected function _before_delete($option)
	{
		$id = $option['where']['id'];
		//防止SQL注入
		if(is_array($id))
		{
			$this->error = '不支持批量删除';
			return FALSE;
		}
		
		/*************LOGO**************/
		$logo = $this -> field('logo,mbig_logo,big_logo,mid_logo,sm_logo') -> find($id);
		delImage($logo);
		
		/************处理会员价格***********/
		$mpModel = M('member_price');
		$mpModel -> where("goods_id = $id")	-> delete();

		/************处理扩展分类***********/
		$gcModel = M('goods_cat');
		$gcModel -> where("goods_id = $id")	-> delete();

		/************处理商品库存***********/
		$gnModel = M('goods_number');
		$gcModel -> where("goods_id = $id")	-> delete();
		
		/***********处理相册图片***********/
		$gpModel = M('goods_pic');
		//取出路径
		$pics = $gpModel -> field('pic,big_pic,mid_pic,sm_pic') -> where("goods_id=$id") -> select();
		// p($pics);die;
		//删除硬盘图片
		//一件商品下有多个相片，所以此时获得的是个二维数组 ，delImage只能处理一维
		foreach($pics as $v)
		{
			delImage($v);
		}
		//从数据库中把记录删除
		$gpModel -> where("goods_id = $id") -> delete();

		/************处理商品属性*************/
		$gaModel = M('goods_attr');
		$gaModel -> where("goods_id=$id") -> delete();
	}

	/*-------------------------------分割线---------------------------------*/
	//添加后执行操作
	protected function _after_insert($data,$option)
	{	

		/************处理商品属性**********/
		//接收属性
		$attrValues = I('post.attr_value');
		$gaModel = M('goods_attr');

		// 循环添加
		foreach($attrValues as $k => $v)
		{
			//属性值去重,防止重复属性添加
			$v = array_unique($v);
			if($v){
				foreach($v as $k1 => $v1)
				{
					$gaModel -> add(array(
						'attr_id' => $k,
						'attr_value' => $v1,
						'goods_id' => $data['id'],
					));
				}
			}
		}

		/************处理扩展分类**********/
		//接收扩展分类id
		$cats = I('post.ext');
		//有扩展分类时才执行
		if($cats){
			$gcModel = M('goods_cat');
			//循环添加
			foreach($cats as $v)
			{	
				//id为空的不写入
				if(empty($v))continue;
				$gcModel -> add(array(
					'cat_id' => $v,
					'goods_id' => $data['id'],
					));
			}

		}

		/***********处理会员价格***********/
		//接收会员价格
		$mp = I('post.member_price');
		if($mp){
			//将每个会员价格和商品id遍历写入数据库
			$mpModel = M('member_price');

			foreach($mp as $k => $v)
			{	
				//将价格转换为浮点数，防止输入非数字的字符串
				$_v = (float)$v;
				//价格大于0才存入数据库
				if($_v > 0 ){
					$mpModel -> add(array(
						'price' => $_v,
						'level_id' => $k,
						'goods_id' => $data['id'],
					));
				}
			}
		}

		/************处理相册图片**********/
		if(isset($_FILES['pic']))
		{
			//重组相册信息再调用uplodeOne循环添加
			$pics = array();

			//遍历图片，重组图片信息
			foreach($_FILES['pic']['name'] as $k => $v)
			{
				$pics[] = array(
					'name' => $v,
					'type' => $_FILES['pic']['type'][$k],
					'tmp_name' => $_FILES['pic']['tmp_name'][$k],
					'error' => $_FILES['pic']['error'][$k],
					'size' => $_FILES['pic']['size'][$k],

				);

			}

			//把处理好的数组赋回给$_FILES,因为封装的uploadOne是再$_FILES中寻找图片
			$_FILES = $pics;
			// p($_FILES);die;
			$gpModel = M('goods_pic');
			//循环添加
			foreach($pics as $k => $v)
			{
				// 如果图片错误代码为0才执行添加
				if($v['error'] == 0)
				{
					$ret = uploadOne($k,'Pic',array(
								array(650,650),
								array(350,350),
								array(50,50)
							));

					if($ret['ok'] == 1)
					{
						$gpModel -> add(array(
							'pic' => $ret['images'][0],
							'big_pic' => $ret['images'][1],
							'mid_pic' => $ret['images'][2],
							'sm_pic' => $ret['images'][3],
							'goods_id' => $data['id'],
						));
					}
				}
			}

		}
	} 

	/*-------------------------------分割线---------------------------------*/


	/********************前台代码*********************/
	/**
	 * [getPromoteGoods 取出当前正在促销的商品
	 * @param  integer $limit [取几条]
	 * @return [type]         [正在促销的数据]
	 */
	public function getPromoteGoods($limit = 5)
	{	
		$today = date("Y-m-d H:i");
		//获得正在促销的商品
		$proData = $this -> field('id,goods_name,mid_logo,promote_price')
						 -> where(array(
						 		'is_on_sale' => array('eq','是'),
						 		'promote_price' => array('gt',0),
						 		'promote_start_date' => array('elt',$today),
						 		'promote_end_date' => array('egt',$today)
						 	))
						 -> limit($limit)
						 -> order("sort_num asc")
						 -> select();

		return $proData;
	}

	/**
	 * [getTypeGoods description]
	 * @param  [type]  $type  [指定的类型]
	 * @param  integer $limit [取几条]
	 * @return [type]         [该类型下的前5条商品]
	 */
	public function getTypeGoods($type,$limit = 5)
	{
		return $this -> field('id,goods_name,shop_price,mid_logo')
					 -> where(array(
					 		'is_on_sale' => array('eq','是'),
					 		"$type" => array('eq','是'),
					 	))
					 -> limit($limit)
					 -> order("sort_num asc")
					 -> select();
	}

	/**
	 * [getMemberPrice 获取当前会员该商品的会员价格
	 * @param  [type] $goodsId [商品id]
	 * @return [type]          [description]
	 */
	public function getMemberPrice($goodsId)
	{
		$today = date('Y-m-d H:i');
		$level_id = session('level_id');

		$promotePrice = $this -> field('promote_price')
							   -> where(array(
							   		'id' => array('eq',$goodsId),
							   		'promote_price' => array('gt',0),
							   		'promote_start_date' => array('elt',$today),
							   		'promote_end_date' => array('egt',$today),

							   	))
							   -> find();
		// echo $promotePrice['promote_price'];
		//判断会员是否登陆
		if($level_id){
			$mpModel = M('member_price');
			$mpData = $mpModel -> field('price') 
			                   -> where(array(
			                   		'goods_id' => array('eq',$goodsId),
			                   		'level_id' => $level_id,
								))
			                   -> find();
			 //判断这个会员级别是否有会员价格
			if($mpData['price']){
				// 判断是否有促销价格
				if($promotePrice['promote_price']){
					return min($mpData['price'],$promotePrice['promote_price']);
				}else{
					return $mpData['price'];
				}
			}else{
				//如果没有会员价格就返回本店价格或促销价格
				$p = $this -> field('shop_price') -> find($goodsId);
				//检查该商品是否促销
				if($promotePrice['promote_price']){
					return min($p['shop_price'],$promotePrice['promote_price']);
				}else{
					return $p['shop_price'];
				}
			}
		}else{

			//如果没有会员价格就返回本店价格或促销价格
			$p = $this -> field('shop_price') -> find($goodsId);
			//检查该商品是否促销
			if($promotePrice['promote_price']){
				return min($p['shop_price'],$promotePrice['promote_price']);
			}else{
				return $p['shop_price'];
			}
		}
	}

	/**
	 * [cat_search 根据分类id查找出该分类及其子类下的所有商品[搜索，排序，分页，取数据]
	 * @param  [type]  $catId    [description]
	 * @param  integer $pageSize [description]
	 * @return [type]            [description]
	 */
	public function cat_search($catId,$pageSize = 20)
	{	
		/****************搜索*****************/
		//根据分类id找出其下的所以商品
		$goodsId = $this -> getGoodsIdByCatId($catId);
		$where['a.id'] = array('IN',$goodsId);

		//品牌
		$brand_id = (int)I('get.brand_id');
		// echo $brand_id;
		if($brand_id)
			$where['brand_id'] =  array('eq',$brand_id);

		//价格
		$price = I('get.price');
		if($price)
		{
			$price = explode('-',$price);
			$where['shop_price'] = array('between',$price);
		}

		/***商品属性***/
		$gaModel = M('goods_attr');
		$attrGoodsId = null;//保存每个属性搜索处理的商品id
		//根据商品搜索，循环所有参数找出属性的参数进行搜索
		foreach($_GET as $k => $v)
		{
			if(strpos($k,'attr_') !== false)
			{
				//解析出属性id和属性值
				$attrId = str_replace('attr_','',$k); //属性id
				$attrName = strrchr($v,'-'); //属性名
				$attrValue = str_replace($attrName,'', $v);//属性值

				//根据属性id和属性值搜索出这个属性值下的商品id并返回字符串格式[1,2,3,4,5]
				$gids = $gaModel -> field('GROUP_CONCAT(goods_id) gid')
								 -> where(array(
								 		'attr_value' => array('eq',$attrValue),
								 		'attr_id' => array('eq',$attrId),
								 	))
								 -> find();
				// p($gids['gid']);
				//判断是否有商品
				if($gids['gid'])
				{
					$gids['gid'] = explode(',', $gids['gid']);
					//判断是否是第一个搜索属性
					if($attrGoodsId === null)
					{
						$attrGoodsId = $gids['gid'];
					}else{
						// 和上一次搜索出来的结果求集
						$attrGoodsId = array_intersect($attrGoodsId, $gids['gid']);
						//如果没有商品满足条件就不考虑下一个属性
						if(empty($attrGoodsId)){
							$where['a.id'] = array('eq',0);
							break;
						}
					}
				}
				else
				{
					//前几次交集的结果清空
					$attrGoodsId = array();
					$where['a.id'] = array('eq',0);
					break;
				}
			}

		}
		//判断如果循环求次之后这个数组不为空说明这些就是满足条件的商品id
		if($attrGoodsId)
		{
			$where['a.id'] = array('IN',$attrGoodsId);
		}

		/***************排序********************/
		//默认根据销量降序
		$orderby = 'xl';
		$orderway = 'desc';
		$odby = I('get.odby');
		// echo $odby . '<br>';
		if($odby)
		{	//商品时间排序
			if($odby == 'addtime'){
				$orderby = 'a.addtime';
			}
			//根据商品价格排序
			if(strpos($odby,'price_') !== FALSE )
			{
				$orderby = 'a.shop_price';
				if($odby == 'price_asc')
				{
					$orderway = 'asc';
				}
			}

		}

		/***************分页*******************/
		$count = $this ->alias('a') ->field('COUNT(a.id) goods_count,GROUP_CONCAT(a.id) goods_id') -> where($where)->find();//取出总记录数
		// echo $this -> getLastSql();
		// 把商品ID返回
		$data['goods_id'] = explode(',', $count['goods_id']);
		// p($count['goods_id']);
		//生成分页类对象
		$pageObj = new \Think\Page($count['goods_count'],$pageSize);

		//设置样式
		$pageObj -> setConfig('prev' , '上一页');
		$pageObj -> setConfig('next' , '下一页');

		//生成在页面显示的分页字符串
		$data['page'] =  $pageObj -> show();

		/**************取数据*******************/
		$data['data'] = $this -> alias('a')
			  -> field('a.id,a.goods_name,a.shop_price,a.mid_logo,SUM(b.goods_number) xl')
			  -> join("LEFT JOIN __ORDER_GOODS__ b ON( a.id=b.goods_id 
			  			AND 
			  			b.order_id IN (SELECT id FROM __ORDERS__ WHERE pay_status = '是'))")
			  -> where($where)
			  -> group('a.id')
			  -> limit($page->firstRow . ',' . $page->listRows)
			  -> order ("$orderby $orderway")
			  -> select();
		// echo $this ->getLastSql();
		//返回分页字符串和数据
		return  $data;
	}


	/**
	 * 获取某个关键字下某一页的商品
	 *
	 * @param unknown_type $key
	 * @param unknown_type $perPage
	 */
	public function key_search($key, $pageSize = 60)
	{
		/*************** 搜索 *************************/
		
		//$goodsId = $this->getGoodsIdByCatId($catId);
		
		// 根据关键字【商品名称、商品描述、商品属性值】取出商品ID
		$goodsId = $this->alias('a')
		->field('GROUP_CONCAT(DISTINCT a.id) gids')
		->join('LEFT JOIN __GOODS_ATTR__ b ON a.id=b.goods_id')
		->where(array(
			'a.is_on_sale' => array('eq', '是'),
			'a.goods_name' => array('exp', " LIKE '%$key%' OR a.goods_desc LIKE '%$key%' OR attr_value LIKE '%$key%'"),
		))
		->find();

		$goodsId = explode(',', $goodsId['gids']);
		
		$where['a.id'] = array('in', $goodsId);
		// 品牌
		$brandId = I('get.brand_id');
		if($brandId)
			$where['a.brand_id'] = array('eq', (int)$brandId);
		// 价格
		$price = I('get.price');
		if($price)
		{
			$price = explode('-', $price);
			$where['a.shop_price'] = array('between', $price);
		}
		
		/******************************************* 商品搜索开始 ************************************************/
		$gaModel = D('goods_attr');
		$attrGoodsId = NULL;  // 根据每个属性搜索出来的商品的ID
		// 根据属性搜索 : 循环所有的参数找出属性的参数进行查询
		foreach ($_GET as $k => $v)
		{
			// 如果变量是以attr_开头的说明是一个属性的查询, 格式：attr_1/黑色-颜色
			if(strpos($k, 'attr_') === 0)
			{
				// 先解析出属性ID和属性值
				$attrId = str_replace('attr_', '', $k); // 属性id
				// 先取出最后一个-往后的字符串
				$attrName = strrchr($v, '-');
				$attrValue = str_replace($attrName, '', $v);
				// 根据属性ID和属性值搜索出这个属性值下的商品id：并返回一个字符串格式：1,2,3,4,5,6,7
				$gids = $gaModel->field('GROUP_CONCAT(goods_id) gids')->where(array(
					'attr_id' => array('eq', $attrId),
					'attr_value' => array('eq', $attrValue),
				))->find();
				// 判断是有商品
				if($gids['gids'])
				{
					$gids['gids'] = explode(',', $gids['gids']);
					// 说明是搜索的第一个属性
					if($attrGoodsId === NULL)
						$attrGoodsId = $gids['gids'];  // 先暂存起来
					else 
					{
						// 和上一个属性搜索出来的结果求集
						$attrGoodsId = array_intersect($attrGoodsId, $gids['gids']);
						// 如果已经没有商品满足条件就不用再考虑下一个属性了
						if(empty($attrGoodsId))
						{
							$where['a.id'] = array('eq', 0);
							break;
						}
					}
				}
				else 
				{
					// 前几次的交集结果清空
					$attrGoodsId = array();
					// 如果这个属性下没有商品就应该向where中添加一个不可能满足的条件，这样后面取商品时就取不出来了！
					$where['a.id'] = array('eq', 0);
					// 取出循环，不用再查询下一个属性了
					break;
				}
			}
		}
		// 判断如果循环求次之后这个数组还不为空说明这些就是满足所有条件的商品id
		if($attrGoodsId)
			$where['a.id'] = array('IN', $attrGoodsId);
		/******************************************* 商品搜索结束 ************************************************/	
			
		
		
		/**************** 翻页 *********************/
		// 取出总的记录数，以及所有的商品ID的字符串
		//$count = $this->alias('a')->where($where)->count();  // 这个只能取总记录数，改成下面这行，即取总记录数，又取出了商品ID
		$count = $this->alias('a')->field('COUNT(a.id) goods_count,GROUP_CONCAT(a.id) goods_id')->where($where)->find();
		// 把商品ID返回
		$data['goods_id'] = explode(',', $count['goods_id']);
		
		$page = new \Think\Page($count['goods_count'], $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		
		/*********************** 排序 ********************/
		$oderby = 'xl';    // 默认
		$oderway = 'desc'; // 默认
		$odby = I('get.odby');
		if($odby)
		{
			if($odby == 'addtime')
				$oderby = 'a.addtime';
			if(strpos($odby, 'price_') === 0)
			{
				$oderby = 'a.shop_price';
				if($odby == 'price_asc')
					$oderway = 'asc';
			}
		}
		
		/**************** 取数据 ********************/
		$data['data'] = $this->alias('a')
		->field('a.id,a.goods_name,a.mid_logo,a.shop_price,SUM(b.goods_number) xl')
		->join('LEFT JOIN __ORDER_GOODS__ b 
				 ON (a.id=b.goods_id 
				      AND 
				     b.order_id IN(SELECT id FROM __ORDERS__ WHERE pay_status="是"))')
		->where($where)
		->group('a.id')
		->limit($page->firstRow.','.$page->listRows)
		->order("$oderby $oderway")
		->select();
		// echo $this -> getLastSql();
		return $data;
	}
}