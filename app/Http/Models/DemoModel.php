<?php
/**
 * 测试模型
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/20
 * Time: 4:21
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

namespace App\Http\Models;

use App\Http\Classes\Db;
use App\Http\Classes\Model;

class DemoModel extends Model
{
    /**
     * 自定义当前模型操作的数据库表名称，
     * 如果不指定，默认为类名称的小写字符串，
     * 这里就是 item 表
     *
     * @var string
     */
    protected $table = 'demo';

    /**
     * 搜索功能，因为 Sql 父类里面没有现成的 like 搜索，所以需要自己写 SQL 语句
     * 对数据库的操作应该都放在 Model 里面，然后提供给 Controller 直接调用
     *
     * @param string $keyword string 查询的关键词
     * @return array 返回的数据
     */
    public function search($keyword)
    {
        $sql = "select * from `$this->table` where `item_name` like :keyword";
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, [':keyword' => "%$keyword%"]);
        $sth->execute();
        return $sth->fetchAll();
    }
}