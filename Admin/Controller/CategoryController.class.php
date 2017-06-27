<?php  
namespace Admin\Controller;
use Think\Controller;

/*------------------------
 |       分类控制器       |
 ------------------------*/

class CategoryController extends Controller{

	// 显示分类
	public function lst()
	{
		$catModel = D('category');
		$data = $catModel -> getTree();
		// 设置页面中的信息
		$this->assign(array(
			'_page_title' => '列表',
			'_page_btn_name' => '添加',
			'_page_btn_link' => U('add'),
			'data' => $data,
		));

		$this -> display();
	}

	// 分类添加
	public function add()
	{
		$cateModel = D('category');
		if(IS_POST){
			//验证表单并保存到模型
			if($cateModel -> create(I('post.'),1))
			{
				if($cateModel -> add()){
					$this -> success('添加成功',U('lst'));
					exit;
				}
			}

			//以上执行失败，从模型中获取错误信息并打印
			$this -> error($cateModel -> getError());
		}
		$data = $cateModel -> getTree();

		//分配数据
		$this -> assign(array(
			'_page_title' => '添加',
			'_page_btn_name' => '列表',
			'_page_btn_link' => U('lst'),
			'data' => $data,
		));
		$this -> display();
	}

	//编辑分类
	public function edit()
	{
		$cateModel = D('category');
		$id = I('get.id');
		if(IS_POST){
			if($cateModel -> create(I('post.'),2)){
				//验证表单并保存到模型
				if($cateModel -> save() !== FALSE)
				{
					$this -> success('修改成功',U('lst'));
					exit;
				}
			}
			//以上执行失败
			$this -> error($cateModel -> getError());
		}
		// 获取该id下的数据
		$data = $cateModel ->find($id);
		//获取数状结构数据
		$tree = $cateModel -> getTree();
		//获取该id下的所有子类id
		$children = $cateModel -> getChildren($id);
		//分配数据
		$this -> assign(array(
			'_page_title' => '添加',
			'_page_btn_name' => '列表',
			'_page_btn_link' => U('lst'),
			'data' => $data,
			'tree' => $tree,
			'children' => $children,
		));
		$this -> display();

	}

	/**
	 * [del  ajax删除分类及该分类下所有的子分类
	 */
	public function del()
	{	if(IS_AJAX){
			$id = I('get.id');
			$mode = D('category');
			if($mode -> delete($id)){
				$this -> success('删除成功',U('lst'),TRUE);
				exit;
			}
			$this -> error($mode->getError(),'',TRUE);
		}
	}
}