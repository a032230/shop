create database shop;


#商品详情页图 350*350
#商品详情页小图 50*50
#商品详情页放大镜 700*700
#列表页图 130*130
create table goods
(
	id mediumint unsigned not null auto_increment comment 'Id',
	goods_name varchar(100) not null comment '商品名称',
	market_price decimal(10,2) not null comment '市场价格',
	shop_price decimal(10,2) not null comment '本店价格',
	goods_desc text not null comment '商品描述', 
	logo varchar(150) not null default '' comment '原图',	
	sm_logo varchar(150) not null default '' comment '小图',	
	mid_logo varchar(150) not null default '' comment '中图',	
	big_logo varchar(150) not null default '' comment '大图',	
	mbig_logo varchar(150) not null default '' comment '特大图',	
	is_on_sale enum('是','否') not null default '是' comment '是否上架',
	is_delete enum('是','否') not null default '否' comment '是否放入回收站',
	addtime int unsigned not null   comment '添加时间',
	primary key (id),
	key shop_price(shop_price),
	key addtime(addtime),
	key is_on_sale(is_on_sale)
)engine=InnoDB default charset=utf8 comment '商品表';