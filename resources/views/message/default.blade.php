<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $data['title'] or '提示' }}</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-theme.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .container {
            padding: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="bs-example" data-example-id="simple-jumbotron">
        <div class="jumbotron">
            <h3>{{ $data['title'] or '提示' }}</h3>
            <p>{{ $data['content'] or '发生错误，请返回！' }}</p>
            <p><a class="btn btn-primary" href="{{ url() }}" role="button">返回首页</a></p>
        </div>
    </div>
</div>
</body>
</html>
