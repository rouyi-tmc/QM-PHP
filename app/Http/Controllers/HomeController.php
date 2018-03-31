<?php
/**
 *
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2018/4/1
 * Time: 2:38
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

namespace App\Http\Controllers;

use App\Http\Classes\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }
}