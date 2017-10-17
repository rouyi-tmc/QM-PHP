<?php
/**
 * 配置文件
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午6:45
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

return [
    'app_url' => (APP_URL) ? APP_URL : ((($_SERVER['SERVER_PORT'] == 80) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME']),

    // 数据库配置
    'db' => [
        'type' => 'mysql', //数据库类型
        'port' => '63918', //数据库端口
        'host' => 'bj-cdb-46lgsk9d.sql.tencentcdb.com', //数据库地址
        'dbname' => 'recieve', //数据表
        'username' => 'rooty', //数据库用户名
        'password' => '1qazxsW@', //数据库密码
        'character' => 'UTF-8', //使用编码
    ],
    'db2' => [
        'type' => 'mysql', //数据库类型
        'port' => '3306', //数据库端口
        'host' => 'localhost', //数据库地址
        'dbname' => 'demo_test_20171016', //数据表
        'username' => 'root', //数据库用户名
        'password' => 'root', //数据库密码
        'character' => 'UTF-8', //使用编码
    ],
    // 默认控制器和操作名
    'route' => [
        'default' => [
            'controller' => 'Item',
            'action' => 'index',
        ],
    ],
    'template' => [
        'suffix' => '.blade.php',
        'style' => 'default',
    ]
];