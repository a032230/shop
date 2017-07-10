<?php  
namespace Home\Controller;
use Think\Controller;
/*--------------------------
|         会员控制器        |
--------------------------*/
class MemberController extends Controller{

	//ajax判断登陆状态，实现局部不缓存
	public function checkLogin()
	{
		if(IS_AJAX){
			if(session('m_id')){
				echo json_encode(array(
						'login' => 1,
						'username' => session('m_user'),
					));
			}else{
				echo json_encode(array('login' => 0));
			}
		}

	}

	//绘制验证码
	public function chkcode()
	{
		$verify = new \Think\Verify(array(
				'fontSize' => 30,
				'length' => 2,
				'useNoise' => false,//是否开启杂点
			));
		$verify ->entry();
	}

	//登陆
	public function login()
	{
		if(IS_POST){
			$model = D('Admin/Member');
			//验证自定义表单规则
			if($model -> validate($model -> _login_validate) -> create()){
				if($model -> login())
				{
					$this -> success('登陆成功！',U('Index/index'));
					exit;
				}
			}
			//以上执行失败返回错误
			$this -> error($model -> getError());
		}

		$this -> display();
	}

	//注册
	public function regist()
	{	
		if(IS_POST){
			$model = D('Admin/Member');
			if($model -> create(I('post.'),1))
			{
				if($model -> add())
				{
					$this -> success('注册成功！',U('login'));
					exit;
				}
			}
			//执行失败
			$this -> error($model -> getError());
		}

		$this -> display();
	}

	//退出
	public function logout()
	{
		session(null);
		redirect('/');
	}
}