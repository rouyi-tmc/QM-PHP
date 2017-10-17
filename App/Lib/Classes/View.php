<?php
/**
 * 视图基类
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午7:01
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

namespace App\Lib\Classes;

class View
{
    protected $variables = [];
    protected $_controller;
    protected $_action;

    function __construct($controller, $action)
    {
        $this->_controller = strtolower($controller);
        $this->_action = strtolower($action);
    }

    /**
     * 分配变量
     * @param $name
     * @param $value
     * @return mixed
     */
    public function assign($name, $value)
    {
        return $this->variables[$name] = $value;
    }

    /**
     * 渲染显示
     * @return mixed
     */
    public function display()
    {
        $controllerLayout = $this->_controller . '/' . $this->_action;
        return template($controllerLayout, $this->variables);
    }
}