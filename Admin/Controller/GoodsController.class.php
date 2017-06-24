<?php  
namespace Admin\Controller;
use Think\Controller;
/*--------------------------
|        商品控制器         |
--------------------------*/
class GoodsController extends Controller{

	//商品列表页
	public function lst()
	{
		$goodsModel = D('goods');

		//返回数据和分页
		$data = $goodsModel -> search();

		//将数据分配到前台
		$this -> assign($data);

		//分配页头动态标题和链接
		$this -> assign(array(
			'_page_title' => '商品列表',
			'_page_btn_name' => '添加商品',
			'_page_btn_link' => U('add'),
		));
		$this -> display();
	}

	//商品添加
	public function add()
	{
		if(IS_POST){
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

		//分配页头动态标题和链接
		$this -> assign(array(
			'_page_title' => '添加商品',
			'_page_btn_name' => '商品列表',
			'_page_btn_link' => U('lst'),
		));
		$this -> display();
	}

	// 商品编辑
	public function edit()
	{
		$id = I('get.id');
		$goodsModel  = D('goods');
		if(IS_POST){
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

		//分配数据到页码
		$this -> assign('data',$data);

		//分配页头动态标题和链接
		$this -> assign(array(
			'_page_title' => '编辑商品',
			'_page_btn_name' => '商品列表',
			'_page_btn_link' => U('lst'),
		));
		$this -> display();
	}


	public function del()
	{
		$id = I('get.id');

		$goodsModel = D('goods');

		if($goodsModel -> delete($id) !== FALSE)
		{
			$this -> success('删除成功',U('lst'));
			exit;
		}

		$this -> error($goodsModel -> getError());
	}
}