<?php
namespace Admin\Controller;
use Think\Controller;

/*--------------------------
|         角色控制器        |
--------------------------*/

class RoleController extends InitController{
    //添加
    public function add()
    {
    	if(IS_POST)
    	{
    		$model = D('Role');
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

     //取出所有权限并按树状结构打印
        $authModel = D('auth');
        $authData = $authModel -> getTree();
        // p($authData);die;
		// 设置页面中的信息
		$this->assign(array(
			'_page_title' => '添加角色',
			'_page_btn_name' => '角色列表',
			'_page_btn_link' => U('lst'),
            'authData' => $authData,
		));
		$this->display();
    }

    //修改
    public function edit()
    {
    	$id = I('get.id');
    	if(IS_POST)
    	{
            // p($_POST);die;
    		$model = D('Role');
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
    	$model = M('Role');
    	$data = $model-> alias('a')
                      -> field('a.*,GROUP_CONCAT(b.auth_id) AS auth_id')
                      -> join("LEFT JOIN __ROLE_AUTH__ b ON a.id=b.role_id")
                      -> where("id=$id")
                      -> find();

        //取出所有权限并按树状结构打印
        $authModel = D('auth');
        $authData = $authModel -> getTree();
    	$this->assign(array(
            'data' => $data,
            'authData' => $authData,
        ));

		// 设置页面中的信息
		$this->assign(array(
			'_page_title' => '修改角色',
			'_page_btn_name' => '角色列表',
			'_page_btn_link' => U('lst'),
		));
		$this->display();
    }

    //删除
    public function delete()
    {
    	$model = D('Role');
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
    	$model = D('Role');
    	$data = $model->search();
    	$this->assign(array(
    		'data' => $data['data'],
    		'page' => $data['page'],
    	));

		// 设置页面中的信息
		$this->assign(array(
			'_page_title' => '角色列表',
			'_page_btn_name' => '添加角色',
			'_page_btn_link' => U('add'),
		));
    	$this->display();
    }
}