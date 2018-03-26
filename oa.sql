-- oa users表
create table users(
	id int unsigned not null auto_increment comment '主键id',
	open_id varchar(64) not null default '' comment 'openid',
	unionid varchar(255) not null default '' comment 'unionid',
	sex tinyint(1) unsigned not null default '0' comment '0女 1男',
	user_name varchar(200) not null default '' comment '名称',
	nickname varchar(200) not null default '' comment '昵称',
	avatar varchar(150) not null default '' comment '头像',
	phone varchar(30) not null  default '' comment '电话',
	pay_qrcode varchar(150) not null default '' comment '付款二维码',
	create_time  int unsigned not null  comment '创建的时间',
	update_time  int unsigned not null  comment '修改的时间',
	primary key(id)
)engine=innodb default charset=utf8 auto_increment=1000 comment '用户表';

-- oa access_token 表
create table access_token(
	id int comment 'id',
	accesstoken varchar(200) not null default '' comment 'access_token',
	expire_time int unsigned not null  default 0  comment '过期时间',
	primary key(id)
)engine=innodb default charset=utf8 auto_increment=1000;