<?php  
namespace Admin\Model;
use Think\Model;

/*--------------------------
|         商品模型          |
--------------------------*/
class GoodsModel extends Model{

	//新增时允许接收的字段
	protected $insertFields = "goods_name,shop_price,market_price,is_on_sale,goods_desc";

	//修改时允许接收的字段
	protected $updateFields = "id,goods_name,shop_price,market_price,is_on_sale,goods_desc";

	//定义表单验证规则
	protected $_validate = array(
		array('goods_name','require','商品名称不能为空',1),
		array('market_price','currency','市场价格必须为货币类型',1),
		array('shop_price','currency','本店价格必须为货币类型',1),
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

			$upload = new \Think\Upload(); 
			$upload -> maxSize = 1024 *1024; //限制上传文件的大小为1M
			$upload -> exts = array('jpeg','gif','png','jpg'); //设置允许上传的文件类型
			$upload -> rootPath = "./Public/Upload/";//文件上传的根目录
			$upload -> savePath = 'Goods/'; //文件上传的子目录

			//执行上传
			$info = $upload -> upload();

			if(!$info){
				//上传失败获取错误信息并保持到模型
				$this -> error = $upload -> getError();
				return false;
			}else{
				/*******************生成缩略图******************/
				// 拼接原图路径
				$logo = $info['logo']['savepath'] . $info['logo']['savename'];
				//拼接缩略图的路径和名称
				$mbiglogo =  $info['logo']['savepath'] .'mbig_'. $info['logo']['savename'];
				$biglogo =  $info['logo']['savepath'] .'big_'. $info['logo']['savename'];
				$midlogo =  $info['logo']['savepath'] .'mid_'. $info['logo']['savename'];
				$smlogo =  $info['logo']['savepath'] .'sm_'. $info['logo']['savename'];
				//实例化图片类
				$img = new \Think\Image();
				//打开原图
				$img -> open('./Public/Upload/' . $logo);

				//生成缩略图
				$img -> thumb(700,700) -> save('./Public/Upload/' . $mbiglogo); //特大图
				$img -> thumb(350,350) -> save('./Public/Upload/' . $biglogo); //大图
				$img -> thumb(130,130) -> save('./Public/Upload/' . $midlogo); //中图
				$img -> thumb(50,50) -> save('./Public/Upload/' . $smlogo); //小图

				/***********将图片路径存入表单**************/
				//以绝对路径放入数据库，方便页面显示
				$data['logo'] = '/Public/Upload' . $logo;
				$data['mbig_logo'] = '/Public/Upload/' . $mbiglogo;
				$data['big_logo'] = '/Public/Upload/' . $biglogo;
				$data['mid_logo'] = '/Public/Upload/' . $midlogo;
				$data['sm_logo'] = '/Public/Upload/' . $smlogo;
			}
		}

		//选择性过滤Xss
		$data['goods_desc'] = removeXss($_POST['goods_desc']);
	}


	//搜索，分页，获取数据
	public function search($perPage = 1)
	{	

		/*********搜索*********/
		$where = array();
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
		$fa = I('get.fa');
		$ta = I('get.ta');
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


		/*******取数据******/
		$this ->limit($pageObj->firstRow . ',' . $pageObj ->listRows) ->where($where) -> select();
	}



	//修改前执行操作
	protected function _before_update(&$data,$option)
	{

	}
}