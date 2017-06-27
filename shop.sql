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
	brand_id mediumint unsigned not null default 0 comment '品牌id',
	addtime int unsigned not null   comment '添加时间',
	primary key (id),
	key shop_price(shop_price),
	key addtime(addtime),
	key is_on_sale(is_on_sale),
	key brand_id(brand_id)
)engine=InnoDB default charset=utf8 comment '商品表';

#在商品表加入分类id，并添加外键
alter table goods add cat_id mediumint unsigned not null comment '分类id' after brand_id;
alter table goods add key cat_id(cat_id);


#品牌表
create table brand(
id mediumint unsigned not null auto_increment comment 'Id',
brand_name varchar(30) not null comment '品牌名称',
site_url varchar(150) not null default '' comment '品牌官方地址',
logo varchar(150) not null comment '品牌logo',
primary key (id)
)engine=InnoDB default charset=utf8 comment '品牌';

#会员等级表
create table member_level(
	id mediumint unsigned not null auto_increment comment 'Id',
	level_name char(20) not null comment '级别名称',
	jifen_bottom mediumint unsigned not null  comment '积分下限',
	jifen_top mediumint unsigned not null  comment '积分上限',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '会员等级';

#会员价格表
create table member_price(
	price decimal(10,2) not null comment '会员价格',
	level_id mediumint unsigned not null comment '会员等级id',
	goods_id mediumint unsigned not null comment '商品id',
	key level_id(level_id),
	key goods_id(goods_id)

)engine=InnoDB default charset=utf8 comment '会员价格';

#外键约束
#在删除商品的同时把对应商品的会员价格自动删除掉
alter table member_price add foreign key (goods_id) references goods(id) on delete cascade; 


#商品分类表
create table category(
	id mediumint unsigned not null auto_increment comment 'Id',
	cat_name char(30) not null  comment '分类名称',
	parent_id mediumint unsigned not null default 0 comment '上级分类id,0:顶级分类',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '商品分类';

