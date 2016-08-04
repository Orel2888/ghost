@extends('apanel.app')

@section('title', 'Удаление товара')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <ol class="breadcrumb">
                <li><a href="{{ url('apanel/goods') }}">Товар</a></li>
                <li class="active">Удаление товара</li>
            </ol>

            @include('apanel.goods.errors')
            @include('apanel.goods.notes')

            <h4>Удаление товара <i class="glyphicon glyphicon-home"></i> Г. {{ $goods->city->name }} <i class="glyphicon glyphicon-shopping-cart"></i> {{ $goods->name }}</h4>
            Вместе с категорией товара, будут удалены все товары в прайс листе.

            <form method="POST">
                <input type="hidden" name="goods_id" value="{{ $goods->id }}">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
        </div>
    </div>
@stop