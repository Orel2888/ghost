@extends('apanel.app')

@section('title', 'Каталог адресов')

@section('content')
<div class="row wrap">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                Каталог адресов
            </div>
            <div class="panel-body">
                {!! $form_filter !!}
            </div>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Товар ID</th>
                    <th>Минер ID</th>
                    <th>Вес</th>
                    <th>Адрес</th>
                    <th>Резерв</th>
                    <th>Цена</th>
                    <th>Дата доб.</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($goods_price as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->goods_id }}</td>
                        <td>{{ $item->miner_id }}</td>
                        <td>{{ $item->weight }}</td>
                        <td>{{ $item->address }}</td>
                        <td>{{ $item->reserve }}</td>
                        <td>{{ $item->cost }}</td>
                        <td>{{ $item->created_at->format('d-m-y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Пусто</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            @if (($paginate = $goods_price->links()) != '')
            <div class="panel-footer">
                {{ $paginate }}
            </div>
            @endif
        </div>
    </div>
</div>
@stop