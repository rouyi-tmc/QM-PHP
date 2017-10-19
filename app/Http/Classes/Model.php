<?php
/**
 * 模型 数据库操作 PDO
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午7:05
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

namespace App\Http\Classes;

use PDOStatement;

class Model
{
    // 数据库表名
    protected $table;

    // 数据库主键
    protected $primary = 'id';

    // WHERE和ORDER拼装后的条件
    protected $filter = '';

    // Pdo bindParam()绑定的参数集合
    private $param = [];

    // 当前页码
    protected $offset;

    // 显示条数
    protected $pageSize;

    // 排序方式
    private $orderBy;

    // 模型名称
    protected $model;

    // 查询结果
    protected $items;

    protected $field = '*';

    /**
     * 构造函数
     * Model constructor.
     */
    public function __construct()
    {
        // 获取数据库表名
        if (!$this->table) {
            $this->model = get_class($this); // 获取模型类名称
            $this->model = substr($this->model, 0, -5); // 删除类名最后的 Model 字符
            $this->table = strtolower($this->model); // 数据库表名与类名一致
        }

        $this->items = (object) [];
    }

    /**
     * 查询条件拼接，使用方式：
     *
     * $this->where(['id = 1','and title="Web"', ...])->fetch();
     * 为防止注入，建议通过$param方式传入参数：
     * $this->where(['id = :id'], [':id' => $id])->fetch();
     *
     * @param array $where 条件
     * @param array $param 参数
     * @return $this 当前对象
     */
    public function where($where = [], $param = [])
    {
        if (count($where) > 0) {
            $this->filter .= ' WHERE ';
            $this->filter .= implode(' ', $where);
        }

        if (count($param) > 0) $this->param = $param;

        return $this;
    }

    /**
     * 分页设置
     * @param int $offset
     * @param int $pageSize
     * @return $this
     */
    public function limit($offset = 0, $pageSize = 20)
    {
        $this->offset = $offset;
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * 分页
     * @param bool $display
     * @return mixed
     */
    public  function page($display = true)
    {
        if (!$this->items->count || !$display || $this->items->count <= $this->pageSize) return $this->items;
        $page = new Page();
        $page->init($this->items->count, $this->pageSize);
        $this->items->page = $page->show();
        return $this->items;
    }

    /**
     * 拼装排序条件，使用方式：
     * $this->order(['id DESC', 'title ASC', ...])->fetch();
     * @param array $order 排序条件
     * @return $this
     */
    public function order($order = ['id DESC'])
    {
        $this->orderBy = implode(',', $order);
        return $this;
    }

    /**
     * 设置查询字段
     * @param string $field
     * @return $this
     */
    public function field($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * 查询所有
     * @return mixed
     */
    public function fetchAll()
    {
        $query = "SELECT %s FROM `{$this->table}` {$this->filter}";
        $sql = sprintf("{$query} ORDER BY %s LIMIT %s,%s", $this->field, $this->orderBy, $this->offset, $this->pageSize);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();
        $this->items->lists = $sth->fetchAll();

        $sqlNum = sprintf($query, $this->field . ',COUNT(*) as total');
        $sthNum = Db::pdo()->prepare($sqlNum);
        $sthNum->execute();
        $count = $sthNum->fetch(\PDO::FETCH_ASSOC);
        $this->items->count = $count['total'];

        return $this;
    }

    /**
     * 查询一条
     * @return mixed
     */
    public function fetch()
    {
        $sql = sprintf("SELECT * FROM `%s` %s", $this->table, $this->filter);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();
        $this->items->item = $sth->fetch();

        return $this->items;
    }

    /**
     * 根据条件 (id) 删除
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        $sql = sprintf("DELETE FROM `%s` WHERE `%s` = :%s", $this->table, $this->primary, $this->primary);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, [$this->primary => $id]);
        $sth->execute();

        return $sth->rowCount();
    }

    /**
     * 新增数据
     * @param $data
     * @return int
     */
    public function add($data)
    {
        $sql = sprintf("INSERT INTO `%s` %s", $this->table, $this->formatInsert($data));
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->rowCount();
    }

    /**
     * 更新数据
     * @param $data
     * @return int
     */
    public function update($data)
    {
        $sql = sprintf("UPDATE `%s` SET %s %s", $this->table, $this->formatUpdate($data), $this->filter);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->rowCount();
    }

    /**
     * 占位符绑定具体的变量值
     * @param PDOStatement $sth 要绑定的PDOStatement对象
     * @param array $params 参数，有三种类型：
     * 1）如果SQL语句用问号?占位符，那么$params应该为
     *    [$a, $b, $c]
     * 2）如果SQL语句用冒号:占位符，那么$params应该为
     *    ['a' => $a, 'b' => $b, 'c' => $c]
     *    或者
     *    [':a' => $a, ':b' => $b, ':c' => $c]
     *
     * @return PDOStatement
     */
    public function formatParam(PDOStatement $sth, $params = [])
    {
        foreach ($params as $param => &$value) {
            $param = is_int($param) ? $param + 1 : ':' . ltrim($param, ':');
            $sth->bindParam($param, $value);
        }

        return $sth;
    }

    /**
     * 将数组转换成插入格式的sql语句
     * @param $data
     * @return string
     */
    private function formatInsert($data)
    {
        $fields = [];
        $names = [];
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s`", $key);
            $names[] = sprintf(":%s", $key);
        }

        $field = implode(',', $fields);
        $name = implode(',', $names);

        return sprintf("(%s) VALUES (%s)", $field, $name);
    }

    /**
     * 将数组转换成更新格式的sql语句
     * @param $data
     * @return string
     */
    private function formatUpdate($data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s` = :%s", $key, $key);
        }

        return implode(',', $fields);
    }
}