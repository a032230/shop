<?php
namespace Admin\Model;
use Think\Model;
/*--------------------------
|         权限模型          |
--------------------------*/
class AuthModel extends Model 
{	
	//添加时允许接收的字段
	protected $insertFields = array('auth_name','module_name','controller_name','action_name','parent_id');

	//修改时允许接收的字段
	protected $updateFields = array('id','auth_name','module_name','controller_name','action_name','parent_id');

	//定义表单验证规则
	protected $_validate = array(
		array('auth_name', '1,30', '权限名称的值最长不能超过 30 个字符！', 2, 'length', 3),
		array('module_name', '1,30', '模块名的值最长不能超过 30 个字符！', 2, 'length', 3),
		array('controller_name', '1,30', '控制器名的值最长不能超过 30 个字符！', 2, 'length', 3),
		array('action_name', '1,30', '方法名的值最长不能超过 30 个字符！', 2, 'length', 3),
		array('parent_id', 'number', '上级权限id必须是一个整数！', 2, 'regex', 3),
	);
	/************************************* 递归相关方法 *************************************/
	public function getTree()
	{
		$data = $this->select();
		return $this->_reSort($data);
	}
	private function _reSort($data, $parent_id=0, $level=0, $isClear=TRUE)
	{
		static $ret = array();
		if($isClear)
			$ret = array();
		foreach ($data as $k => $v)
		{
			if($v['parent_id'] == $parent_id)
			{
				$v['level'] = $level;
				$ret[] = $v;
				$this->_reSort($data, $v['id'], $level+1, FALSE);
			}
		}
		return $ret;
	}
	public function getChildren($id)
	{
		$data = $this->select();
		return $this->_children($data, $id);
	}
	private function _children($data, $parent_id=0, $isClear=TRUE)
	{
		static $ret = array();
		if($isClear)
			$ret = array();
		foreach ($data as $k => $v)
		{
			if($v['parent_id'] == $parent_id)
			{
				$ret[] = $v['id'];
				$this->_children($data, $v['id'], FALSE);
			}
		}
		return $ret;
	}

	public function checkAuth()
	{
		$admin_id = session('id');
		//判断是否是超级管理员
		if($adminId == 1){
			return true;
		}

		$arModel = M('admin_role');
		//查询当前管理员是否有权限访问当前的模型，控制器，方法；
		$res = $arModel -> alias('a')
						-> join("LEFT JOIN __ROLE_AUTH__ b ON a.role_id=b.role_id
								 LEFT JOIN __AUTH__ c ON b.auth_id=c.id")
						-> where(array(
								'a.admin_id' => array('eq',$admin_id),
								'c.module_name' => array('eq',MODULE_NAME),
								'c.controller_name' => array('eq',CONTROLLER_NAME),
								'c.action_name' => array('eq',ACTION_NAME),
							))
						-> count();
		// echo $arModel -> getLastSql();die;
		return $res > 0;
	}

	//删除之前
	public function _before_delete($option)
	{
		$id = $option['where']['id'];
		// 先找出所有的子分类
		$children = $this->getChildren($id);
		// 如果有子分类都删除掉
		if($children)
		{
			$this->error = '有下级数据无法删除';
			return FALSE;
		}

		//当删除该权限时在角色权限表中也删掉对应的权限
		$raModel = M('role_auth');
		$raModel -> where("auth_id = $id") -> delete();
	}
}