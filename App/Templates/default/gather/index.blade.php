<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo isset($title) ? $title : ''?></title>
    <link href="//cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo asset('css/style.css')?>" rel="stylesheet">
</head>
<body>
<div class="container content">
    <div class="items">
        <?php foreach ($data->items as $item) : ?>
        <div class="row item">
            <div class="col-xs-2">
                <a href="#">
                    <img src="<?php echo $item['shop_logo']?>" alt="<?php echo $item['shop_name']?>">
                </a>
            </div>
            <div class="col-xs-10">
                <h4 class="media-heading">店名：<?php echo $item['shop_name']?> <?php echo $item['categories']?></h4>
                <p>地址：<?php echo $item['address']?></p>
                <p>
                    <span>经度：<?php echo $item['longitude']?></span>
                    <span>纬度：<?php echo $item['latitude']?></span>
                </p>
                <p>
                    <span>电话：<?php echo $item['phone']?></span>
                    <span>评分：<?php echo $item['score']?>分</span>
                </p>
            </div>
        </div>
        <?php endforeach; ?>
        <?php echo $data->page->show()?>
    </div>
</div>
</body>
</html>