@extends('apanel.app')

@section('title', 'Курьеры')

@section('content')
    <div class="row wrap">

        <ol class="breadcrumb">
            <li class="active">Каталог курьеров</li>
            <li><a href="{{ url('miners/payments') }}">Выплаты</a></li>
        </ol>

        <div class="col-md-12">
            @include('apanel.elements.notify_success')

            <div class="panel panel-primary">
                <div class="panel-heading">
                    Курьеры
                </div>
                <div class="panel-body">
                    {!! $form_filter !!}
                </div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Ставка</th>
                        <th>Баланс</th>
                        <th>Кол-во товара</th>
                        <th>Кол-во товара продано</th>
                        <th>Кол-во товара не найдено</th>
                        <th>Всего кладов</th>
                        <th>Дата рег.</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($miners as $miner)
                    <tr>
                        <td>{{ $miner->id }}</td>
                        <td>{{ $miner->name }}</td>
                        <td>{{ $miner->ante }}</td>
                        <td>{{ $miner->balance }}</td>
                        <td>{{ $miner->counter_goods }}</td>
                        <td>{{ $miner->counter_goods_ok }}</td>
                        <td>{{ $miner->counter_goods_fail }}</td>
                        <td>{{ $miner->counter_total_goods }}</td>
                        <td>{{ $miner->created_at->format('d-m-y H:i') }}</td>
                        <td><a href="{{ url('apanel/miner/'. $miner->id) }}" class="btn btn-sm btn-success">Инфо</a></td>
                    </tr>
                        @empty
                    <tr>
                        <td colspan="10">Нет курьеров</td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
                @if (($paginate = $miners->links()) != '')
                    <div class="panel-footer">
                        {{ $paginate }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop