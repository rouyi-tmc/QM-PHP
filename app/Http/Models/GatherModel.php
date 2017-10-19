<?php
/**
 *
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午7:17
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

namespace App\Http\Models;

use App\Http\Classes\Db;
use App\Http\Classes\Model;

class GatherModel extends Model
{
    protected $table = 'APP_783648_1508128459057';

    /**
     * 搜索
     * @param float $longitude 经度
     * @param float $latitude 纬度
     * @param string $categories 分类搜索
     * @param int $km 多少公里以内 默认为3公里以内的
     * @return mixed
     */
    public function search($longitude, $latitude, $categories = '', $km = 3)
    {
        $distanceSql = "acos(sin(({$latitude} * 3.1415) / 180) * sin((latitude * 3.1415) / 180) + cos(({$latitude} * 3.1415) / 180) * cos((latitude * 3.1415) / 180) * cos(({$longitude} * 3.1415) / 180 - (longitude * 3.1415) / 180)) * 6370.996";
        $where = ($categories) ? "WHERE `categories` like '%{$categories}%' " : '';
        $query = "SELECT %s FROM `{$this->table}` %s";

        $pdo = Db::pdo();
        $formatParam = [];
        $sql = sprintf($query, "{$this->field},round(({$distanceSql}), 3) AS distance", "{$where}HAVING distance <= {$km} ORDER BY distance ASC LIMIT {$this->offset},{$this->pageSize}");

        $sth = $pdo->prepare($sql);
        $sth = $this->formatParam($sth, $formatParam);
        $sth->execute();
        $items = $sth->fetchAll();
        if (strpos($this->field, 'categories') !== false) {
            foreach ($items as $k => $v) {
                $items[$k]['categories'] = getCategoriesName($v['categories']);
            }
        }
        $this->items->lists = $items;

        $where = str_replace('WHERE', 'AND', $where);
        $sqlNum = sprintf($query, "{$this->field},COUNT(*) as total", "WHERE ({$distanceSql}) <= {$km} {$where}");
        $sthNum = $pdo->prepare($sqlNum);
        $sthNum = $this->formatParam($sthNum, $formatParam);
        $sthNum->execute();
        $count = $sthNum->fetch(\PDO::FETCH_ASSOC);
        $this->items->count = $count['total'];
        return $this;
    }
}