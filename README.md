# QM-PHP

## 简述

**QM-PHP**是一款简单的PHP MVC框架，使用面向对面快速开发，框架使用 Blade 模板引擎，让您快速上手！

要求：

* PHP 5.6+

## 目录说明

```
├─app                   程序主目录
├───Common              全局函数目录
├───Http                应用目录
│   ├─Classes           核心库文件
│   ├─Controllers       控制器目录
│   ├─Models            模型目录
│   ├─Route.php         路由程序文件
├─config                配置文件目录
├─public                前台资源目录
├───assets              静态资源目录
├───.htaccess           伪静态配置文件
├───index.php           入口文件
├─resources             资源目录
├───views               模板视图目录
├─storage               储存缓存及其它文件目录
├───cache               其它缓存目录
├───views               模板视图缓存目录
├─vendor                扩展包目录
```

## 使用

### 1.克隆代码

```
git clone https://github.com/RouyiTian/QM-PHP.git
```

### 2.修改数据库配置文件

打开配置文件 config/database.php ，使之与自己的数据库匹配

```
return [
    'default' => 'mysql',       // 数据库类型
    'mysql' => [
        'port' => '3306',       //数据库端口
        'host' => 'localhost',  //数据库地址
        'dbname' => 'test',     //数据表
        'username' => 'root',   //数据库用户名
        'password' => 'root',   //数据库密码
        'character' => 'UTF-8', //使用编码
    ],
];
```

### 3.配置 Nginx 或 Apache
在Apache 或 Nginx中创建一个站点，把 public 设置为站点根目录（入口文件 index.php 所在的目录）。

然后设置单一入口， Apache 服务器配置：
```
<IfModule mod_rewrite.c>
    # 打开Rerite功能
    RewriteEngine On

    # 如果请求的是真实存在的文件或目录，直接访问
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    # 如果访问的文件或目录不是真事存在，分发请求至 index.php
    RewriteRule . index.php
</IfModule>
```
Nginx 服务器配置：
```
location / {
    # 重新向所有非真实存在的请求到index.php
    try_files $uri $uri/ /index.php$args;
}
```

### 4.测试访问

然后访问站点域名：http://localhost/ 就可以了。
