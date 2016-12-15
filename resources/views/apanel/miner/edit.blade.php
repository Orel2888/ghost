@extends('apanel.miner.info')

@section('edit-form')
    <div id="form-edit">
        <div class="panel panel-info">
            <div class="panel-heading">
                Редактирование курьера <b>{{ $miner->name }}</b>
            </div>
            <div class="panel-body">

                @if (session()->has('edited-success'))
                    <div class="alert alert-success">{{ session('edited-success') }}</div>
                @endif

                {!! Form::model($miner->minerModel, ['route' => ['miner.update', $miner->id], 'method' => 'PUT']) !!}

                {!! Form::control('Ставка', 'ante', $miner->ante) !!}
                {!! Form::control('Баланс', 'balance', $miner->balance) !!}
                {!! Form::control('Кол-во товара', 'counter_goods', $miner->counter_goods) !!}
                {!! Form::control('Кол-во товара продано', 'counter_goods_ok', $miner->counter_goods_ok) !!}
                {!! Form::control('Кол-во товара не найдено', 'counter_goods_fail', $miner->counter_goods_fail) !!}
                {!! Form::control('Кол-во товара всего', 'counter_total_goods', $miner->counter_total_goods) !!}
                {!! Form::buttonType('Сохранить', 'success') !!}
                <a href="{{ route('miner.show', [$miner->id]) }}" class="btn btn-default">Отмена</a>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop