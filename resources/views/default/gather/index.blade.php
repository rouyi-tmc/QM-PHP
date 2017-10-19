@extends('layout')
@section('title', $data['title'])
@section('content')
    <div class="map">
        <div id="myPageTop">
            <form class="layui-form" action="">
                <div class="form-group">
                    <input type="text" id="tipInput" class="form-control tipInput" placeholder="位置获取中...">
                    <input type="hidden" id="location">
                </div>
                <div class="form-group">
                    <select name="category" id="category" lay-search="">
                        <option value="">请输入或选择商品分类</option>
                        <option value="所有分类">所有分类</option>
                        @foreach($data['category'] as $v)
                        <option value="{{ $v }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="btn-box hide">
                    <a class="btn btn-default developMap on">展开地图</a>
                </div>
                <div class="tips alert hide" id="tips" role="alert"></div>
            </form>
        </div>
        <div id="container" class="hide" style="height: 500px;"></div>
    </div>
    <div class="items hide" id="itemsBox"></div>
    <script id="itemsTemplate" type="text/html">
        @{{#  layui.each(d.lists, function(index, value){ }}
        <div class="row item">
            <div class="col-xs-4">
                <a href="{{ url('gather/show/') }}@{{value.id}}">
                    <img src="@{{value.shop_logo}}" alt="@{{value.shop_name}}">
                </a>
            </div>
            <div class="col-xs-8">
                <h4 class="title">@{{value.shop_name}}</h4>
                <p>主营：@{{value.categories}}</p>
                <p>地址：@{{value.address}}</p>
                <p>离我：@{{value.distance}}公里</p>
                <p>
                    <span>电话：@{{value.phone}}</span>
                    <span>评分：@{{value.score}}分</span>
                </p>
            </div>
        </div>
        @{{#  }); }}
    </script>
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
@endsection
@section('footerJs')
<script src="http://webapi.amap.com/maps?v=1.3&key=15a8f418cf38e511f756575ea1f1bb08&plugin=AMap.Autocomplete,AMap.PlaceSearch,AMap.Geocoder"></script>
@endsection
@section('footerScript')
<script>
    layui.use('app', function (app) {
        app.map();
    });
</script>
@endsection