<?php
/**
 * 控制器基类
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午7:06
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

namespace App\Lib\Classes;

class Controller
{
    protected $_controller;
    protected $_action;
    protected $_view;

    /**
     * 构造函数，初始化属性，并实例化对应模型
     * Controller constructor.
     * @param $controller
     * @param $action
     */
    public function __construct($controller, $action)
    {
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_view = new View($controller, $action);
    }

    /**
     * 分配变量
     * @param $name
     * @param $value
     * @return mixed
     */
    public function assign($name, $value)
    {
        return $this->_view->assign($name, $value);
    }

    /**
     * 渲染视图
     * @return mixed
     */
    public function display()
    {
        return $this->_view->display();
    }
}