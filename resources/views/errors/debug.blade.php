<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
	<title>温馨提示</title>
	<link rel="stylesheet" href="{{ asset('css/errors.css') }}">
</head>
<body>
<div class="main">
	<div class="pic">:(</div>
	<div class="msg">
		{!! $data['error'] !!}
		<p style="font-size: 14px;margin: 15px 0;color:#999;">Severity: {!! $data['errNo'] !!}</p>
	</div>
	<div class="info">
		<div class="title">File:</div>
		<div class="path">File:{!! $data['file'] !!}  Line:{!! $data['line'] !!}</div>
	</div>
	<div class="info">
		<div class="title">Trace</div>
		<div class="path">
			<?php unset($data['error'])?>
			@foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $id => $f)
				@if (isset($f['file']))
					#{!! $id !!} {!! $f['file'] !!}({!! $f['line'] !!}) <br/>
				@endif
			@endforeach
		</div>
	</div>
</div>
</body>
</html>