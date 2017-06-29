###### 第一次更新：完成商品表的CRUD

###### 第二次更新：优化GoodModel的上传和缩略和删除硬盘上的图片

###### 第三次更新 ： 完成品牌表的CRUD，并将品牌与商品建立一对多的关系

###### 第四次更新 完成商品表与会员等级间多对多关系，并在会员价格增加外键绑定商品表，使得商品删除后对应的会员价格同时删除并添加了商品加入回收站功能

###### 第五次更新 完成分类表的CRUD,添加无限级分类功能，修改分类时不能上次分类不能是自己和子类，删除分类时同时删除其所有子类，并关联上商品表，在商品列表中添加分类搜索功能，当搜索商品的该类或其子类时，商品都会被搜索到

###### 第六次更新 一个商品在正常情况下，不会只有一个分类 如：一个化妆品，可用是个人护理栏目下，也可以属于美妆栏目，所以扩展分类就应运而生：添加扩展分类表 关联商品表和分类表，实现多对多关系，在商品增加和编辑时添加扩展分类并在商品列表显示
并在商品列表页重新修改分类搜索的功能，将扩展分类也考虑进去	
######
```
	
	/*******取数据******/
	$data = $this -> field('a.*,b.brand_name,c.cat_name,GROUP_CONCAT(e.cat_name SEPARATOR "<br />") AS ext_name')
				  -> alias('a')
	              -> join("LEFT JOIN __BRAND__ b ON a.brand_id = b.id
	              		   LEFT JOIN __CATEGORY__ c ON a.cat_id = c.id
	              		   LEFT JOIN __GOODS_CAT__ d ON a.id = d.goods_id
	              		   LEFT JOIN __CATEGORY__ e ON d.cat_id = e.id")
	              -> where($where) 
	              -> order("$orderby $orderway")
	              -> limit($pageObj->firstRow . ',' . $pageObj ->listRows)
	              -> group('a.id')
	              -> select();

```

###### 在数据库增加商品相册表，并在商品的CRUD中添加了商品相册的CRUD，实现了多文件上传功能和ajax删除相册图片