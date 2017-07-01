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

#在商品表添加类型id 并添加外键
alter table goods add type_id mediumint unsigned not null comment '类型id' after cat_id;
alter table goods add key type_id(type_id);


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


#扩展分类表 ----商品表和分类表的中间表
create table goods_cat(
	cat_id mediumint unsigned not null comment '分类id',
	goods_id mediumint unsigned not null comment '商品id',
	key cat_id(cat_id),
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '扩展分类';


#商品相册表
create table goods_pic(
	id mediumint unsigned not null auto_increment comment 'ID',
	pic varchar(100) not null  comment '原图',
	big_pic varchar(100) not null  comment '大图',
	mid_pic varchar(100) not null  comment '中图',
	sm_pic varchar(100) not null  comment '小图',
	goods_id mediumint unsigned not null comment '商品id',
	primary key (id),
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '商品相册';

/*********************属性相关表**********************/
#	关系：  
#		类型表关联商品表
#		属性表关联类型表
#		商品属性表关联 属性表和商品表
#		库存量表关联商品属性表和商品表

#类型表
create table type(
	id mediumint unsigned not null auto_increment comment 'id',
	type_name varchar(100) not null comment '类型名称',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '类型';

#属性表
create table attr(
	id mediumint unsigned not null auto_increment comment 'id',
	attr_name char(20) not null comment '属性名',
	attr_type enum('唯一','可选') not null  comment '属性类型'
	attr_option_values varchar(200) not null default '' comment '属性可选值',
	type_id mediumint unsigned not null comment '所属类型id',
	primary key (id),
	key type_id(type_id)
)engine=InnoDB default charset=utf8 comment '属性';

#商品属性表
create table goods_attr(
	id mediumint unsigned not null auto_increment comment 'id',
	attr_value varchar(150) not null default '' comment '属性值',
	attr_id mediumint unsigned not null comment '属性id',
	goods_id mediumint unsigned not null comment '商品id',
	primary key (id),
	key goods_id(goods_id),
	key attr_id(attr_id)

)engine=InnoDB default charset=utf8 comment '商品属性';

#商品库存量表
create table goods_number(
	goods_id mediumint unsigned not null comment '商品id',
	goods_number mediumint unsigned not null comment '库存量',
	attr_id mediumint unsigned not null default '0' comment '属性id',
	goods_attr_id varchar(150) not null comment '商品属性id,如果有多个以,分割到此字段',
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '库存量';
