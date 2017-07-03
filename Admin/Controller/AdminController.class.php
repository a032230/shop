<?php
namespace Admin\Controller;
use Think\Controller;

/*--------------------------
|         管理员控制器      |
--------------------------*/

class AdminController extends InitController 
{
    //添加
    public function add()
    {
    	if(IS_POST)
    	{
            // p($_POST);die;
    		$model = D('Admin');
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

        //获取所有角色
        $roModel = M('role');
        $roleData = $roModel -> select();

		// 设置页面中的信息
		$this->assign(array(
			'_page_title' => '添加管理员',
			'_page_btn_name' => '管理员列表',
			'_page_btn_link' => U('lst'),
            'roleData' => $roleData,
		));
		$this->display();
    }

    //修改
    public function edit()
    {
    	$id = I('get.id');
    	if(IS_POST)
    	{
    		$model = D('Admin');
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
    	$model = M('Admin');
    	$data = $model->find($id);

        //取出所有角色
        $roleModel = M('role');
        $roleData = $roleModel -> alias('a')
                               -> field('a.*,b.admin_id')
                               -> join("LEFT JOIN __ADMIN_ROLE__ b ON (a.id = b.role_id AND b.admin_id=$id)")
                               -> select();
        // echo $roleModel -> getLastSql();
        // p($roleData);die;
		// 设置页面中的信息
		$this->assign(array(
			'_page_title' => '修改管理员',
			'_page_btn_name' => '管理员列表',
			'_page_btn_link' => U('lst'),
            'data' => $data,
            'roleData' => $roleData,
		));
		$this->display();
    }

    //删除
    public function delete()
    {
    	$model = D('Admin');
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
    	$model = D('Admin');
    	$data = $model->search();
    	$this->assign(array(
    		'data' => $data['data'],
    		'page' => $data['page'],
    	));

		// 设置页面中的信息
		$this->assign(array(
			'_page_title' => '管理员列表',
			'_page_btn_name' => '添加管理员',
			'_page_btn_link' => U('add'),
		));
    	$this->display();
    }
}