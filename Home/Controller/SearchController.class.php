<?php
namespace Home\Controller;
use Think\Controller;

//前台搜索控制器
class SearchController extends NavController{

	//分类搜索
	public function search_cat()
	{
		$catId = I('get.cat_id');
		

		$gModel = D('Admin/goods');
		$gData = $gModel -> cat_search($catId);	

		$caModel = D('Admin/category');
		//返回商品筛选条件
		$data = $caModel -> searchConditionByGoodsId($gData['goods_id']); 
		// p($gData);	
		//配置页面信息
    	$this -> assign(array(
    		'_show_nav' => 0,
    		'_page_title' => '分类搜索',
    		'_page_keywords' => '分类搜索页',
    		'_page_description' => '分类搜索页',
    		'data' => $data,
    		'page' => $gData['page'],
    		'gData' => $gData['data'],
    	));
		$this -> display();
	}

	// 关键字搜索
    public function key_search()
    {
    	$key = I('get.key');
   
    	// 取出商品和翻页
    	$goodsModel = D('Admin/goods');
    	$data = $goodsModel->key_search($key);
    	//p($data);
    	// 根据上面搜索出来的商品计算筛选条件
    	$catModel = D('Admin/category');
    	$searchFilter = $catModel->searchConditionByGoodsId($data['goods_id']);
    	
    	// 设置页面信息
    	$this->assign(array(
    		'page' => $data['page'],
    		'data' => $data['data'],
    		'searchFilter' => $searchFilter,
    		'_page_title' => '关键字搜索页',
    		'_page_keywords' => '关键字搜索页',
    		'_page_description' => '关键字搜索页',
    	));
    	$this->display();
    }
}
