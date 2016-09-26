@extends('apanel.app')

@section('title', 'Выплаты курьеров')

@section('content')
    <div class="row wrap">

        <ol class="breadcrumb">
            <li><a href="{{ url('apanel/miner') }}">Курьеры</a></li>
            <li class="active">Выплаты</li>
        </ol>

        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Выплаты
                </div>

                <div class="panel-body">
                    {!! $form_filter !!}
                </div>

                <table class="table table-bordered table-hover">
                    <thead>
                    <th>ID</th>
                    <th>Минер ID</th>
                    <th>Сумма</th>
                    <th>Кол-во находов</th>
                    <th>Кол-во ненаходов</th>
                    <th>Статус</th>
                    <th>Дата</th>
                    <th>Действие</th>
                    </thead>
                    <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->miner_id }}</td>
                            <td>{{ $payment->amount }}</td>
                            <td>{{ $payment->counter_goods_ok }}</td>
                            <td>{{ $payment->counter_goods_fail }}</td>
                            <td>{{ $payment->status }}</td>
                            <td>{{ $payment->created_at->format('d-m-Y H:i:s') }}</td>
                            <td>
                                <div class="btn-group btn-group-xs">
                                    <a href="{{ '' }}" class="btn btn-success btn-xs">Выплатить</a>
                                    <a href="{{ '' }}" class="btn btn-danger btn-xs">Отклонить</a>
                                    <a href="{{ '' }}" class="btn btn-info btn-xs">В обработке</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">Нет данных</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                @if (($paginate = $payments->links()) != '')
                    <div class="panel-footer">
                        {{ $paginate }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@stop