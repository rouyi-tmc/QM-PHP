<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo isset($title) ? $title : ''?></title>
    <link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="//v3.bootcss.com/dist/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="<?php echo asset('css/style.css')?>" rel="stylesheet">
</head>
<body>
<div class="container content">
    <div class="map">
        <div id="myPageTop">
            <input id="tipInput" class="form-control tipInput" placeholder="位置获取中...">
            <div class="btn-box">
                <a class="btn btn-default developMap">展开地图</a>
            </div>
            <div class="tips alert hide" id="tips" role="alert"></div>
        </div>
        <div id="container" class="hide" style="height: 500px;"></div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">提示信息</h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<script src="http://webapi.amap.com/maps?v=1.3&key=15a8f418cf38e511f756575ea1f1bb08&plugin=AMap.Autocomplete,AMap.PlaceSearch,AMap.Geocoder"></script>
<script src="<?php echo asset('js/jquery.min.js')?>"></script>
<script src="<?php echo asset('js/bootstrap.min.js')?>"></script>
<script src="<?php echo asset('js/map.js')?>"></script>
</body>
</html>