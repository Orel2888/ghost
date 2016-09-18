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

                </table>
            </div>
        </div>

    </div>
@stop