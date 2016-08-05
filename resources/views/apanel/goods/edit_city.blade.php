@extends('apanel.app')

@section('title', 'Редактирование города')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h4>Редактирование города <i class="glyphicon glyphicon-home"></i> Г. {{ $city->name }}</h4>

            @include('apanel.goods.errors')
            @include('apanel.goods.notes')

            <form method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="city_name" value="{{ $city->id }}">
                <label>Имя города</label>
                <input type="text" name="name" class="form-control" value="{{ $city->name }}">
                <br>
                <button type="submit" class="btn btn-success">Сохранить</button>
            </form>
        </div>
    </div>
@stop