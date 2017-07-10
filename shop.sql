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
	promote_price decimal(10,2) not null default 0 comment '促销价格',
	promote_start_date datetime not null  comment '促销开始时间',
	promote_end_date datetime not null  comment '促销结束时间',
	is_new enum('是','否') not null default '否' comment '是否新品',	
	is_hot enum('是','否') not null default '否' comment '是否热卖',	
	is_best enum('是','否') not null default '否' comment '是否精品',	
	is_floor enum('是','否') not null default '否' comment '是否推荐到楼层',	
	brand_id mediumint unsigned not null default 0 comment '品牌id',
	cat_id mediumint unsigned not null comment '分类id',
	type_id mediumint unsigned not null comment '类型id',
	addtime int unsigned not null   comment '添加时间',
	sort_num tinyint unsigned not null default 100 comment '排序权重',
	primary key (id),
	key shop_price(shop_price),
	key addtime(addtime),
	key is_on_sale(is_on_sale),
	key brand_id(brand_id),
	key promote_price(promote_price),
	key promote_start_date(promote_start_date),
	key promote_end_date(promote_end_date),
	key is_new(is_new),
	key is_hot(is_hot),
	key is_best(is_best),
	kye sort_num(sort_num)
)engine=InnoDB default charset=utf8 comment '商品表';

alter table goods add is_floor enum('是','否') not null default '否' comment '是否推荐到楼层' after is_best;
#在商品表加入分类id，并添加外键
alter table goods add cat_id mediumint unsigned not null comment '分类id' after brand_id;
alter table goods add key cat_id(cat_id);

#在商品表添加类型id 并添加外键
alter table goods add type_id mediumint unsigned not null comment '类型id' after cat_id;
alter table goods add key type_id(type_id);
#在商品表添加促销价格 并添加外键
alter table goods add promote_price decimal(10,2) not null default 0 comment '促销价格' after is_delete;
alter table goods add key promote_price(promote_price);
#在商品表添加促销开始时间 并添加外键
alter table goods add promote_start_date datetime not null  comment '促销开始时间' after promote_price;
alter table goods add key promote_start_date(promote_start_date)
#在商品表添加促销结束时间并添加外键
alter table goods add promote_end_date datetime not null  comment '促销结束时间' after promote_start_date;
alter table goods add key promote_end_date(promote_end_date);
#在商品表添加是否新品并添加外键
alter table goods add is_new enum('是','否') not null default '否' comment '是否新品' after promote_end_date;
alter table goods add key is_new(is_new);
#在商品表添加是否热卖并添加外键
alter table goods add is_hot enum('是','否') not null default '否' comment '是否热卖' after is_new;
alter table goods add key is_hot(is_hot);
#在商品表添加是否精品并添加外键
alter table goods add is_best enum('是','否') not null default '否' comment '是否精品' after is_hot;
alter table goods add key is_best(is_best);

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
	is_floor enum('是','否') not null default '否' comment '是否推荐到楼层',	
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
	goods_attr_id varchar(150) not null comment '商品属性id,如果有多个以,分割到此字段',
	key goods_id(goods_id)
)engine=InnoDB default charset=utf8 comment '库存量';


/*********************RBAC相关表**************************/

#权限表
create table auth(
	id mediumint unsigned not null auto_increment comment '主键id',
	auth_name char(30) not null default '' comment '权限名称',
	module_name char(30) not null default '' comment '模块名',
	controller_name char(30) not null default '' comment '控制器名',
	action_name char(30) not null default '' comment '方法名',
	parent_id mediumint unsigned not null default '0' comment '上级权限id',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '权限';

#角色表
create table role(
	id mediumint unsigned not null auto_increment comment '主键id',
	role_name char(30) not null comment '角色名称',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '角色';

#管理员表
create table admin(
	id mediumint unsigned not null auto_increment comment '主键id',
	username char(20) not null comment '用户名',
	password char(32) not null comment '密码',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '管理员';

#管理员表默认有超级管理[不能删除,拥有所有权限]
insert into admin (id,username,password) values(1,'root','6d7983710a288289c27687b91e9d706f')
#角色和权限中间表 --> 角色权限表
create table role_auth(
	auth_id mediumint unsigned not null comment '权限id',
	role_id mediumint unsigned not null comment '角色id',
	key auth_id(auth_id),
	key role_id(role_id)
)engine=InnoDB default charset=utf8 comment '角色权限';

#角色和管理员中级表 --> 管理员角色表
create table admin_role(
	admin_id mediumint unsigned not null comment '管理员id',
	role_id mediumint unsigned not null comment '角色id',
	key admin_id(admin_id),
	key role_id(role_id)
)engine=InnoDB default charset=utf8 comment '管理员角色';


/*****************前台相关表******************/
#会员表
create table member(
	id mediumint unsigned not null auto_increment comment '主键id',
	username char(30) not null comment '用户名',
	password char(32) not null comment '密码',
	face varchar(150) not null default '' comment '会员头像',
	jifen mediumint unsigned not null default 0 comment '会员积分',
	primary key (id)
)engine=InnoDB default charset=utf8 comment '会员';

#购物车表
create table cart(
	id mediumint unsigned not null auto_increment comment '主键id',
	goods_id mediumint unsigned not null  comment '商品id',
	goods_attr_id varchar(150) not null default '' comment '商品属性id',
	goods_number tinyint unsigned not null default 0 comment '商品数量',
	member_id mediumint unsigned not null comment '会员id',
	primary key (id),
	key member_id(member_id)
)engine=InnoDB default charset=utf8 comment '购物车';