<?php
/**
 * 测试控制器
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午7:07
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

namespace App\Http\Controllers;

use App\Http\Classes\Controller;
use App\Http\Models\ItemModel;

class ItemController extends Controller
{
    /**
     * 首页方法，测试框架自定义DB查询
     * @return mixed
     */
    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

        if ($keyword) {
            $items = (new ItemModel())->search($keyword);
        } else {
            // 查询所有内容，并按倒序排列输出
            // where()方法可不传入参数，或者省略
            $items = (new ItemModel)->where()->order(['id DESC'])->fetchAll();
        }

        $data = ['title' => '欢迎使用', 'items' => $items];
        return view('welcome', compact('data'));
    }

    /**
     * 查看单条记录详情
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        // 通过?占位符传入$id参数
        $item = (new ItemModel())->where(["id = ?"], [$id])->fetch();
        $data = ['title' => '条目详情', 'items' => $item];
        return view('item.detail', compact('data'));
    }

    /**
     * 添加记录，测试框架DB记录创建 (Create)
     * @return mixed
     */
    public function add()
    {
        $data['item_name'] = $_POST['value'];
        $count = (new ItemModel)->add($data);
        $data = ['title' => '添加成功', 'count' => $count];
        return view('item.add', compact('data'));
    }

    /**
     * 操作管理
     * @param int $id
     * @return mixed
     */
    public function manage($id = 0)
    {
        $items = [];
        if ($id) {
            // 通过名称占位符传入参数
            $items = (new ItemModel())->where(["id = :id"], [':id' => $id])->fetch();
        }
        $data = ['title' => '管理条目', 'items' => $items];
        return view('item.manage', compact('data'));
    }

    /**
     * 更新记录，测试框架DB记录更新 (Update)
     * @return mixed
     */
    public function update()
    {
        $data = ['id' => $_POST['id'], 'item_name' => $_POST['value']];
        $count = (new ItemModel)->where(['id = :id'], [':id' => $data['id']])->update($data);
        $data = ['title' => '修改成功', 'count' => $count];
        return view('item.update', compact('data'));
    }

    /**
     * 删除记录，测试框架DB记录删除 (Delete)
     * @param null $id
     * @return mixed
     */
    public function delete($id = null)
    {
        $count = (new ItemModel)->delete($id);
        $data = ['title' => '删除成功', 'count' => $count];
        return view('item.update', compact('data'));
    }
}