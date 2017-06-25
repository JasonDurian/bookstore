Bookstore
===============

## Now Begin

First:
1. git clone https://github.com/JasonDurian/bookstore.git

Second:
1. add 'data/' to the base directory

~~~
|--data							
	|--conf                 /配置目录
		|--config.php		/配置文件
		|--db.php			/数据库文件
		|--route.php		/路由文件
~~~

使用七牛云进行项目的资源管理，所以配置文件内容为：

~~~
config.php:
<?php	
return array (
  'FILE_UPLOAD_TYPE' => 'Qiniu',
  'UPLOAD_TYPE_CONFIG' => 
  array (
    'accessKey' => 'XXX',
    'secretKey' => 'XXX',
    'upHost' => 'http://up.qiniu.com',
    'domain' => 'XXX',
    'bucket' => 'XXX',
  ),
);
~~~

~~~
db.php:
return array(
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'XXX',
    'DB_USER' => 'XXX',
    'DB_PWD' => 'XXX',
    'DB_PORT' => 'XXX',
    'DB_PREFIX' => 'XXX',
    //密钥
    "AUTHCODE" => 'XXX',
    //cookies
    "COOKIE_PREFIX" => 'XXX',
);
~~~

> ThinkCMFX的运行环境要求PHP5.3.0以上。

详细开发文档参考 [ThinkCMFX完全开发手册](http://www.thinkcmf.com/docs/cmfx/)

## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
|--admin                      /管理后台URL重定向目录，你可以将文件夹名改为任何你喜欢的
    |--themes                 /后台模板文件目录
|--application                /应用目录 
  |--Admin                    /后台管理应用
  |--Api                      /公共接口
  |--Asset                    /资源管理应用
  |--Comment                  /评论应用
  |--Common                   /应用公共模块
  |--Portal                   /门户应用
|--data                       /各类数据存放目录，包括缓存数据
|--simplewind                 /核心包，无特殊情况请勿改动
|--public                     /静态文件存放包，包含bootstrap资源
|--themes                     /前台模板文件目录
~~~
