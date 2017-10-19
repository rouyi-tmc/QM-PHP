<?php
/**
 * 数据库配置
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/18
 * Time: 下午3:08
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

return [
    // 数据库类型
    'default' => 'mysql',

    // 数据库配置
    'mysql2' => [
        'host' => 'bj-cdb-46lgsk9d.sql.tencentcdb.com', //数据库地址
        'port' => '63918', //数据库端口
        'username' => 'rooty', //数据库用户名
        'password' => '1qazxsW@', //数据库密码
        'dbname' => 'recieve', //数据表
        'character' => 'UTF-8', //使用编码
    ],
    'mysql' => [
        'port' => '3306',
        'host' => 'localhost',
        'dbname' => 'demo_20171018',
        'username' => 'root',
        'password' => 'root',
        'character' => 'UTF-8',
    ],
];