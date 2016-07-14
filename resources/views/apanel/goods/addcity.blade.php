@extends('apanel.app')

@section('title', 'Добавление города')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <ol class="breadcrumb">
                <li><a href="{{ url('apanel/goods') }}">Товар</a></li>
                <li class="active">Добавление города</li>
            </ol>

            @include('apanel.goods.errors')
            @include('apanel.goods.notes')

            <form method="POST">
                <label>Имя города</label>
                <input type="text" class="form-control" name="name" placeholder="Сity" value="{{ old('name') }}">
                {{ csrf_field() }}
                <br>
                <button type="submit" class="btn btn-success">Создать</button>
            </form>
        </div>
    </div>
@stop