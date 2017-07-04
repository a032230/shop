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


	/**
	 * [getChildren  获取该分类id下的所有子类id
	 * @param  [type] $catid [传递进来的分类id]
	 * @return [array]        [返回所有该分类下的所有子类id]
	 */
	public function getChildren($catid)
	{
		$data = $this -> select();

		return $this -> _getChildren($data,$catid,TRUE);
	}

	/**
	 * [_getChildren 递归查找所有子类
	 * @param  [array]  $data     [分类数据]
	 * @param  [int]  $catid    [分类id]
	 * @param  boolean $is_clean [是否清空$ret]
	 * @return [$ret]  array     [该分类id下的所有子类id]
	 */
	private function _getChildren($data,$catid,$is_clean = FALSE)
	{
		static $ret = array(); //定义静态数组用于存放子类id

		//首次调用，先清空数组，防止多次调用该方法时保存上次继承的数据
		if($is_clean)
		{
			$ret = array();
		}

		//遍历分类数据获取所有子类id
		foreach($data as $v){
			if($v['parent_id'] == $catid){
				$ret[] = $v['id'];

				//再查找该$v['id']下的子类
				$this -> _getChildren($data,$v['id']);
			}
		}
		return $ret;
	}

	/**
	 * [getTree 打印树状结构数据
	 * @return [array] [标记好分类等级的数据]
	 */
	public  function getTree()
	{
		$data = $this -> select();
		return $this -> _getTree($data);
	}


	/**
	 * [_getTree 为所有分类标记等级
	 * @param  [type]  $data      [分类数据]
	 * @param  integer $parent_id [父级id]
	 * @param  integer $level     [分类等级]
	 * @return [array] $ret       [所有标记好等级的分类数据]
	 */
	private function _getTree($data,$parent_id = 0 ,$level = 0)
	{
		static $ret = array();

		foreach($data as $v){
			//查找出所有的顶级id
			if($v['parent_id'] == $parent_id){
				//标记该分类等级
				$v['level'] = $level;
				$ret[] = $v;

				$this -> _getTree($data,$v['id'],$level + 1);
			}
		}

		return $ret;
	}

	/**
	 * [checkAuth 验证管理员权限
	 * @return [bool] [$res]
	 */
	public function checkAuth()
	{
		$admin_id = session('id');
		//判断是否是超级管理员
		if($admin_id == 1){
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

	/**
	 * [getBtns 获得管理员所拥有的前两级权限
	 * @return [array] [前两级权限]
	 */
	public function getBtns()
	{
		$admin_id = session('id');
		//如果是超级管理员直接返回所有权限
		if($admin_id == 1)
		{
			$authModel = M('auth');
			$authData = $authModel -> select();
		}
		else
		{	
			//获得当前管理员所拥有的权限
			$arModel = M('admin_role');
			$authData = $arModel -> alias('a') 
			                     -> field('DISTINCT c.*')
			                     -> join("LEFT JOIN __ROLE_AUTH__ b ON a.role_id =b.role_id
			                     		  LEFT JOIN __AUTH__ c ON b.auth_id= c.id")
			                     ->where(array('admin_id' => array('eq',$admin_id)))
			                     ->select();
		}

		//挑出前两级权限
		$btns = array();
		foreach($authData as $k => $v)
		{	
			//找出顶级
			if($v['parent_id'] == 0)
			{	
				//找出顶级的子级
				foreach($authData as $k1 => $v1)
				{
					if($v1['parent_id'] == $v['id'])
					{
						$v['children'][] = $v1;
					}
				}

				$btns[] = $v;
			}
		}

		//返回该管理员的前两级权限
		return $btns;
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