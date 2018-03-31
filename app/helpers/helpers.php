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
     *
     * @param string $file
     * @return mixed
     */
    function Config($file)
    {
        if (!$file) return [];

        if (strpos($file, '.') === false) {
            $filename = $file;
        } else {
            list($filename, $field) = explode('.', $file);
        }

        $loadFile = root_path('config/' . $filename . '.php');
        if (!file_exists($loadFile)) return [];

        $config = require $loadFile;
        return (isset($field) && isset($config[$field])) ? $config[$field] : $config;
    }
}

if (!function_exists('isJson')) {
    /**
     * 判断是否是 JSON 字符串
     *
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
     *
     * @param string $file
     * @return string
     */
    function asset($file = '')
    {
        $app_url = Config('app.url');
        return $app_url . '/assets/' . $file;
    }
}

if (!function_exists('url')) {
    /**
     * url
     *
     * @param string $url
     * @return string
     */
    function url($url = '')
    {
        $app_url = Config('app.url');
        return $app_url . '/' . $url;
    }
}

if (!function_exists('message')) {
    /**
     * 提示消息
     *
     * @param string $message
     * @param string $title
     * @return mixed
     */
    function message($message = '', $title = '提示消息')
    {
        return view('message.default', [
            'title' => $title,
            'content' => $message
        ]);
    }
}

if (!function_exists('getControllerAction')) {
    /**
     * 获得控制器和方法名
     *
     * @param string $field
     * @return array|string
     */
    function getControllerAction($field = null)
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
     *
     * @param string $field
     * @return array|bool
     */
    function input($field = '')
    {
        $array = [];
        if (isset($_POST)) foreach ($_POST as $k => $v) $array[$k] = $v;
        if (isset($_GET)) foreach ($_GET as $k => $v) $array[$k] = $v;
        return ($field && isset($array[$field])) ? $array[$field] : false;
    }
}

if (!function_exists('root_path')) {
    /**
     * 拼接站点根目录真实路径
     *
     * @param string $path
     * @return string
     */
    function root_path($path = '')
    {
        return dirname(dirname(__DIR__)) . '/' . $path;
    }
}

if (!function_exists('app_path')) {
    /**
     * 拼接真实路径
     *
     * @param string $path
     * @return string
     */
    function app_path($path = '')
    {
        return root_path('app/' . $path);
    }
}

if (!function_exists('public_path')) {
    /**
     * 拼接真实路径
     *
     * @param string $path
     * @return string
     */
    function public_path($path = '')
    {
        return root_path('public/' . $path);
    }
}

if (!function_exists('storage_path')) {
    /**
     * 拼接真实路径
     *
     * @param string $path
     * @return string
     */
    function storage_path($path = '')
    {
        return root_path('storage/' . $path);
    }
}

if (!function_exists('resources_path')) {
    /**
     * 拼接真实路径
     *
     * @param string $path
     * @return string
     */
    function resources_path($path = '')
    {
        return root_path('resources/' . $path);
    }
}

if (!function_exists('view')) {
    /**
     * 加载模板
     *
     * @param null $view
     * @param array $data
     * @return mixed
     */
    function view($view = null, $data = [])
    {
        if (is_null($view)) {
            $view = getControllerAction('controller') . '/' . getControllerAction('action');
        }

        $view = strtolower($view);
        $file = new Xiaoler\Blade\Filesystem();
        $compiler = new Xiaoler\Blade\Compilers\BladeCompiler($file, storage_path('views'));
        $resolver = new Xiaoler\Blade\Engines\EngineResolver();
        $resolver->register('blade', function () use ($compiler) {
            return new Xiaoler\Blade\Engines\CompilerEngine($compiler);
        });

        $theme = null;
        if (!preg_match("/(message|errors)/", $view)) {
            $theme = Config('app.template');
        }

        $factory = new Xiaoler\Blade\Factory($resolver, new Xiaoler\Blade\FileViewFinder($file, [
            resources_path('views/' . $theme)
        ]));
        $factory->addExtension('blade', 'php');

        if (!$factory->exists($view)) {
            return abort(404, '模板 ' . $view . ' 不存在！');
        }

        try {
            $show = $factory->make($view, compact('data'))->render();
        } catch (Throwable $exception) {
            $show = $exception->getMessage();
        }

        exit($show);
    }
}

if (!function_exists('abort')) {
    /**
     * 显示自定义错误页面模板
     *
     * @param int $code 错误状态码
     * @param string $message 错误信息
     * @return mixed
     */
    function abort($code = 404, $message = null)
    {
        switch ($code) {
            case 404:
                Header('HTTP/1.1 404 Not Found');
                break;
        }

        return view("errors.{$code}");
    }
}

if (!function_exists('json')) {
    /**
     * 结束并输出JSON字符串
     *
     * @param array $data
     */
    function json($data = [])
    {
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }
}

if (!function_exists('getUniqueArray')) {
    /**
     * 去除数组重复的值
     *
     * @param array $array
     * @param string $field
     * @return array
     */
    function getUniqueArray($array = [], $field = '')
    {
        $old = $repeat = [];
        $new = $array;
        foreach ($new as $key => $value) {
            foreach ($value['name'] as $_k => $name) {
                $array_search = array_search($name, $old);
                if ($array_search !== false) {
                    $repeat[] = [
                        'name' => [$name],
                        'id' => [
                            $key => $value['id'],
                            $array_search => $new[$array_search]['id']
                        ],
                    ];
                }

                $old[$key] = $name;
            }
        }

        foreach ($repeat as $k => $v) {
            foreach ($v['id'] as $_k => $_v) unset($new[$_k]);
        }

        $data = [
            'index' => array_merge($repeat, $new),
            'output' => []
        ];

        foreach ($data['index'] as $k => $v) {
            foreach ($v['name'] as $_k => $_v) {
                $data['output'][] = [
                    'index' => $k,
                    'name' => $_v,
                ];
            }
        }

        return ($field) ? $data[$field] : $data;
    }
}

if (!function_exists('setCache')) {
    /**
     * 写入缓存
     *
     * @param $file
     * @param array $data
     * @return bool
     */
    function setCache($file, $data = [])
    {
        return true;
    }
}

if (!function_exists('getCache')) {
    /**
     * 读取缓存
     *
     * @param string $file
     * @return array|mixed
     */
    function getCache($file = '')
    {
        $cache = storage_path('cache/' . $file . '.php');
        if (!file_exists($cache)) return false;
        $data = require $cache;
        return $data;
    }
}

if (!function_exists('getCategoriesName')) {
    /**
     * 获取分类名称
     *
     * @param string $string
     * @return mixed|string
     */
    function getCategoriesName($string = '')
    {
        $name = (isJson($string)) ? json_decode($string, true) : $string;
        if (is_array($name)) $name = implode($name, '，');
        return $name;
    }
}

if (!function_exists('getJsonToArray')) {
    /**
     * JSON字符串转数组
     *
     * @param string $string
     * @return mixed|string
     */
    function getJsonToArray($string = '')
    {
        $data = isJson($string) ? json_decode($string, true) : $string;
        return $data;
    }
}