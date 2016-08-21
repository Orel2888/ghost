@extends('apanel.app')

@section('title', 'Курьер '. $miner->name)

@section('content')
    <div class="row wrap">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Курьер <b>{{ $miner->name }}</b>
                </div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <span class="badge">{{ $miner->id }}</span>
                            ID
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $miner->ante }}</span>
                            Ставка
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $miner->balance }}</span>
                            Баланс
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $miner->counter_goods + $miner->counter_goods_ok * $miner->ante }}</span>
                            Ожидаемый баланс
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $miner->counter_goods }}</span>
                            Кол-во товара
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $miner->counter_goods_ok }}</span>
                            Кол-во товара продано
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $miner->counter_goods_fail }} - {{ $miner->counter_goods_ok * $miner->counter_goods_fail / 100 }}%</span>
                            Кол-во товара не найдено
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $miner->counter_total_goods }}</span>
                            Кол-во товара всего
                        </li>
                        <li class="list-group-item">
                            Дата регистрации <span class="pull-right"><b>{{ $miner->created_at->format('d-m-Y H:i') }}</b></span>
                        </li>
                        <a href="{{ url('apanel/miner/createpay?id='. $miner->id) }}" class="list-group-item list-group-item-info">
                            <h4><i class="glyphicon glyphicon-pencil"></i> Корректировка баланса</h4>
                            <p class="list-group-item-text">Редактировать баланс курьера</p>
                        </a>
                        <a href="{{ url('apanel/miner/createpay?id='. $miner->id) }}" class="list-group-item list-group-item-success">
                            <h4 class="list-group-item-heading"><i class="glyphicon glyphicon-ruble"></i> Выплатить</h4>
                            <p class="list-group-item-text">Создание заявки на выплату. Счетчики баланс, ожидаемый баланс, количество товара, количество товара продано, количество товара не найдено, сбрасываются. Создается заявка на выплаты.</p>
                        </a>
                        <a href="{{ url('apanel/miner/delete?id='. $miner->id) }}" class="list-group-item list-group-item-danger">
                            <h4 class="list-group-item-heading"><i class="glyphicon glyphicon-remove"></i> Уволить</h4>
                            <p class="list-group-item-text">Удалить курьера из базы безвозвратно</p>
                        </a>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop