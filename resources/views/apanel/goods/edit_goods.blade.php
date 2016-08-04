@extends('apanel.app')

@section('title', 'Редактирование товара')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h4>Редактирование товара <i class="glyphicon glyphicon-home"></i> Г. {{ $goods->city->name }} <i class="glyphicon glyphicon-shopping-cart"></i> {{ $goods->name }}</h4>

            @include('apanel.goods.errors')
            @include('apanel.goods.notes')

            <form method="POST">
                {{ csrf_field() }}
                <input type="hidden" value="{{ $goods->id }}">
                <label>Имя товара</label>
                <input type="text" name="name" class="form-control" value="{{ $goods->name }}">
                <br>
                <button type="submit" class="btn btn-success">Сохранить</button>
            </form>
        </div>
    </div>
@stop