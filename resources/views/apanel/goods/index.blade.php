@extends('apanel.app')

@section('content')
    <div class="row wrap">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Города и товары
                </div>
                <div class="panel-body">

                    @include('apanel.goods.errors')
                    @include('apanel.goods.notes')

                    <div class="list-group">
                        <a href="{{ url('apanel/goods/addcity') }}" class="btn btn-success btn-sm">Добавить город</a>
                        <hr>
                        @forelse ($cities as $city)
                            <div class="list-group-item">
                                <h4 class="list-group-item-heading">
                                    <i class="glyphicon glyphicon-home"></i> {{ $city->name }}
                                    <a href="{{ url('apanel/goods/addgoods?city_id='. $city->id) }}" class="btn btn-success btn-sm pull-right">Добавить категорию товара</a>
                                </h4>
                                <p class="list-group-item-text">
                                    @forelse ($city->goods as $goods)
                                        <i class="glyphicon glyphicon-shopping-cart"></i> {{ $goods->name }}
                                        <a href="{{ url('apanel/goods/addgoods-price?goods_id='. $goods->id) }}" data-toggle="tooltip" title="Добавить товар в прайс"><i class="glyphicon glyphicon-plus-sign"></i></a>
                                        <a href="{{ url('apanel/goods/delete-goods?goods_id='. $goods->id) }}" data-toggle="tooltip" title="Удалить категорию товара"><i class="glyphicon glyphicon-remove"></i></a>
                                        <a href="{{ url('apanel/goods/edit-goods?goods_id='. $goods->id) }}" data-toggle="tooltip" title="Редактировать категорию товара"><i class="glyphicon glyphicon-pencil"></i></a>
                                        <ul class="list-group">
                                        @forelse ($goods_weights[$city->id][$goods->id] as $weight => $count)
                                            <li class="list-group-item">
                                                <span class="badge">{{ $count }}</span>
                                                {{ $weight }}
                                            </li>
                                        @empty
                                            <li class="list-group-item">Нет товара в прайсе</li>
                                        @endforelse
                                        </ul>
                                    @empty
                                        Нет товаров<br>
                                    @endforelse
                                </p>
                            </div>
                        @empty
                            Нет городов
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop