@extends('apanel.app')

@section('title', 'Создание покупки '. $client->name)

@section('content')
    @include('apanel.elements.modal_goods', compact('cities'))
    <div class="row wrap">
        <div class="col-md-12">
            <a href="{{ route('client.show', $client->id) }}" class="btn btn-default">
                <i class="glyphicon glyphicon-menu-left"></i> {{ $client->name }}
            </a><br><br>
        </div>
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Создание покупки {{ $client->name }} {{ '@'. $client->tg_username }}
                </div>
                <div class="panel-body">
                    Баланс: {{ $client->balance }}<br>
                    <hr>

                    {!! Form::open(['route' => ['client.purchase.store', $client->id]]) !!}
                    {!! method_field('put') !!}

                    <label>Товар <a id="go-select-goods" class="btn btn-xs btn-success">Выбрать товар</a></label><br>
                    <div class="well" id="selected-goods" style="display: none">
                        <i class="glyphicon glyphicon-home"></i> <span id="city-name"></span><br>
                        <i class="glyphicon glyphicon-shopping-cart"></i> <span id="goods-name"></span>
                        {!! Form::hidden('goods_id', null, ['id' => 'input-goods-id']) !!}
                    </div>

                    {!! Form::selectControl('Курьер', 'miner_id', $miner_list) !!}
                    {!! Form::control('Вес', 'weight') !!}
                    {!! Form::control('Цена', 'cost') !!}
                    {!! Form::textareaControl('Товар', 'address') !!}
                    {!! Form::checkboxControl('Отправить покупку в телеграм', 'notify_telegram', 1, 1) !!}
                    {!! Form::checkboxControl('Снять средства с баланса', 'decrement_balance', 1, 1) !!}
                    {!! Form::buttonType('Создать', 'success') !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <script>
//        $('#modal-select-goods').modal('show');
    </script>
@stop