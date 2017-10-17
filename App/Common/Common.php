<?php
/**
 * 全局函数
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午6:42
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

if (!function_exists('Config')) {
    /**
     * 获取配置项
     * @param string $field
     * @return mixed
     */
    function Config($field = '')
    {
        $config = require APP_PATH . 'Config/Config.php';
        return (isset($config[$field])) ? $config[$field] : $config;
    }
}

if (!function_exists('isJson')) {
    /**
     * 判断是否是 JSON 字符串
     * @param $string
     * @return bool
     */
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('asset')) {
    /**
     * asset
     * @param string $file
     * @return string
     */
    function asset($file = '')
    {
        $app_url = Config('app_url');
        return $app_url . '/assets/' . $file;
    }
}

if (!function_exists('url')) {
    /**
     * url
     * @param string $url
     * @return string
     */
    function url($url = '')
    {
        $app_url = Config('app_url');
        return $app_url . '/' . $url;
    }
}

if (!function_exists('template')) {
    /**
     * 加载模板
     * @param string $template
     * @param array $data
     * @return mixed
     */
    function template($template, $data = [])
    {
        extract($data);
        $config = Config('template');
        if ($template == 'header' || $template == 'footer') {
            $path = 'Templates/' . $config['style'] . '/layouts/';
        } else {
            $path = 'Templates/' . $config['style'] . '/';
        }

        $path = APP_PATH . $path;
        $templateFile = $path . $template . $config['suffix'];
        if (!file_exists($templateFile)) return message('模板 ' . $template . ' 不存在！');

        return include $templateFile;
    }
}

if (!function_exists('message')) {
    /**
     * 提示消息
     * @param string $message
     * @return mixed
     */
    function message($message)
    {
        exit($message);
    }
}

if (!function_exists('getControllerName')) {
    /**
     * 获得控制器和方法名
     * @param string $field
     * @return array|string
     */
    function getControllerAction($field = '')
    {
        $config = Config('route');

        $array = [
            'controller' => $config['default']['controller'],
            'action' => $config['default']['action'],
        ];

        $url = $_SERVER['REQUEST_URI'];
        $position = strpos($url, '?');
        $url = $position === false ? $url : substr($url, 0, $position);
        $url = trim($url, '/');
        if ($url) {
            $urlArray = explode('/', $url);
            $urlArray = array_filter($urlArray);
            $array['controller'] = ucfirst($urlArray[0]);
            array_shift($urlArray);
            if ($urlArray) $array['action'] = $urlArray[0];
        }

        return ($field) ? $array[$field] : $array;
    }
}

if (!function_exists('input')) {
    /**
     * 获取请求参数
     * @param string $field
     * @return array|bool
     */
    function input($field = '') {
        $array = [];
        if (isset($_POST)) foreach ($_POST as $k => $v) $array[$k] = $v;
        if (isset($_GET)) foreach ($_GET as $k => $v) $array[$k] = $v;
        return ($field && isset($array[$field])) ? $array[$field] : false;
    }
}