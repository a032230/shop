<?php
namespace Home\Controller;
use Think\Controller;

/*--------------------------
|         首页控制器        |
--------------------------*/
class IndexController extends NavController {

    //处理浏览历史
    public function displayHistory()
    {
        if(IS_AJAX)
        {
            $id = I('get.id');
            //从cookie中取出浏览历史的id
            //history反序列化成数组
            $data = isset($_COOKIE['history']) ? unserialize($_COOKIE['history']) : array();
            //将最新浏览的商品放到数组的第一位
            array_unshift($data, $id);
            //去除重复的商品
            $data = array_unique($data);

            //存放数组的前6个
            if(count($data) > 6)
            {
                $data = array_slice($data, 0,6);
            }

            //存回cookie,cookie中不能存放数组，所以序列化存入
            setcookie('history',serialize($data),time() + 30 * 86400,'/');//保存一个月

            //根据商品id返回商品详细信息,并且与数组中的位置一样
            $data = implode(',',$data);
            $model = M('goods');
            $gdata = $model -> field('id,mid_logo,goods_name')
                            -> where(array(
                                'id' => array('IN',$data),
                                'is_on_sale' => array('eq','是'),
                            ))
                            -> order("FIELD(id,$data)")
                            -> select();

            //返回json数据
            echo json_encode($gdata);
        }
    }


    //首页
    public function index(){

        //测试并发代码
        // $file = uniqid();
        // file_put_contents("./test/$file", '123');


        $goodsModel = D('Admin/goods');
        //获得正在促销商品
        $proData = $goodsModel -> getPromoteGoods();
        //获得热卖商品
        $hotData = $goodsModel -> getTypeGoods('is_hot');
        //获得精品推荐商品
        $bestData = $goodsModel -> getTypeGoods('is_best');
        //获得最新上架商品
        $newData = $goodsModel -> getTypeGoods('is_new');
        //获得楼层数据
        $catModel = D('Admin/category');
        $floor = $catModel -> floorData();
        // p($floor);
    	//配置页面变量
    	$this -> assign(array(
    		'_show_nav' => 1,
    		'_page_title' => '首页',
    		'_page_keywords' => '首页',
    		'_page_description' => '首页',
            'proData' => $proData,
            'hotData' => $hotData,
            'bestData' => $bestData,
            'newData' => $newData,
            'floor' => $floor,
    	));
        $this -> display();
    }


    //商品详情页
    public function goods()
    {   
        $id = I('get.id');//获得商品id
        $model = M('goods');
        //获取商品详细信息
        $data = $model -> find($id);
        //获取商品的相册
        $gpModel = M('goods_pic');
        $gpic = $gpModel -> where("goods_id = $id") -> select();
        //获取商品的所有属性
        $gaModel = M('goods_attr');
        $gaData = $gaModel -> alias('a')
                           -> field('a.*,b.attr_name,b.attr_type')
                           -> join("LEFT JOIN __ATTR__ b ON a.attr_id=b.id")
                           -> where("a.goods_id=$id")
                           -> select();
        //将可选和唯一属性分开存放
        $uniArr = array(); //存放唯一
        $mulArr = array(); //存放可选
        foreach($gaData as $k => $v)
        {
            if($v['attr_type'] == '可选')
            {
                $mulArr[$v['attr_name']][] = $v;
            }
            else
            {
                $uniArr[] = $v;
            }
        }

        //获取该商品的会员价格
        $mpModel = M('member_price');
        $mpData = $mpModel -> alias('a')
                           -> field('a.price,b.level_name')
                           -> join("LEFT JOIN __MEMBER_LEVEL__ b ON a.level_id = b.id")
                           -> where("a.goods_id=$id")
                           -> select();

        //取出整个分类下所以的上级分类制作面包屑导航
        $catModel = D("Admin/category");
        $path = $catModel -> parentPath($data['cat_id']);
        //反转数据输出面包屑
        $path = array_reverse($path);

    	//配置页面信息
    	$this -> assign(array(
    		'_show_nav' => 0,
    		'_page_title' => '商品详情页',
    		'_page_keywords' => '商品详情页',
    		'_page_description' => '商品详情页',
            'data' => $data,
            'path' => $path,
            'gpic' => $gpic,
            'uniArr' => $uniArr,
            'mulArr' => $mulArr,
            'imgPath' => C('IMAGE_CONFIG'),
            'mpData' => $mpData,
    	));
    	$this -> display();
    }
}