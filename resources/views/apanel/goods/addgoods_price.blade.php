@extends('apanel.app')

@section('title', 'Добавление товара в прайс')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <ol class="breadcrumb">
                <li><a href="{{ url('apanel/goods') }}">Товар</a></li>
                <li class="active">Добавление товара в прайс</li>
            </ol>

            <h4>Г. <i class="glyphicon glyphicon-home"></i> {{ $goods->city->name }} </h4>
            <h5>Товар <i class="glyphicon glyphicon-shopping-cart"></i> {{ $goods->name }} </h5>

            @include('apanel.goods.notes')

            @if (isset($addresses))
            <div class="panel panel-primary" id="adding">
                <div class="panel-heading">
                    Добавление адресов
                </div>
                <div class="panel-body">
                    @if (Request::input('type') == 'adding')
                        @include('apanel.goods.errors')
                        @include('apanel.goods.notes')
                    @endif
                    <form method="POST">
                        <label>Загружаемые адреса</label>
                        <div class="well">
                            @foreach ($addresses as $index => $address)
                                {{ ++$index }}) {{ $address }}<br>
                            @endforeach
                        </div>
                        <label>Вес</label>
                        <input type="text" name="weight" value="{{ old('weight') }}" class="form-control">
                        <label>Цена</label>
                        <input type="text" name="cost" value="{{ old('cost') }}" class="form-control">
                        <label>Минер</label>
                        <select name="miner_id" class="form-control">
                            @foreach ($miners as $miner)
                                <option value="{{ $miner->id }}">{{ $miner->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="goods_id" value="{{ $goods_id }}">
                        <input type="hidden" name="type" value="adding">
                        {{ csrf_field() }}
                        <br>
                        <button type="submit" class="btn btn-success">Добавить</button>
                    </form>
                </div>
            </div>
            @endif

            <div class="panel panel-primary" id="import-file">
                <div class="panel-heading">
                    Импорт из файла
                </div>
                <div class="panel-body">
                    @if (Request::input('type') == 'file')
                        @include('apanel.goods.errors')
                        @include('apanel.goods.notes')
                    @endif
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="goods_id" value="{{ $goods->id }}">
                        <input type="hidden" name="type" value="file">
                        <input type="file" name="goods_file" class="form-control">
                        {{ csrf_field() }}
                        <br><button type="submit" class="btn btn-success">Загрузить</button>
                    </form>
                    <h3>Формат адресов</h3>
                    <div class="well">
                        1) Адрес<br>
                        2) Адрес<br>
                        3) Адрес<br>
                    </div>
                </div>
            </div>

            <div class="panel panel-primary" id="import-list">
                <div class="panel-heading">
                    Импорт списка
                </div>
                <div class="panel-body">
                    @if (Request::input('type') == 'list')
                        @include('apanel.goods.errors')
                        @include('apanel.goods.notes')
                    @endif
                    <form method="POST">
                        <input type="hidden" name="goods_id" value="{{ $goods->id }}">
                        <input type="hidden" name="type" value="list">
                        <label>Список</label>
                        <textarea rows="20" class="form-control" name="goods_list"></textarea>
                        {{ csrf_field() }}
                        <br><button type="submit" class="btn btn-success">Загрузить</button>
                    </form>
                    <h3>Формат адресов</h3>
                    <div class="well">
                        1) Адрес<br>
                        2) Адрес<br>
                        3) Адрес<br>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
