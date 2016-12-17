@extends('apanel.app')

@section('title', 'Редактирование клиента '. $client->name)

@section('content')
    <div class="row wrap">
        <div class="col-md-12">
            <a href="{{ route('client.show', $client->id) }}" class="btn btn-default">
                <i class="glyphicon glyphicon-menu-left"></i> {{ $client->name }}
            </a><br><br>
        </div>
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Редактирование клиента {{ $client->name }} {{ '@'. $client->tg_username }}
                </div>
                <div class="panel-body">

                    @include('apanel.elements.notify_success')

                    {!! Form::open(['route' => ['client.update', $client->id]]) !!}
                    {!! method_field('put') !!}
                    {!! Form::control('Имя', 'name', $client->name) !!}
                    {!! Form::control('Телеграм', 'tg_username', $client->tg_username) !!}
                    {!! Form::control('Рейтинг', 'rating', $client->rating) !!}
                    {!! Form::control('Баланс', 'balance', $client->balance) !!}
                    {!! Form::control('Количество покупок', 'count_purchases', $client->count_purchases) !!}
                    {!! Form::control('Платежный коммент', 'comment', $client->comment) !!}
                    {!! Form::checkboxControl('Получает уведомления', 'notify', $client->notify, $client->notify) !!}
                    {!! Form::buttonType('Сохрнаить', 'success') !!}
                    <a href="{{ route('client.show', $client->id) }}" class="btn btn-danger">Отмена</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop