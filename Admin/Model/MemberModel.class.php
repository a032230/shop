<?php  
namespace Admin\Model;
use Think\Model;

/*--------------------------
|         会员模型          |
--------------------------*/
class MemberModel extends Model{

	//定义添加时允许接收的字段
	protected $insertFields = "username,password,cpassword,chkcode,must_click";
	//定义修改时允许接收的字段
	protected $updateFields = "id,username,password,cpassword";

	//定义表单验证规则
	protected $_validate = array(
			array('username','require','用户名不能为空',1),
			array('username','1,30','用户名不能超过30个字符',1,'length',3),
			array('username','','用户名已经存在',1,'unique',3),
			array('password','require','密码不能为空',1),
			array('cpassword','password','两次输入的密码不一致',1,'confirm',3),
			array('chkcode','require','验证码不能为空',1),
			array('chkcode','check_verify','验证码不正确',1,'callback',3),
			array('must_click','require','必须同意注册协议',1),
		);

	//定义登陆表单的验证规则
	public $_login_validate = array(
			array('username','require','用户名不能为空',1),
			array('password','require','密码不能为空',1),
			array('chkcode','require','验证码不能为空',1),
			array('chkcode','check_verify','验证码不正确',1,'callback',3),
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
		//从模型中获取用户名和密码
		$username = $this -> username;
		$password = $this -> password;

		//查询用户名是否存在
		$user = $this -> where(array('username' => array('eq',$username))) -> find();

		if($user)
		{
			if($user['password'] == md5($username . $password))
			{
				//登陆成功记录session
				session('m_id',$user['id']);
				session('m_user',$user['username']);

				//计算当前会员等级id并存入session
				$mlModel = M('member_level');
				$level_id = $mlModel -> field('id') 
									 -> where(array(
									 	'jifen_top' => array('egt',$user['jifen']),
									 	'jifen_bottom' => array('elt',$user['jifen'])
									 	))
									 ->find();
				session('level_id',$level_id['id']);

				//登陆成功后将cookie中的购物车数据存入数据库
				$cartModel = D('Home/cart');
				$cartModel -> moveDataToDb();

				return true;
			}
			else
			{
				$this -> error = '密码不正确!';
				return false;
			}
		}
		else
		{
			$this -> error = '用户名不存在!';
			return false;
		}
	}


	//添加之前
	protected function _before_insert(&$data,$option)
	{
		//MD5加密后存入数据库
		$data['password'] = md5($data['username'] . $data['password']);
	}
}