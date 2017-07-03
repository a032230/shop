<?php
namespace Admin\Controller;
use Think\Controller;

/*--------------------------
|         权限控制器        |
--------------------------*/
class AuthController extends InitController
{
    //添加
    public function add()
    {
    	if(IS_POST)
    	{
    		$model = D('Auth');
    		if($model->create(I('post.'), 1))
    		{
    			if($id = $model->add())
    			{
    				$this->success('添加成功！', U('lst'));
    				exit;
    			}
    		}
    		$this->error($model->getError());
    	}
		$parentModel = D('Auth');
		$parentData = $parentModel->getTree();
		$this->assign('parentData', $parentData);

		// 设置页面中的信息
		$this->assign(array(
			'_page_title' => '添加权限',
			'_page_btn_name' => '权限列表',
			'_page_btn_link' => U('lst'),
		));
		$this->display();
    }

    //修改
    public function edit()
    {
    	$id = I('get.id');
    	if(IS_POST)
    	{
    		$model = D('Auth');
    		if($model->create(I('post.'), 2))
    		{
    			if($model->save() !== FALSE)
    			{
    				$this->success('修改成功！', U('lst'));
    				exit;
    			}
    		}
    		$this->error($model->getError());
    	}
    	$model = M('Auth');
    	$data = $model->find($id);
    	$this->assign('data', $data);
		$parentModel = D('Auth');
		$parentData = $parentModel->getTree();
		$children = $parentModel->getChildren($id);
		$this->assign(array(
			'parentData' => $parentData,
			'children' => $children,
		));

		// 设置页面中的信息
		$this->assign(array(
			'_page_title' => '修改权限',
			'_page_btn_name' => '权限列表',
			'_page_btn_link' => U('lst'),
		));
		$this->display();
    }

    //删除
    public function delete()
    {
    	$model = D('Auth');
    	if($model->delete(I('get.id', 0)) !== FALSE)
    	{
    		$this->success('删除成功！', U('lst'));
    		exit;
    	}
    	else 
    	{
    		$this->error($model->getError());
    	}
    }

    //显示
    public function lst()
    {
    	$model = D('Auth');
		$data = $model->getTree();
    	$this->assign(array(
    		'data' => $data,
    	));

		// 设置页面中的信息
		$this->assign(array(
			'_page_title' => '权限列表',
			'_page_btn_name' => '添加权限',
			'_page_btn_link' => U('add'),
		));
    	$this->display();
    }
}