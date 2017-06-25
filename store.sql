/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : store

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2017-06-25 16:19:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for yz_ad
-- ----------------------------
DROP TABLE IF EXISTS `yz_ad`;
CREATE TABLE `yz_ad` (
  `ad_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '广告id',
  `ad_name` varchar(255) NOT NULL COMMENT '广告名称',
  `ad_content` text NOT NULL COMMENT '广告内容',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  PRIMARY KEY (`ad_id`),
  KEY `idx_ad_name` (`ad_name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_app_ad
-- ----------------------------
DROP TABLE IF EXISTS `yz_app_ad`;
CREATE TABLE `yz_app_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(200) NOT NULL,
  `skip_type` tinyint(1) NOT NULL COMMENT '0:url;1:商品详情',
  `aim_id` int(11) NOT NULL COMMENT '商品详情目标ID',
  `url` varchar(200) NOT NULL,
  `listorder` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  `slide_name` varchar(100) NOT NULL COMMENT '幻灯片名称',
  `slide_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_asset
-- ----------------------------
DROP TABLE IF EXISTS `yz_asset`;
CREATE TABLE `yz_asset` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户 id',
  `key` varchar(50) NOT NULL COMMENT '资源 key',
  `filename` varchar(50) DEFAULT NULL COMMENT '文件名',
  `filesize` int(11) DEFAULT NULL COMMENT '文件大小,单位Byte',
  `filepath` varchar(200) NOT NULL COMMENT '文件路径，相对于 upload 目录，可以为 url',
  `uploadtime` int(11) NOT NULL COMMENT '上传时间',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1：可用，0：删除，不可用',
  `meta` text COMMENT '其它详细信息，JSON格式',
  `suffix` varchar(50) DEFAULT NULL COMMENT '文件后缀名，不包括点',
  `download_times` int(11) NOT NULL DEFAULT '0' COMMENT '下载次数',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='资源表';

-- ----------------------------
-- Table structure for yz_auth_access
-- ----------------------------
DROP TABLE IF EXISTS `yz_auth_access`;
CREATE TABLE `yz_auth_access` (
  `role_id` mediumint(8) unsigned NOT NULL COMMENT '角色',
  `rule_name` varchar(255) NOT NULL COMMENT '规则唯一英文标识,全小写',
  `type` varchar(30) DEFAULT NULL COMMENT '权限规则分类，请加应用前缀,如admin_',
  KEY `role_id` (`role_id`),
  KEY `rule_name` (`rule_name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限授权表';

-- ----------------------------
-- Table structure for yz_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `yz_auth_rule`;
CREATE TABLE `yz_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '规则所属module',
  `type` varchar(30) NOT NULL DEFAULT '1' COMMENT '权限规则分类，请加应用前缀,如admin_',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识,全小写',
  `param` varchar(255) DEFAULT NULL COMMENT '额外url参数',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`status`,`type`)
) ENGINE=MyISAM AUTO_INCREMENT=174 DEFAULT CHARSET=utf8 COMMENT='权限规则表';

-- ----------------------------
-- Table structure for yz_bank
-- ----------------------------
DROP TABLE IF EXISTS `yz_bank`;
CREATE TABLE `yz_bank` (
  `bank_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '银行名称',
  `code` varchar(60) NOT NULL,
  `icon` varchar(180) NOT NULL,
  `listorder` mediumint(8) NOT NULL DEFAULT '0' COMMENT '优先级',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效，1-有效，0-无效',
  PRIMARY KEY (`bank_id`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8 COMMENT='银行表';

-- ----------------------------
-- Table structure for yz_comments
-- ----------------------------
DROP TABLE IF EXISTS `yz_comments`;
CREATE TABLE `yz_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_table` varchar(100) NOT NULL COMMENT '评论内容所在表，不带表前缀',
  `post_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评论内容 id',
  `url` varchar(255) DEFAULT NULL COMMENT '原文地址',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '发表评论的用户id',
  `to_uid` int(11) NOT NULL DEFAULT '0' COMMENT '被评论的用户id',
  `full_name` varchar(50) DEFAULT NULL COMMENT '评论者昵称',
  `email` varchar(255) DEFAULT NULL COMMENT '评论者邮箱',
  `createtime` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '评论时间',
  `content` text NOT NULL COMMENT '评论内容',
  `type` smallint(1) NOT NULL DEFAULT '1' COMMENT '评论类型；1实名评论',
  `parentid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '被回复的评论id',
  `path` varchar(500) DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1' COMMENT '状态，1已审核，0未审核',
  PRIMARY KEY (`id`),
  KEY `comment_post_ID` (`post_id`),
  KEY `comment_approved_date_gmt` (`status`),
  KEY `comment_parent` (`parentid`),
  KEY `table_id_status` (`post_table`,`post_id`,`status`),
  KEY `createtime` (`createtime`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='评论表';

-- ----------------------------
-- Table structure for yz_common_action_log
-- ----------------------------
DROP TABLE IF EXISTS `yz_common_action_log`;
CREATE TABLE `yz_common_action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) DEFAULT '0' COMMENT '用户id',
  `object` varchar(100) DEFAULT NULL COMMENT '访问对象的id,格式：不带前缀的表名+id;如posts1表示xx_posts表里id为1的记录',
  `action` varchar(50) DEFAULT NULL COMMENT '操作名称；格式规定为：应用名+控制器+操作名；也可自己定义格式只要不发生冲突且惟一；',
  `count` int(11) DEFAULT '0' COMMENT '访问次数',
  `last_time` int(11) DEFAULT '0' COMMENT '最后访问的时间戳',
  `ip` varchar(15) DEFAULT NULL COMMENT '访问者最后访问ip',
  PRIMARY KEY (`id`),
  KEY `user_object_action` (`user`,`object`,`action`),
  KEY `user_object_action_ip` (`user`,`object`,`action`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='访问记录表';

-- ----------------------------
-- Table structure for yz_goods
-- ----------------------------
DROP TABLE IF EXISTS `yz_goods`;
CREATE TABLE `yz_goods` (
  `goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_sn` bigint(20) NOT NULL,
  `type_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `pre_price` double(11,2) NOT NULL,
  `now_price` double(11,2) NOT NULL,
  `cover` varchar(100) NOT NULL,
  `photos` text NOT NULL,
  `params` text NOT NULL,
  `inventory` int(11) NOT NULL DEFAULT '0',
  `sale_num` int(11) NOT NULL DEFAULT '0',
  `postage` double(11,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0',
  `is_recommend` tinyint(1) NOT NULL DEFAULT '0',
  `is_new` tinyint(1) NOT NULL DEFAULT '0',
  `listorder` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`goods_id`),
  KEY `idx_goods_brand` (`brand_id`) USING BTREE,
  KEY `idx_goods_type` (`type_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_goods_brand
-- ----------------------------
DROP TABLE IF EXISTS `yz_goods_brand`;
CREATE TABLE `yz_goods_brand` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `parent` int(11) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `type` int(2) NOT NULL DEFAULT '0',
  `list_order` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_goods_comments
-- ----------------------------
DROP TABLE IF EXISTS `yz_goods_comments`;
CREATE TABLE `yz_goods_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '发表评论的用户id',
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '被评论的书籍id',
  `content` text NOT NULL COMMENT '评论内容',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '评论类型；1实名评论',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态，1已审核，0未审核',
  `score` tinyint(1) NOT NULL DEFAULT '5' COMMENT '评分；1-5分',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comm_goods` (`goods_id`) USING BTREE,
  CONSTRAINT `fk_comm_goods` FOREIGN KEY (`goods_id`) REFERENCES `yz_goods` (`goods_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for yz_goods_detail
-- ----------------------------
DROP TABLE IF EXISTS `yz_goods_detail`;
CREATE TABLE `yz_goods_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `detail` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gd_goods` (`goods_id`) USING BTREE,
  CONSTRAINT `fk_gd_goods` FOREIGN KEY (`goods_id`) REFERENCES `yz_goods` (`goods_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for yz_goods_type
-- ----------------------------
DROP TABLE IF EXISTS `yz_goods_type`;
CREATE TABLE `yz_goods_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `parent` int(11) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `listorder` int(11) NOT NULL COMMENT '0',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_guestbook
-- ----------------------------
DROP TABLE IF EXISTS `yz_guestbook`;
CREATE TABLE `yz_guestbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(50) NOT NULL COMMENT '留言者姓名',
  `email` varchar(100) NOT NULL COMMENT '留言者邮箱',
  `title` varchar(255) DEFAULT NULL COMMENT '留言标题',
  `msg` text NOT NULL COMMENT '留言内容',
  `createtime` datetime NOT NULL COMMENT '留言时间',
  `status` smallint(2) NOT NULL DEFAULT '1' COMMENT '留言状态，1：正常，0：删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='留言表';

-- ----------------------------
-- Table structure for yz_links
-- ----------------------------
DROP TABLE IF EXISTS `yz_links`;
CREATE TABLE `yz_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) NOT NULL COMMENT '友情链接地址',
  `link_name` varchar(255) NOT NULL COMMENT '友情链接名称',
  `link_image` varchar(255) DEFAULT NULL COMMENT '友情链接图标',
  `link_target` varchar(25) NOT NULL DEFAULT '_blank' COMMENT '友情链接打开方式',
  `link_description` text NOT NULL COMMENT '友情链接描述',
  `link_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  `link_rating` int(11) NOT NULL DEFAULT '0' COMMENT '友情链接评级',
  `link_rel` varchar(255) DEFAULT NULL COMMENT '链接与网站的关系',
  `listorder` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='友情链接表';

-- ----------------------------
-- Table structure for yz_member
-- ----------------------------
DROP TABLE IF EXISTS `yz_member`;
CREATE TABLE `yz_member` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `nick_name` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL,
  `avatar` varchar(200) NOT NULL,
  `sex` tinyint(2) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_member_address
-- ----------------------------
DROP TABLE IF EXISTS `yz_member_address`;
CREATE TABLE `yz_member_address` (
  `addr_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `province` varchar(20) NOT NULL,
  `city` varchar(20) NOT NULL,
  `district` varchar(30) NOT NULL,
  `address` varchar(60) NOT NULL,
  `is_default` tinyint(2) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`addr_id`),
  KEY `idx_ma_member` (`member_id`) USING BTREE,
  CONSTRAINT `fk_ma_member` FOREIGN KEY (`member_id`) REFERENCES `yz_member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_member_cart
-- ----------------------------
DROP TABLE IF EXISTS `yz_member_cart`;
CREATE TABLE `yz_member_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `num` int(3) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0：失效宝贝；1：有效',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mcart_member` (`member_id`) USING BTREE,
  KEY `idx_mcart_goods` (`goods_id`) USING BTREE,
  KEY `idx_mem_goods` (`member_id`,`goods_id`) USING BTREE,
  CONSTRAINT `fk_mcart_goods` FOREIGN KEY (`goods_id`) REFERENCES `yz_goods` (`goods_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_member_collection
-- ----------------------------
DROP TABLE IF EXISTS `yz_member_collection`;
CREATE TABLE `yz_member_collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mcoll_goods` (`goods_id`) USING BTREE,
  KEY `idx_mcoll_member` (`member_id`) USING BTREE,
  CONSTRAINT `fk_mcoll_goods` FOREIGN KEY (`goods_id`) REFERENCES `yz_goods` (`goods_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_member_message
-- ----------------------------
DROP TABLE IF EXISTS `yz_member_message`;
CREATE TABLE `yz_member_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `detail` varchar(500) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mm_member` (`member_id`) USING BTREE,
  CONSTRAINT `fk_mm_member` FOREIGN KEY (`member_id`) REFERENCES `yz_member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_member_third
-- ----------------------------
DROP TABLE IF EXISTS `yz_member_third`;
CREATE TABLE `yz_member_third` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `type` tinyint(2) NOT NULL,
  `uuid` varchar(50) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mthird_member` (`member_id`) USING BTREE,
  KEY `idx_mthird_uuid` (`uuid`) USING BTREE,
  CONSTRAINT `fk_mt_member` FOREIGN KEY (`member_id`) REFERENCES `yz_member` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_menu
-- ----------------------------
DROP TABLE IF EXISTS `yz_menu`;
CREATE TABLE `yz_menu` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `parentid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `app` varchar(30) NOT NULL DEFAULT '' COMMENT '应用名称app',
  `model` varchar(30) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作名称',
  `data` varchar(50) NOT NULL DEFAULT '' COMMENT '额外参数',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '菜单类型  1：权限认证+菜单；0：只作为菜单',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态，1显示，0不显示',
  `name` varchar(50) NOT NULL COMMENT '菜单名称',
  `icon` varchar(50) DEFAULT NULL COMMENT '菜单图标',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `listorder` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序ID',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `parentid` (`parentid`),
  KEY `model` (`model`)
) ENGINE=MyISAM AUTO_INCREMENT=191 DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

-- ----------------------------
-- Table structure for yz_mobile_code_log
-- ----------------------------
DROP TABLE IF EXISTS `yz_mobile_code_log`;
CREATE TABLE `yz_mobile_code_log` (
  `code_id` int(11) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `code` int(6) NOT NULL,
  `count` tinyint(2) NOT NULL,
  `send_time` int(11) NOT NULL,
  `expire_time` int(11) NOT NULL,
  PRIMARY KEY (`code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_nav
-- ----------------------------
DROP TABLE IF EXISTS `yz_nav`;
CREATE TABLE `yz_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL COMMENT '导航分类 id',
  `parentid` int(11) NOT NULL COMMENT '导航父 id',
  `label` varchar(255) NOT NULL COMMENT '导航标题',
  `target` varchar(50) DEFAULT NULL COMMENT '打开方式',
  `href` varchar(255) NOT NULL COMMENT '导航链接',
  `icon` varchar(255) NOT NULL COMMENT '导航图标',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  `listorder` int(6) DEFAULT '0' COMMENT '排序',
  `path` varchar(255) NOT NULL DEFAULT '0' COMMENT '层级关系',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='前台导航表';

-- ----------------------------
-- Table structure for yz_nav_cat
-- ----------------------------
DROP TABLE IF EXISTS `yz_nav_cat`;
CREATE TABLE `yz_nav_cat` (
  `navcid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '导航分类名',
  `active` int(1) NOT NULL DEFAULT '1' COMMENT '是否为主菜单，1是，0不是',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`navcid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='前台导航分类表';

-- ----------------------------
-- Table structure for yz_oauth_user
-- ----------------------------
DROP TABLE IF EXISTS `yz_oauth_user`;
CREATE TABLE `yz_oauth_user` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `from` varchar(20) NOT NULL COMMENT '用户来源key',
  `name` varchar(30) NOT NULL COMMENT '第三方昵称',
  `head_img` varchar(200) NOT NULL COMMENT '头像',
  `uid` int(20) NOT NULL COMMENT '关联的本站用户id',
  `create_time` datetime NOT NULL COMMENT '绑定时间',
  `last_login_time` datetime NOT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(16) NOT NULL COMMENT '最后登录ip',
  `login_times` int(6) NOT NULL COMMENT '登录次数',
  `status` tinyint(2) NOT NULL,
  `access_token` varchar(512) NOT NULL,
  `expires_date` int(11) NOT NULL COMMENT 'access_token过期时间',
  `openid` varchar(40) NOT NULL COMMENT '第三方用户id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='第三方用户表';

-- ----------------------------
-- Table structure for yz_options
-- ----------------------------
DROP TABLE IF EXISTS `yz_options`;
CREATE TABLE `yz_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(64) NOT NULL COMMENT '配置名',
  `option_value` longtext NOT NULL COMMENT '配置值',
  `autoload` int(2) NOT NULL DEFAULT '1' COMMENT '是否自动加载',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='全站配置表';

-- ----------------------------
-- Table structure for yz_order
-- ----------------------------
DROP TABLE IF EXISTS `yz_order`;
CREATE TABLE `yz_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` bigint(20) NOT NULL,
  `member_id` int(11) NOT NULL,
  `postage` double(11,2) NOT NULL,
  `exp_code` varchar(20) NOT NULL,
  `exp_sn` varchar(20) NOT NULL,
  `exp_time` int(11) unsigned NOT NULL,
  `total_price` double(11,2) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `pay_channel` int(11) NOT NULL,
  `remark` varchar(110) NOT NULL,
  `create_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`),
  KEY `idx_order_member` (`member_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_order_address
-- ----------------------------
DROP TABLE IF EXISTS `yz_order_address`;
CREATE TABLE `yz_order_address` (
  `addr_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `province` varchar(20) NOT NULL,
  `city` varchar(20) NOT NULL,
  `district` varchar(30) NOT NULL,
  `address` varchar(60) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`addr_id`),
  KEY `idx_oa_order` (`order_id`) USING BTREE,
  CONSTRAINT `fk_oa_order` FOREIGN KEY (`order_id`) REFERENCES `yz_order` (`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_order_detail
-- ----------------------------
DROP TABLE IF EXISTS `yz_order_detail`;
CREATE TABLE `yz_order_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `pre_price` double(11,2) NOT NULL,
  `now_price` double(11,2) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0:有效 1：库存不足 2：过期3:退款',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_od_goods` (`goods_id`) USING BTREE,
  KEY `idx_od_order` (`order_id`) USING BTREE,
  CONSTRAINT `fk_od_order` FOREIGN KEY (`order_id`) REFERENCES `yz_order` (`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_plugins
-- ----------------------------
DROP TABLE IF EXISTS `yz_plugins`;
CREATE TABLE `yz_plugins` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` varchar(50) NOT NULL COMMENT '插件名，英文',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '插件名称',
  `description` text COMMENT '插件描述',
  `type` tinyint(2) DEFAULT '0' COMMENT '插件类型, 1:网站；8;微信',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态；1开启；',
  `config` text COMMENT '插件配置',
  `hooks` varchar(255) DEFAULT NULL COMMENT '实现的钩子;以“，”分隔',
  `has_admin` tinyint(2) DEFAULT '0' COMMENT '插件是否有后台管理界面',
  `author` varchar(50) DEFAULT '' COMMENT '插件作者',
  `version` varchar(20) DEFAULT '' COMMENT '插件版本号',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '插件安装时间',
  `listorder` smallint(6) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='插件表';

-- ----------------------------
-- Table structure for yz_posts
-- ----------------------------
DROP TABLE IF EXISTS `yz_posts`;
CREATE TABLE `yz_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned DEFAULT '0' COMMENT '发表者id',
  `post_keywords` varchar(150) NOT NULL COMMENT 'seo keywords',
  `post_source` varchar(150) DEFAULT NULL COMMENT '转载文章的来源',
  `post_date` datetime DEFAULT '2000-01-01 00:00:00' COMMENT 'post创建日期，永久不变，一般不显示给用户',
  `post_content` longtext COMMENT 'post内容',
  `post_title` text COMMENT 'post标题',
  `post_excerpt` text COMMENT 'post摘要',
  `post_status` int(2) DEFAULT '1' COMMENT 'post状态，1已审核，0未审核,3删除',
  `comment_status` int(2) DEFAULT '1' COMMENT '评论状态，1允许，0不允许',
  `post_modified` datetime DEFAULT '2000-01-01 00:00:00' COMMENT 'post更新时间，可在前台修改，显示给用户',
  `post_content_filtered` longtext,
  `post_parent` bigint(20) unsigned DEFAULT '0' COMMENT 'post的父级post id,表示post层级关系',
  `post_type` int(2) DEFAULT '1' COMMENT 'post类型，1文章,2页面',
  `post_mime_type` varchar(100) DEFAULT '',
  `comment_count` bigint(20) DEFAULT '0',
  `smeta` text COMMENT 'post的扩展字段，保存相关扩展属性，如缩略图；格式为json',
  `post_hits` int(11) DEFAULT '0' COMMENT 'post点击数，查看数',
  `post_like` int(11) DEFAULT '0' COMMENT 'post赞数',
  `istop` tinyint(1) NOT NULL DEFAULT '0' COMMENT '置顶 1置顶； 0不置顶',
  `recommended` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐 1推荐 0不推荐',
  PRIMARY KEY (`id`),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`id`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`),
  KEY `post_date` (`post_date`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Portal文章表';

-- ----------------------------
-- Table structure for yz_region
-- ----------------------------
DROP TABLE IF EXISTS `yz_region`;
CREATE TABLE `yz_region` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增长',
  `name` varchar(100) NOT NULL,
  `fullname` varchar(200) NOT NULL COMMENT '名称',
  `shortname` varchar(60) NOT NULL,
  `pinyin` varchar(100) NOT NULL,
  `pinyin_f` varchar(100) NOT NULL,
  `lng` varchar(64) NOT NULL COMMENT '经度',
  `lat` varchar(64) NOT NULL COMMENT '纬度',
  `parent` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '类型（1-省，2-市，3-区）',
  `is_hot` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否热门（1-热门，0-普通）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3269 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for yz_role
-- ----------------------------
DROP TABLE IF EXISTS `yz_role`;
CREATE TABLE `yz_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '角色名称',
  `pid` smallint(6) DEFAULT NULL COMMENT '父角色ID',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '状态',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `listorder` int(3) NOT NULL DEFAULT '0' COMMENT '排序字段',
  PRIMARY KEY (`id`),
  KEY `parentId` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Table structure for yz_role_user
-- ----------------------------
DROP TABLE IF EXISTS `yz_role_user`;
CREATE TABLE `yz_role_user` (
  `role_id` int(11) unsigned DEFAULT '0' COMMENT '角色 id',
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户角色对应表';

-- ----------------------------
-- Table structure for yz_route
-- ----------------------------
DROP TABLE IF EXISTS `yz_route`;
CREATE TABLE `yz_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '路由id',
  `full_url` varchar(255) DEFAULT NULL COMMENT '完整url， 如：portal/list/index?id=1',
  `url` varchar(255) DEFAULT NULL COMMENT '实际显示的url',
  `listorder` int(5) DEFAULT '0' COMMENT '排序，优先级，越小优先级越高',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态，1：启用 ;0：不启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='url路由表';

-- ----------------------------
-- Table structure for yz_slide
-- ----------------------------
DROP TABLE IF EXISTS `yz_slide`;
CREATE TABLE `yz_slide` (
  `slide_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slide_cid` int(11) NOT NULL COMMENT '幻灯片分类 id',
  `slide_name` varchar(255) NOT NULL COMMENT '幻灯片名称',
  `slide_pic` varchar(255) DEFAULT NULL COMMENT '幻灯片图片',
  `slide_url` varchar(255) DEFAULT NULL COMMENT '幻灯片链接',
  `slide_des` varchar(255) DEFAULT NULL COMMENT '幻灯片描述',
  `slide_content` text COMMENT '幻灯片内容',
  `slide_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  `listorder` int(10) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`slide_id`),
  KEY `slide_cid` (`slide_cid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='幻灯片表';

-- ----------------------------
-- Table structure for yz_slide_cat
-- ----------------------------
DROP TABLE IF EXISTS `yz_slide_cat`;
CREATE TABLE `yz_slide_cat` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) NOT NULL COMMENT '幻灯片分类',
  `cat_idname` varchar(255) NOT NULL COMMENT '幻灯片分类标识',
  `cat_remark` text COMMENT '分类备注',
  `cat_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  PRIMARY KEY (`cid`),
  KEY `cat_idname` (`cat_idname`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='幻灯片分类表';

-- ----------------------------
-- Table structure for yz_terms
-- ----------------------------
DROP TABLE IF EXISTS `yz_terms`;
CREATE TABLE `yz_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` varchar(200) DEFAULT NULL COMMENT '分类名称',
  `slug` varchar(200) DEFAULT '',
  `taxonomy` varchar(32) DEFAULT NULL COMMENT '分类类型',
  `description` longtext COMMENT '分类描述',
  `parent` bigint(20) unsigned DEFAULT '0' COMMENT '分类父id',
  `count` bigint(20) DEFAULT '0' COMMENT '分类文章数',
  `path` varchar(500) DEFAULT NULL COMMENT '分类层级关系路径',
  `seo_title` varchar(500) DEFAULT NULL,
  `seo_keywords` varchar(500) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `list_tpl` varchar(50) DEFAULT NULL COMMENT '分类列表模板',
  `one_tpl` varchar(50) DEFAULT NULL COMMENT '分类文章页模板',
  `listorder` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1发布，0不发布',
  PRIMARY KEY (`term_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Portal 文章分类表';

-- ----------------------------
-- Table structure for yz_term_relationships
-- ----------------------------
DROP TABLE IF EXISTS `yz_term_relationships`;
CREATE TABLE `yz_term_relationships` (
  `tid` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'posts表里文章id',
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `listorder` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1发布，0不发布',
  PRIMARY KEY (`tid`),
  KEY `term_taxonomy_id` (`term_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='Portal 文章分类对应表';

-- ----------------------------
-- Table structure for yz_users
-- ----------------------------
DROP TABLE IF EXISTS `yz_users`;
CREATE TABLE `yz_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `user_pass` varchar(64) NOT NULL DEFAULT '' COMMENT '登录密码；sp_password加密',
  `user_nicename` varchar(50) NOT NULL DEFAULT '' COMMENT '用户美名',
  `user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '登录邮箱',
  `user_url` varchar(100) NOT NULL DEFAULT '' COMMENT '用户个人网站',
  `avatar` varchar(255) NOT NULL COMMENT '用户头像，相对于upload/avatar目录',
  `sex` smallint(1) NOT NULL DEFAULT '0' COMMENT '性别；0：保密，1：男；2：女',
  `birthday` date NOT NULL COMMENT '生日',
  `signature` varchar(255) NOT NULL COMMENT '个性签名',
  `last_login_ip` varchar(16) NOT NULL COMMENT '最后登录ip',
  `last_login_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '最后登录时间',
  `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '注册时间',
  `user_activation_key` varchar(60) NOT NULL DEFAULT '' COMMENT '激活码',
  `user_status` int(11) NOT NULL DEFAULT '1' COMMENT '用户状态 0：禁用； 1：正常 ；2：未验证',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `user_type` smallint(1) NOT NULL DEFAULT '1' COMMENT '用户类型，1:admin ;2:会员',
  `coin` int(11) NOT NULL DEFAULT '0' COMMENT '金币',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  PRIMARY KEY (`id`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Table structure for yz_user_favorites
-- ----------------------------
DROP TABLE IF EXISTS `yz_user_favorites`;
CREATE TABLE `yz_user_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL COMMENT '用户 id',
  `title` varchar(255) DEFAULT NULL COMMENT '收藏内容的标题',
  `url` varchar(255) DEFAULT NULL COMMENT '收藏内容的原文地址，不带域名',
  `description` varchar(500) DEFAULT NULL COMMENT '收藏内容的描述',
  `table` varchar(50) DEFAULT NULL COMMENT '收藏实体以前所在表，不带前缀',
  `object_id` int(11) DEFAULT NULL COMMENT '收藏内容原来的主键id',
  `createtime` int(11) DEFAULT NULL COMMENT '收藏时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户收藏表';

-- ----------------------------
-- Table structure for yz_wechat_message
-- ----------------------------
DROP TABLE IF EXISTS `yz_wechat_message`;
CREATE TABLE `yz_wechat_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(50) NOT NULL,
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '1：未注册用户；2：已注册用户',
  `detail` varchar(500) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wm_mthird` (`uuid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Procedure structure for scheduler_sync
-- ----------------------------
DROP PROCEDURE IF EXISTS `scheduler_sync`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `scheduler_sync`()
BEGIN

UPDATE yz_order a
INNER JOIN yz_order_detail b USING (order_id)
INNER JOIN yz_goods c ON b.goods_id = c.goods_id
SET a.status = 7,a.end_time = UNIX_TIMESTAMP(),c.inventory = c.inventory+b.num
WHERE
	a.status = 1
AND (
	(
		UNIX_TIMESTAMP() - a.create_time
	) > 900
);

END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for scheduler_sync2
-- ----------------------------
DROP PROCEDURE IF EXISTS `scheduler_sync2`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `scheduler_sync2`()
BEGIN

UPDATE yz_order a
INNER JOIN yz_order_detail b USING (order_id)
INNER JOIN yz_goods c ON b.goods_id = c.goods_id
SET a.status = 4,a.end_time = UNIX_TIMESTAMP(),c.sale_num = c.sale_num+b.num
WHERE
	a.status = 3
AND (
	(
		UNIX_TIMESTAMP() - a.create_time
	) > 86400*15
);

END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for status_scheduler_event
-- ----------------------------
DROP EVENT IF EXISTS `status_scheduler_event`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` EVENT `status_scheduler_event` ON SCHEDULE EVERY 30 SECOND STARTS '2016-12-14 11:45:01' ON COMPLETION NOT PRESERVE DISABLE DO CALL scheduler_sync()
;;
DELIMITER ;

-- ----------------------------
-- Event structure for status_scheduler_event2
-- ----------------------------
DROP EVENT IF EXISTS `status_scheduler_event2`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` EVENT `status_scheduler_event2` ON SCHEDULE EVERY 1 DAY STARTS '2016-12-14 22:55:37' ON COMPLETION NOT PRESERVE ENABLE DO CALL scheduler_sync2()
;;
DELIMITER ;
