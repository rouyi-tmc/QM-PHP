<?php
/**
 * 数据库操作类，$pdo属性为静态属性，这样在页面执行周期内，
 * 只要经过一次赋值，那么第二次再获取还是首次赋值的内容，这
 * 里就是PDO对象，这样可以确保运行期间只有一个数据库连接对
 * 像，这种是一种简单的单例模式
 *
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午7:05
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

namespace App\Http\Classes;

use PDO;
use Exception;

class Db
{
    private static $pdo = null;

    /**
     * 实例化 PDO
     *
     * @return null|PDO
     */
    public static function pdo()
    {
        if (self::$pdo !== null) return self::$pdo;

        try {
            $dsn = sprintf('%s:host=%s;port=%s;name=%s', DB_TYPE, DB_HOST, DB_PORT, DB_NAME);
            $option = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
            $db = new PDO($dsn, DB_USER, DB_PASS, $option);
            $db->query("set character set '" . DB_CHAR . "'");
            return self::$pdo = $db;
        } catch (Exception $e) {
            die('数据库连接失败，失败信息：' . $e);
        }
    }
}