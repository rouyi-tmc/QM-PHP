<?php
/**
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/16
 * Time: 13:28
 * Desc: 单文件入口
 */

// 程序目录
define('ROOT_PATH', __DIR__ . '/');

// 应用目录为当前目录
define('APP_PATH', ROOT_PATH . 'App/');

// 网站地址 为空的话自动获取
define('APP_URL', null);

// 开启调试模式
define('APP_DEBUG', true);

// 加载函数库
require APP_PATH . 'Common/Common.php';

// 自动加载
spl_autoload_register(function ($class) {
    if (!$class) return false;
    $file = str_replace('\\', '/', $class) . '.php';
    if (!file_exists($file)) return message('类' . $class . '不存在！');
    return require $file;
});

// 实例化框架类
(new App\Lib\Route())->run();