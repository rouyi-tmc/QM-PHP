<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
	<title>错误提示</title>
	<link rel="stylesheet" href="{{ asset('css/errors.css') }}">
</head>
<body>
<div class="main">
	<div class="pic">:(</div>
	<div class="msg">{!! $data['exception']->getMessage() !!}</div>
	<div class="info">
		<div class="title">File:</div>
		<div class="path">
			File:{!! $data['exception']->getFile() !!}  Line:{!! $data['exception']->getLine() !!}
		</div>
	</div>
	<div class="info">
		<div class="title">Trace</div>
		<div class="path">
			{!! nl2br($data['exception']->__toString()) !!}
		</div>
	</div>
</div>
</body>
</html>