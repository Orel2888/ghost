@extends('apanel.app')

@section('title', 'Удаление города')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h4>Удаление города <i class="glyphicon glyphicon-home"></i> Г. {{ $city->name }}</h4>
            Удалив город вместе с ним удалятся категории товаров, товары в прайсе и заказы по товарам.

            <form method="POST">
                {{ csrf_field() }}
                <br>
                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
        </div>
    </div>
@stop