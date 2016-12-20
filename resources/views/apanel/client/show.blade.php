@extends('apanel.app')

@section('title', 'Клиент')

@section('content')
    <div class="row wrap">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Клиент {{ $client->name }} {{ '@'. $client->tg_username }}
                </div>

                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <span class="badge">{{ $client->name }}</span>
                            Имя
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $client->tg_username }}</span>
                            Telegram username
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $client->rating }}</span>
                            Рейтинг
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $client->balance }}</span>
                            Баланс
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $client->count_purchases }}</span>
                            Количество покупок
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $client->comment }}</span>
                            Платежный коммент
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $client->notify ? 'Да' : 'Нет' }}</span>
                            Получает уведомления
                        </li>
                        <li class="list-group-item">
                            <span class="badge">{{ $client->created_at->format('d-m-Y H:i:s') }}</span>
                            Дата регистрации
                        </li>
                        <a href="{{ route('client.edit', $client->id) }}" class="list-group-item list-group-item-info">
                            <h4><i class="glyphicon glyphicon-pencil"></i> Редактировать</h4>
                        </a>
                        <a href="{{ route('client.purchase', $client->id) }}" class="list-group-item list-group-item-info">
                            <h4><i class="glyphicon glyphicon-shopping-cart"></i> Оформить покупку</h4>
                        </a>
                        <a href="{{ route('client.delete', $client->id) }}" class="list-group-item list-group-item-danger">
                            <h4><i class="glyphicon glyphicon-remove"></i> Удалить</h4>
                        </a>
                        <a href="{{ url('apanel/purchase?filter=1&f_client_id='. $client->id) }}" target="_blank" class="list-group-item"><i class="glyphicon glyphicon-search"></i> Найти покупки</a>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop