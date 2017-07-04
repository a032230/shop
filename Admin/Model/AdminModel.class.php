<?php
namespace Admin\Model;
use Think\Model;

/*--------------------------
|         管理员模型        |
--------------------------*/

class AdminModel extends Model 
{	
	//添加时允许接收的字段
	protected $insertFields = array('username','password','cpassword','chkcode');

	//修改时允许接收的字段
	protected $updateFields = array('id','username','password','cpassword');

	//定义表单验证规则
	protected $_validate = array(
		array('username', 'require', '用户名不能为空！', 1, 'regex', 3),
		array('username', '1,20', '用户名的值最长不能超过 20 个字符！', 1, 'length', 3),
		array('username', '', '用户名已经存在！', 1, 'unique', 3),
		array('password', 'require', '密码不能为空！', 1, 'regex', 1),
		array('password','cpassword','两次输入的密码不一致',1,'confirm',3),
	);

	//定义登陆表单验证
	public $_login_validate = array(
		array('username', 'require', '用户名不能为空！', 1),
		array('password', 'require', '密码不能为空！', 1),
		array('chkcode', 'require', '验证码不能为空！', 1),
		array('chkcode', 'check_verify', '验证码错误！', 1,'callback'),
	);

	//验证验证码是否正确
	function check_verify($code,$id = '')
	{
		$verify = new \Think\Verify();
		return $verify -> check($code,$id);
	}


	//验证登陆
	public function login()
	{	
		//从模型中获取账号和密码
		$username = $this -> username;
		$password = $this -> password;
		//查询用户是否存在
		$user = $this-> where("username='$username'") -> find();
		if($user){
			if($user['password'] == md5($username . $password)){
				//登陆成功,保存session
				session('id',$user['id']);
				session('username',$user['username']);
				return true;
			}else{
				$this -> error = '密码不正确！';
				return false;
			}
		}else{

			$this -> error = '用户名不存在！';
			return false;
		}
	}


	//分页、搜索
	public function search($pageSize = 20)
	{
		/**************************************** 搜索 ****************************************/
		$where = array();
		if($username = I('get.username'))
			$where['a.username'] = array('like', "%$username%");

		/************************************* 翻页 ****************************************/
		$count = $this->alias('a')->where($where)->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();

		/************************************** 取数据 ******************************************/
		$data['data'] = $this -> alias('a')
							  -> field('a.*,GROUP_CONCAT(c.role_name) AS role_name')
							  -> join("LEFT JOIN __ADMIN_ROLE__ b ON a.id=b.admin_id
							  		   LEFT JOIN __ROLE__ c ON b.role_id=c.id")
		                      -> where($where)
		                      -> group('a.id')
		                      ->limit($page->firstRow.','.$page->listRows)
		                      ->select();
		// echo $this -> getLastSql();die;
		return $data;
	}

	//添加前
	protected function _before_insert(&$data,$option)
	{
		//密码以用户名和密码组合进行MD5后再执行添加
		$data['password'] = md5($data['username'].$data['password']);
	}

	//删除前
	protected function _before_delete($option)
	{	
		$id = $option['where']['id'];
		//超级管理员不能被删除
		if($id == 1){
			$this -> error = '超级管理员无法删除';
			return false;
		}

		//将存在管理员角色表中对应的管理员数据删除
		$arModel = M('admin_role');
		$arModel -> where("admin_id = $id") -> delete();
	}

	//修改前
	protected function _before_update(&$data,$option)
	{
		$id = $option['where']['id'];
		if($data['password'])
		{
			//密码以用户名和密码组合进行MD5后再执行添加
			$data['password'] = md5($data['username'] . $data['password']);
		}
		else
		{
			//密码为空则不进行修改
			unset($data['password']);
		}

		if($id == 1){
			$this -> error = "超级管理员无法分配角色,默认拥有所有权限";
			return false;
		}else{
			//清空管理员角色表对应的管理员id的记录
			$arModel = M('admin_role');
			$arModel -> where("admin_id=$id") -> delete();

			//重新添加
			$rid = I('post.role_id');//接收角色id
			if($rid)
			{
				foreach($rid as $v)
				{
					$arModel -> add(array(
						'role_id' => $v,
						'admin_id' => $id,
					));
				}
			}
		}
	}

	//添加后
	protected function _after_insert($data,$option)
	{
		//将管理员id和角色id写入admin_role表
		$rid = I('post.role_id');//接收角色id
		$arModel = M('admin_role');
		if($rid)
		{
			foreach($rid as $v)
			{
				$arModel -> add(array(
					'role_id' => $v,
					'admin_id' => $data['id'],
				));
			}
		}
	}
}