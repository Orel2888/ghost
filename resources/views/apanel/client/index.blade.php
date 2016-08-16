@extends('apanel.app')

@section('title', 'Клиенты')

@section('content')
    <div class="row wrap">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Клиенты
                </div>

                <div class="panel-body">
                    {!! $form_filter !!}
                </div>

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ник</th>
                        <th>TG ID</th>
                        <th>TG username</th>
                        <th>Рейтинг</th>
                        <th>Баланс</th>
                        <th>Кол-во покупок</th>
                        <th>Дата рег.</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($clients as $client)
                    <tr>
                        <td>{{ $client->id }}</td>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->tg_chatid }}</td>
                        <td>{{ $client->tg_username }}</td>
                        <td>{{ $client->rating }}</td>
                        <td>{{ $client->balance }}</td>
                        <td>{{ $client->count_purchases }}</td>
                        <td>{{ $client->created_at->format('d-m-y H:i') }}</td>
                    </tr>
                        @empty
                    <tr>
                        <td colspan="8">Нет клиентов</td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>

                @if (($paginate = $clients->links()) != '')
                    <div class="panel-footer">
                        {{ $paginate }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop