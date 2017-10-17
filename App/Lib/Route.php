<?php
/**
 * 路由
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午7:04
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

namespace App\Lib;

class Route
{
    protected $config = [];

    /**
     * 构造函数
     * Route constructor.
     */
    public function __construct()
    {
        $this->config = Config();
    }

    /**
     * 运行程序
     */
    public function run()
    {
        $this->setReporting();
        $this->removeMagicQuotes();
        $this->unRegisterGlobals();
        $this->setDbConfig();
        $this->route();
    }

    /**
     * 路由处理
     * @return mixed
     */
    public function route()
    {
        $controllerName = getControllerAction('controller');
        $actionName = getControllerAction('action');
        $param = [];
        $url = $_SERVER['REQUEST_URI'];
        $position = strpos($url, '?');
        $url = ($position === false) ? $url : substr($url, 0, $position);
        $url = trim($url, '/');
        if ($url) {
            $urlArray = explode('/', $url);
            $urlArray = array_filter($urlArray);
            array_shift($urlArray);
            $param = $urlArray ? $urlArray : [];
        }

        $controller = __NAMESPACE__ . '\Controllers\\' . $controllerName . 'Controller';

        if (!class_exists($controller)) return message($controller . '控制器不存在');

        if (!method_exists($controller, $actionName)) return message($actionName . '方法不存在');

        $dispatch = new $controller($controllerName, $actionName);

        return call_user_func_array([$dispatch, $actionName], $param);
    }

    /**
     * 检测是否打开了 DeBug 模式
     */
    public function setReporting()
    {
        if (APP_DEBUG === true) {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 'Off');
            ini_set('log_errors', 'On');
        }
    }

    /**
     * 删除敏感字符
     * @param $value
     * @return array|string
     */
    public function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map([$this, 'stripSlashesDeep'], $value) : stripslashes($value);
        return $value;
    }

    /**
     * 检测敏感字符并删除
     */
    public function removeMagicQuotes()
    {
        if (get_magic_quotes_gpc()) {
            $_GET = isset($_GET) ? $this->stripSlashesDeep($_GET) : '';
            $_POST = isset($_POST) ? $this->stripSlashesDeep($_POST) : '';
            $_COOKIE = isset($_COOKIE) ? $this->stripSlashesDeep($_COOKIE) : '';
            $_SESSION = isset($_SESSION) ? $this->stripSlashesDeep($_SESSION) : '';
        }
    }

    /**
     * 检测自定义全局变量并移除
     */
    public function unRegisterGlobals()
    {
        if (ini_get('register_globals')) {
            $array = ['_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES'];
            foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }

    /**
     * 配置数据库信息
     * @return bool|mixed
     */
    public function setDbConfig()
    {
        if (!isset($this->config['db'])) return message('缺少数据库配置项，请检查！');

        define('DB_TYPE', $this->config['db']['type']);
        define('DB_HOST', $this->config['db']['host']);
        define('DB_PORT', $this->config['db']['port']);
        define('DB_NAME', $this->config['db']['dbname']);
        define('DB_USER', $this->config['db']['username']);
        define('DB_PASS', $this->config['db']['password']);
        define('DB_CHAR', $this->config['db']['character']);

        return true;
    }
}