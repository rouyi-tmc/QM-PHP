<?php
/**
 * 控制器
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午7:17
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

namespace App\Lib\Controllers;

use App\Lib\Classes\Controller;
use App\Lib\Models\GatherModel;

class GatherController extends Controller
{
    public function index()
    {
        $page = (input('page')) ? input('page') : 1;
        $pageSize = 30;
        $offset = ($page - 1) * $pageSize;
        $model = new GatherModel();
        $data = $model->order(['id DESC'])->limit($offset, $pageSize)->fetchAll();

        $this->assign('title', '测试');
        $this->assign('keyword', '测试');
        $this->assign('data', $data);
        return $this->display();
    }

    public function map()
    {
        $this->assign('title', '地图测试');
        return $this->display();
    }
}