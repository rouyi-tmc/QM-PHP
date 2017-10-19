<?php
/**
 * 网站的一些简单配置文件
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午6:45
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

return [
    'debug' => true,
    'url' => ((($_SERVER['SERVER_PORT'] == 80) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME']),
    'template' => 'default', // 当前模板风格
];