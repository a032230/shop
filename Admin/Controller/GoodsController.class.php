<?php  
namespace Admin\Controller;
use Think\Controller;
/*--------------------------
|        商品控制器         |
--------------------------*/
class GoodsController extends Controller{

	//ajax删除商品属性
	public function ajaxDelAttr()
	{
		if(IS_AJAX){
			$gaid = I('get.gaid');
			$gaModel = M('goods_attr');

			//执行删除
			$gaModel -> delete($gaid);
		}
	}

	//ajax获取类型属性
	public function ajaxGetAttr()
	{
		if(IS_AJAX){
			$typeId = I('get.type_id');
			$attrModel = M('attr');
			$data = $attrModel -> where("type_id=$typeId") -> select();
			echo json_encode($data);
		}
	}

	//ajax删除相册图片
	public function ajaxDelPic()
	{
		if(IS_AJAX){
			$pid = I('get.pid');
			//根据id删除数据库的数据和硬盘上的图片
			$gpModel = M('goods_pic');
			$pics = $gpModel ->field('pic,big_pic,sm_pic,mid_pic') -> find($pid);

			//删除硬盘上的图片
			delImage($pics);

			//删除数据记录
			$gpModel -> delete($pid);
		}
	}

	//商品列表页
	public function lst()
	{
		$goodsModel = D('goods');

		//返回数据和分页
		$data = $goodsModel -> search();

		//将数据分配到前台
		$this -> assign($data);

		//取分类表的数据
		$cateModel = D('category');
		$cats = $cateModel -> getTree();
		//分配页头动态标题和链接
		$this -> assign(array(
			'_page_title' => '商品列表',
			'_page_btn_name' => '添加商品',
			'_page_btn_link' => U('add'),
			'cats' => $cats,
		));
		$this -> display();
	}

	//商品添加
	public function add()
	{
		if(IS_POST){

			// p($_FILES);die;
			// p($_POST);exit;
			$goodsModel = D('goods');
			//验证表单并保存到模型
			if($goodsModel -> create(I('post.'),1))
			{	
				//执行添加
				if($goodsModel -> add()){
					$this -> success('添加成功！',U('lst'));
					exit;
				}
			}
			//以上都执行失败，则打印错误信息
			$this -> error($goodsModel -> getError());
		}

		//取出所有会员的等级
		$mlModel = M('member_level');
		$mldata = $mlModel -> getField('id,level_name');
		// p($mldata);die;
		//取出所有分类
		$cateModel = D('category');
		$cats = $cateModel -> getTree();
		//分配页头动态标题和链接
		$this -> assign(array(
			'_page_title' => '添加商品',
			'_page_btn_name' => '商品列表',
			'_page_btn_link' => U('lst'),
			'data' => $mldata,
			'cats' => $cats,
		));
		$this -> display();
	}

	// 商品编辑
	public function edit()
	{
		$id = I('get.id');
		$goodsModel  = D('goods');
		if(IS_POST){
			// p($_POST);die;
			//验证表单并保存到模型
			if($goodsModel -> create(I('post.'),2)){
				if($goodsModel -> save() !== FALSE){
					$this -> success('修改成功！',U('lst'));
					exit;
				}
			}
			//以上操作执行失败
			$this -> error($goodsModel -> getError());
		}
		//根据id获得对应数据
		$data = $goodsModel -> find($id);

		//分配数据到页面
		$this -> assign('data',$data);
		//取出所有分类
		$cateModel = D('category');
		$cats = $cateModel -> getTree();

		//取出相册图片
		$gpModel = M('goods_pic');
		$gpdata = $gpModel ->where("goods_id=$id") -> select();

		//取出会员等级
		$mlModel = M('member_level');
		$mldata = $mlModel -> getField('id,level_name');
		// p($mldata);die;
		//取出会员价格
		$mpModel = M('member_price');
		$mpdata = $mpModel ->where("goods_id=$id") -> select();
		// p($mpdata);die
		//把会员价格的二维数组转一维
		$_mpdata = array();
		foreach($mpdata as $v){
			$_mpdata[$v['level_id']] = $v['price'];
		}
		// p($_mpdata);die;
		//取出扩展分类
		$gcModel = M('goods_cat');
		$ext = $gcModel ->where("goods_id = $id") -> select();
		// p($ext);die;
		
		//获取当前类型下所有的属性,
		/**
		 *  注：此处以属性表为主关联商品属性表
		 *  如果以商品属性表关联属性表的话，修改页面只显示在商品属性表里保存的属性，
		 *  而当再该类型新添加一个属性，修改页面不会显示新添加的属性
		 *  如：我在手机的类型里新添加了个X属性,而某品牌的手机是属于手机这个类型，我在添加该商品时还没有这个X属性
		 * 
		 **/
		$attrModel = M('attr');
		$gadata = $attrModel -> alias('a')
						   -> field('a.*,b.id as goods_attr_id,b.attr_value')
						   -> join("LEFT JOIN __GOODS_ATTR__ b ON (a.id=b.attr_id AND b.goods_id=$id )")
						   -> where(array('a.type_id' => array('eq',$data['type_id'])))
						   -> select();
		// p($gadata);die;
		// echo $attrModel->getLastSql();

		//分配页头动态标题和链接
		$this -> assign(array(
			'_page_title' => '编辑商品',
			'_page_btn_name' => '商品列表',
			'_page_btn_link' => U('lst'),
			'gpdata' => $gpdata,
			'cats' => $cats,
			'ext'  => $ext,
			'mldata' => $mldata,
			'mpdata' => $_mpdata,
			'gadata' => $gadata,
		));
		$this -> display();
	}

	//放入回收站
	public function recycle()
	{

		$goodsModel = M('goods');
		//IS_GET不行。。。
		if(!$_GET){

			//获取所有回收站的数据
			$data = $goodsModel ->where("is_delete = '是'") -> select();

			$this -> assign('data',$data);
			$this -> display();
			
		}else{
			$id = I('get.id');
			//将字段is_delete设置为是
			$goodsModel->where("id=$id")->setField('is_delete','是'); 
			$this -> redirect('lst');
		}

		
	}

	//重新上架
	public function ground()
	{
		$id = I('get.id');
		$goodsModel = M('goods');
		$goodsModel -> where("id = $id") -> setField('is_delete','否');
		$this -> redirect('recycle');
	}

	//商品彻底删除
	public function del()
	{
		$id = I('get.id');

		$goodsModel = D('goods');

		if($goodsModel -> delete($id) !== FALSE)
		{
			$this -> success('删除成功',U('recycle'));
			exit;
		}

		$this -> error($goodsModel -> getError());
	}
}