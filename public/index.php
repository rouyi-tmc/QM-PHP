<?php
/**
 * 单文件入口
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/18
 * Time: 下午3:48
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

require __DIR__ . '/../vendor/autoload.php';

(new App\Http\Route())->run();