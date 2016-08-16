@extends('apanel.app')

@section('title', 'Покупки')

@section('content')
    <div class="row wrap">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Покупки
                </div>
                <div class="panel-body">
                    {!! $form_filter !!}
                </div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Город ID</th>
                        <th>Товар ID</th>
                        <th>Минер ID</th>
                        <th>Клиент ID</th>
                        <th>Вес</th>
                        <th>Адрес</th>
                        <th>Цена</th>
                        <th>Статус</th>
                        <th>Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->id }}</td>
                        <td>{{ $purchase->city_id }}</td>
                        <td>{{ $purchase->goods_id }}</td>
                        <td>{{ $purchase->miner_id }}</td>
                        <td>{{ $purchase->client_id }}</td>
                        <td>{{ $purchase->weight }}</td>
                        <td>{{ $purchase->address }}</td>
                        <td>{{ $purchase->cost }}</td>
                        <td>{{ $purchase->status == 1 ? 'OK' : 'Fail' }}</td>
                        <td>{{ $purchase->created_at->format('d-m-y H:i') }}</td>
                    </tr>
                        @empty
                    <tr>
                        <td colspan="9">Нет покупок</td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
                @if (($paginate = $purchases->links()) != '')
                    <div class="panel-footer">
                        {{ $paginate }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop