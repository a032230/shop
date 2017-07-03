<?php 
namespace Admin\Controller;
use Think\Controller;

/*--------------------------
|         登陆控制器        |
--------------------------*/

class LoginController extends Controller
{


	//生成验证码
	public function chkcode()
	{
		//验证码配置
		$config = array(
			    'fontSize'    =>    30,    // 验证码字体大小
			    'length'      =>    2,     // 验证码位数
			    'useNoise'    =>    true, // 关闭验证码杂点
			);
		$Verify = new \Think\Verify($config);
		// $Verify->useImgBg = true;//使用背景图片
		$Verify->entry();
	}

	//登陆
	public function login()
	{
		if(IS_POST)
		{

			$model = D('admin');
			//验证自定义的表单验证
			if($model -> validate($model -> _login_validate) -> create())
			{
				if($model -> login())
				{
					$this -> success('登陆成功',U('Index/index'));
					exit;
				}
			}

			//以上执行失败
			$this -> error($model -> getError());
		}

		$this -> display();
	}


	//退出
	public function logout()
	{
		session(null);
		$this -> success('退出成功！',U('login'));
		exit;
	}


}