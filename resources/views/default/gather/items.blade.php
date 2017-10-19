@extends('layout')
@section('title', $data['title'])
@section('content')
    <div class="items">
        @if(isset($data['items']))
            @foreach ($data['items']->lists as $item)
                <div class="row item">
                    <div class="col-xs-4">
                        <a href="{{ url('gather/show/' . $item['id']) }}">
                            <img src="{{ $item['shop_logo'] }}" alt="{{ $item['shop_name'] }}">
                        </a>
                    </div>
                    <div class="col-xs-8">
                        <h4 class="title">{{ $item['shop_name'] }}</h4>
                        <p>主营：{{ getCategoriesName($item['categories']) }}</p>
                        <p>地址：{{ $item['address'] }}</p>
                        @if(isset($item['distance']))
                        <p>离我：{{ $item['distance'] }}公里</p>
                        @endif
                        <p>
                            <span>电话：{{ $item['phone'] }}</span>
                            <span>评分：{{ $item['score'] }}分</span>
                        </p>
                    </div>
                </div>
            @endforeach
            @if(isset($data['items']->page))
                {!! $data['items']->page !!}
            @endif
        @endif
    </div>
@endsection