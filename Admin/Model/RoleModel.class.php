<?php
namespace Admin\Model;
use Think\Model;

/*--------------------------
|         角色模型          |
--------------------------*/

class RoleModel extends Model 
{	
	//添加时允许接收的字段
	protected $insertFields = array('role_name');

	//修改时允许接收的字段
	protected $updateFields = array('id','role_name');

	//定义表单验证规则
	protected $_validate = array(
		array('role_name', 'require', '角色名称不能为空！', 1, 'regex', 3),
		array('role_name', '1,30', '角色名称的值最长不能超过 30 个字符！', 1, 'length', 3),
		array('role_name', '', '角色名称已经存在', 1, 'unique', 3),
	);
	public function search($pageSize = 20)
	{
		/************************************* 翻页 ****************************************/
		$count = $this->alias('a')->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		/************************************** 取数据 ******************************************/
		$data['data'] = $this -> alias('a')
							  -> field('a.*,GROUP_CONCAT(c.auth_name) AS auth_name')
							  -> join("LEFT JOIN __ROLE_AUTH__ b ON a.id=b.role_id
							  		   LEFT JOIN __AUTH__ c ON b.auth_id=c.id")
		                      -> limit($page->firstRow.','.$page->listRows)
		                      -> group('a.id')
		                      -> select();
		return $data;
	}

	//修改前执行
	protected function _before_update($data,$option)
	{
		$id = $option['where']['id'];
		$aids = I('post.auth_id');//接收权限id
		//删除角色权限表该id原有的记录
		$raModel = M('role_auth');
		$raModel -> where("role_id=$id") -> delete();
		//重新添加
		foreach($aids as $k => $v)
		{
			$raModel -> add(array(
				'auth_id' => $v,
				'role_id' => $id,
			));
		}
	}

	//删除前执行
	protected function _before_delete($option)
	{
		$id = $option['where']['id'];

		//删除角色对应的权限
		$raModel = M('role_auth');
		$raModel -> where('role_id='.$id) -> delete();

		//删除管理员对应的角色id的记录
		$arModel = M('admin_role');
		$arModel -> where("role_id=$id") -> delete();

	}
	//添加后执行
	protected function _after_insert($data,$option)
	{
		//将权限id和角色id添加进角色权限表
		$aids = I('post.auth_id');//接收权限id
		$raModel = M('role_auth');

		//循环添加
		foreach($aids as $k => $v)
		{
			$raModel -> add(array(
				'auth_id' => $v,
				'role_id' => $data['id'],
			));
		}

	}
}