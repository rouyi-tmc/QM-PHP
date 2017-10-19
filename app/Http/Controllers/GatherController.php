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

namespace App\Http\Controllers;

use App\Http\Classes\Controller;
use App\Http\Models\GatherModel;

class GatherController extends Controller
{
    private $model;

    /**
     * 构造函数
     * GatherController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new GatherModel();
    }

    /**
     * Index
     * @return mixed
     */
    public function index()
    {
        $items = $this->model
            ->field('categories')
            ->order(['id DESC'])
            ->limit(0, 10000)
            ->fetchAll()
            ->page(false);

        $category = [];
        foreach ($items->lists as $k => $v) {
            $category[$k] = (isJson($v['categories'])) ? json_decode($v['categories'], true) : $v['categories'];
        }
        $category = $this->getUniqueArray($category);

        return view('gather.index', [
            'title' => '选择地址',
            'category' => $category,
        ]);
    }

    /**
     * 数据列表
     * @return mixed
     */
    public function items()
    {
        $page = (input('page')) ? input('page') : 1;
        $pageSize = 30;
        $offset = ($page - 1) * $pageSize;
        $field = 'id,longitude,latitude,phone,score,shop_name,shop_logo,address,categories';

        $items = $this->model
            ->field($field)
            ->order(['id DESC'])
            ->limit($offset, $pageSize)
            ->fetchAll()
            ->page();

        return view('gather.items', [
            'title' => '测试',
            'keyword' => '测试',
            'items' => $items,
        ]);
    }

    /**
     * 详情
     * @param int $id
     * @return mixed
     */
    public function show($id = 0)
    {
        if (!$id) return message('参数错误！');
        $items = $this->model->where(["id = ?"], [$id])->fetch();
        if (!$items) return message('您查看的数据不存在！');
        return view('gather.show', $items);
    }

    /**
     * 根据经度纬度查询数据库中3公里内的数据
     */
    public function getLocation()
    {
        $page = (input('page')) ? input('page') : 1;
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;
        $field = 'id,longitude,latitude,phone,score,shop_name,shop_logo,address,categories';

        $latitude = (input('latitude')) ? input('latitude') : null;
        $longitude = (input('longitude')) ? input('longitude') : null;
        $category = (input('category')) ? input('category') : null;
        $category = ($category == '所有分类') ? null : $category;

        if (!$latitude || !$longitude) return canBackJson([
            'code' => -100,
            'message' => '参数不正确'
        ]);

        $items = $this->model
            ->order(['id DESC'])
            ->limit($offset, $pageSize)
            ->field($field)
            ->search($longitude, $latitude, $category)
            ->page(false);

        $items->last_page = ceil($items->count / $pageSize);

        return canBackJson([
            'code' => 200,
            'data' => $items,
        ]);
    }

    public function double()
    {
        $getDouble = $this->getDouble(116.401158, 39.94083, 3.0);
        print_r($getDouble);
    }

    /**
     * 计算出方圆位置
     * @param float $lng 经度
     * @param float $lat 纬度
     * @param float $distance 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
     * @return array 正方形四个点的经纬度坐标
     */
    private function getDouble($lng, $lat, $distance = 0.5)
    {
        $distance = ($distance == 3) ? $distance - 0.881 : $distance; //此处需要计算准确，要不然会有偏差
        $earth_radius = 6371;
        $dLng = 2 * asin(sin($distance / (2 * $earth_radius)) / cos(deg2rad($lat)));
        $dLng = rad2deg($dLng);

        $dLat = $distance / $earth_radius;
        $dLat = rad2deg($dLat);

        $result = [
            'left-top' => [
                'lat' => $lat + $dLat,
                'lng' => $lng - $dLng
            ],
            'right-top' => [
                'lat' => $lat + $dLat,
                'lng' => $lng + $dLng
            ],
            'left-bottom' => [
                'lat' => $lat - $dLat,
                'lng' => $lng - $dLng
            ],
            'right-bottom' => [
                'lat' => $lat - $dLat,
                'lng' => $lng + $dLng
            ],
        ];

        return $result;
    }

    /**
     * 计算两地之前的距离
     * @param float $latitude1 起点纬度
     * @param float $longitude1 起点经度
     * @param float $latitude2 终点纬度
     * @param float $longitude2 终点经度
     * @param string $unit 单位 Mi:英里 Km:公里
     * @return float
     */
    private function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Km')
    {
        $theta = $longitude1 - $longitude2;
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        switch ($unit) {
            case 'Mi':
                break;
            case 'Km' :
                $distance = $distance * 1.609344;
        }
        return (round($distance, 4));
    }

    /**
     * 把分类信息写入缓存
     * @param array $data
     * @return bool
     */
    private function setCategory($data = [])
    {
        $cache = [
            'index' => getCache('shop_category_index'),
            'category' => getCache('shop_category'),
        ];

        $getUniqueArray = getUniqueArray($data);
        $category_index = $getUniqueArray['index'];
        $category_index = var_export($category_index, true);
        $category_index = "<?php\n// 分类索引缓存\nreturn " . $category_index . ";";
        file_put_contents(storage_path('cache/shop_category_index.php'), $category_index);

        $category = $getUniqueArray['output'];
        $category = var_export($category, true);
        $category = "<?php\n// 分类缓存\nreturn " . $category . ";";
        file_put_contents(storage_path('cache/shop_category.php'), $category);
        return true;
    }

    /**
     * 删除二维数组中重复的值
     * @param array $category
     * @return array
     */
    private function getUniqueArray($category = [])
    {
        $str = '';
        foreach ($category as $k => $value) {
            foreach ($value as $name) $str .= $name . "\n";
        }
        $output = explode("\n", $str);
        $output = array_unique($output);
        $output = array_values($output);
        $output = array_filter($output);
        return $output;
    }
}