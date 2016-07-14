@extends('apanel.app')

@section('title', 'Добавление категории товара')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <ol class="breadcrumb">
                <li><a href="{{ url('apanel/goods') }}">Товар</a></li>
                <li class="active">Добавление категории товара</li>
            </ol>

            @include('apanel.goods.errors')
            @include('apanel.goods.notes')

            <form method="POST">
                <label>
                    Г. <i class="glyphicon glyphicon-home"></i> {{ $city->name }}
                </label>
                <input type="hidden" name="city_id" value="{{ $city->id }}">
                <br>
                <label>Имя товара</label>
                <input type="text" class="form-control" name="goods_name" placeholder="Goods name">
                {{ csrf_field() }}
                <br>
                <button type="submit" class="btn btn-success">Создать</button>
            </form>

        </div>
    </div>
@stop