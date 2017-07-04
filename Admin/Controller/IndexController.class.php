<?php
namespace Admin\Controller;
use Think\Controller;
/*------------------------
 |       首页控制器       |
 ------------------------*/
class IndexController extends InitController {
    public function index(){
        $this->display();
    }

    public function top()
    {
    	$this -> display();
    }

    public function main()
    {
    	$this -> display();
    }

    public function menu()
    {   

        //返回该管理员的前两级权限
        $authModel = D('auth');
        $btns = $authModel -> getBtns();

        $this -> assign('btns',$btns);

    	$this -> display();
    }
}