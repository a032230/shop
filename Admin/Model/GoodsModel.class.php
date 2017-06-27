<?php  
namespace Admin\Model;
use Think\Model;

/*--------------------------
|         商品模型          |
--------------------------*/
class GoodsModel extends Model{

	//新增时允许接收的字段
	protected $insertFields = "cat_id,brand_id,goods_name,shop_price,market_price,is_on_sale,goods_desc";

	//修改时允许接收的字段
	protected $updateFields = "id,cat_id,brand_id,goods_name,shop_price,market_price,is_on_sale,goods_desc";

	//定义表单验证规则
	protected $_validate = array(
		array('goods_name','require','商品名称不能为空',1),
		array('market_price','currency','市场价格必须为货币类型',1),
		array('shop_price','currency','本店价格必须为货币类型',1),
		array('brand_id','require','商品品牌不能为空',1),
		array('cat_id','require','必须选择一个分类',1),
	);

	//自动完成
	protected $_auto = array(
		array('addtime','time',1,'function'),
	);

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


	//搜索，分页，排序，获取数据
	public function search($perPage = 1)
	{	

		/*********搜索*********/
		$where = array();
		$where['is_delete'] = '否';
		//商品品牌
		$brand_id = I('get.brand_id');
		if($brand_id)
			$where['brand_id'] = array('eq',$brand_id);

		//商品名
		$gn = I('get.gn');
		if($gn)
			$where['goods_name'] = array('like',"%$gn%"); //where goods_name like %$gn%;
		//价格
		$fp = I('get.fp');
		$tp = I('get.tp');
		if($fp && $tp)
			$where['shop_price'] = array('between',array($fp,$tp));
		else if($fp)
			$where['shop_price'] = array('egt',$fp);
		else if($tp)
			$where['shop_price'] = array('elt',$tp);

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


		/*********分页*********/
		$count = $this -> where($where)->count();//取出总记录数

		//生成分页类对象
		$pageObj = new \Think\Page($count,$perPage);

		//设置样式
		$pageObj -> setConfig('prev' , '上一页');
		$pageObj -> setConfig('next' , '下一页');

		//生成在页面显示的分页字符串
		$pageString = $pageObj -> show();


		/*****排序**********/
		$orderby = 'id'; //默认排序字段
		$orderway = 'desc'; //默认排序方式
		$odby = I('get.odby');

		if($odby == 'id_asc')
			$orderway = 'asc';
		elseif($odby == 'price_desc')
			$orderby = 'shop_price';
		elseif($odby == 'price_asc')
		{
			$orderby = 'shop_price';
			$orderway = 'asc';
		}




		/*******取数据******/
		$data = $this -> field('a.*,b.brand_name,c.cat_name')
					  -> alias('a')
		              -> join("LEFT JOIN __BRAND__ b ON a.brand_id = b.id
		              		   LEFT JOIN __CATEGORY__ c ON a.cat_id = c.id")
		              -> where($where) 
		              -> order("$orderby $orderway")
		              -> limit($pageObj->firstRow . ',' . $pageObj ->listRows)
		              -> select();

		   // echo $this -> getLastSql();
		//返回分页字符串和数据
		return  array('data' => $data,'page' => $pageString);
	}



	//修改前执行操作
	protected function _before_update(&$data,$option)
	{
		// p($option);die;
		$id = $option['where']['id'];//获取要修改商品的id
		/***********处理logo*************/
		//判断是否重新上传图片
		if($_FILES['logo']['error'] == 0){
		
			// $upload = new \Think\Upload();//实例化上传类
			// $upload -> maxSize = 1024 *1024; //限制上传大小为1M
			// $uplpad -> exts = array('jpg','jpeg','gif','png');//限制上传类型
			// $upload -> rootPath = './Public/Upload/'; //设置图片存放的根目录
			// $upload -> savePath = 'Goods/'; //存放的子目录

			// //执行上传
			// $info = $upload -> upload();

			// if(!$info){
			// 	//上传失败则将错误信息保存到模型，交由控制器打印
			// 	$this -> error = $upload -> getError();
			// 	return false;
			// }else{
			// 	//拼接原图路径
			// 	$logo = $info['logo']['savepath'] . $info['logo']['savename'];
			// 	//拼接缩略图路径
			// 	$mbiglogo = $info['logo']['savepath'] . 'mbig_' . $info['logo']['savename'];
			// 	$biglogo = $info['logo']['savepath'] . 'big_' . $info['logo']['savename'];
			// 	$midlogo = $info['logo']['savepath'] . 'mid_' . $info['logo']['savename'];
			// 	$smlogo = $info['logo']['savepath'] . 'sm_' . $info['savename'];

			// 	/********生成缩略图********/
			// 	$img = new \Think\Image(); 
			// 	$img -> open('./Public/Upload/' . $logo);//打开原图
			// 	/**
			// 	 * 执行缩略
			// 	 * 碰到的坑：
			// 	 * 缩略过程由大到小，当上次进行缩略图片的尺寸小于当次图片缩略的尺寸，则无法缩略直接返回该图；
			// 	 * 不解：图片缩略是基于原图，还是基于前一次缩略的图片为参照物 ?;
			// 	 */
			// 	$img -> thumb(700,700) -> save('./Public/Upload/'.$mbiglogo);//特大图
			// 	$img -> thumb(350,350) -> save('./Public/Upload/'.$biglogo); //大图
			// 	$img -> thumb(130,130) -> save('./Public/Upload/'.$midlogo);//中图
			// 	$img -> thumb(50,50) -> save('./Public/Upload/'.$smlogo);//小图


			// 	//将图片路径存放入表单，
			// 	$data['logo'] =  $logo; 
			// 	$data['mbig_logo'] = $mbiglogo; 
			// 	$data['big_logo'] = $biglogo; 
			// 	$data['mid_logo'] =  $midlogo; 
			// 	$data['sm_logo'] =  $smlogo; 
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


				//删除硬盘上存放的未修改之前的图片资源
				$oldlogo = $this -> field('logo,mbig_logo,big_logo,mid_logo,sm_logo') -> find($id);
				// p($oldlogo);die;
				/**
				unlink('./Public/Upload/'.$oldlogo['logo']);
				unlink('./Public/Upload/'.$oldlogo['mbig_logo']);
				unlink('./Public/Upload/'.$oldlogo['big_logo']);
				unlink('./Public/Upload/'.$oldlogo['mid_logo']);
				unlink('./Public/Upload/'.$oldlogo['sm_logo']);
				**/
				delImage($oldlogo);
			//}
		}
		//有选择性过滤XSS
		$data['goods_desc'] = removeXss($_POST['goods_desc']);
	}

	//删除前执行操作
	protected function _before_delete($option)
	{
		$id = $option['where']['id'];
		//防止SQL注入
		if(is_array($option['where']['id']))
		{
			$this->error = '不支持批量删除';
			return FALSE;
		}
		
		//删除硬盘上存放的图片
		$logo = $this -> field('logo,mbig_logo,big_logo,mid_logo,sm_logo') -> find($id);
		/**
		unlink('./Public/Upload/'.$logo['logo']);
		unlink('./Public/Upload/'.$logo['mbig_logo']);
		unlink('./Public/Upload/'.$logo['big_logo']);
		unlink('./Public/Upload/'.$logo['mid_logo']);
		unlink('./Public/Upload/'.$logo['sm_logo']);
		**/
		delImage($logo);

		//删除该商品时同时删除对应的会员价格信息
		//加入外键约束，此代码注释
		
		// $mpModel = M('member_price');
		// $mpModel -> where(array('goods_id' => array('eq',$id))) -> delete();
			
	}

	//添加后执行操作
	protected function _after_insert($data,$option)
	{	
		//接收会员价格
		$mp = I('post.member_price');

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
}