@extends('layout')
@section('title', $data->item['shop_name'])
@section('content')
    <div class="show" style="padding: 15px 0">
        <div class="well">店铺详情</div>
        <div class="panel panel-default">
            <div class="panel-heading">{{ $data->item['shop_name'] }} 简介</div>
            <div class="panel-body">
                <div class="content">
                    <p>ID：{{ $data->item['shop_id'] }}</p>
                    <p>主营：{{ getCategoriesName($data->item['categories']) }}</p>
                    <p>地址：{{ $data->item['address'] }}</p>
                    <p>电话：{{ $data->item['phone'] }}</p>
                    <p>评分：{{ $data->item['score'] }} &nbsp; 共 {{ $data->item['score_count'] }} 人参与评分</p>
                    <p>营业时间：{{ getCategoriesName($data->item['open_time']) }}</p>
                    <p>{{ $data->item['starting_price'] }}元起送费</p>
                    <p>{{ $data->item['deliver_fee'] }}元送餐费</p>
                    <p>平均收货时间{{ $data->item['avg_delivery_duration'] }}分钟</p>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">{{ $data->item['shop_name'] }} 活动</div>
            <div class="panel-body">
                <div class="content">
                    @foreach(getJsonToArray($data->item['activities']) as $activities)
                    <h4>活动名称：{{ $activities['activity_name'] }}</h4>
                    <p>活动描述：{{ $activities['activity_description'] }}</p>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">{{ $data->item['shop_name'] }} 商品</div>
            <div class="panel-body">
                <div class="row">
                    @foreach(getJsonToArray($data->item['foods']) as $foods)
                        @foreach($foods as $items)
                            @if(is_array($items))
                                @foreach($items as $item)
                                    <div class="col-sm-6 col-md-4">
                                        <div class="thumbnail">
                                            <img src="{{ $item['food_image'] }}" alt="{{ $item['food_name'] }}">
                                            <div class="caption">
                                                <h3>{{ $item['food_name'] }}</h3>
                                                <div class="content">
                                                    <p>ID：{{ $item['food_id'] }}</p>
                                                    <div class="price">
                                                        @foreach($item['food_price'] as $food_price)
                                                            <p>价格：{{ $food_price['sub_food_name'] }} &nbsp; {{ $food_price['sub_food_price'] }}元</p>
                                                        @endforeach
                                                    </div>
                                                    <p>评分：{{ round($item['food_score'], 2) }}</p>
                                                    <p>推荐率：{{ $item['food_recommend_rate'] }}</p>
                                                    <p>推荐数：{{ $item['food_recommend_count'] }}</p>
                                                    <p>月售：{{ $item['food_monthly_sold_count'] }}件</p>
                                                </div>
                                                <p class="hide">
                                                    <a href="#" class="btn btn-primary" role="button">Button</a>
                                                    <a href="#" class="btn btn-default" role="button">Button</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection