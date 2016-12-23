@extends('apanel.app')

@section('title', 'Подтверждение действия')

@section('content')
    <div class="row wrap">
        <div class="col-md-offset-4 col-md-4">
            <div class="well">
                <h4>{{ $message }}</h4>
                <br>
                @if (isset($attributes['form']))
                    {!! Form::open(['url' => $urlUp, 'class' => 'form-inline']) !!}
                    {!! method_field($attributes['form']) !!}
                    {!! Form::buttonType('OK', 'success') !!}
                    <a href="{{ $urlCancel }}" class="btn btn-danger">Отмена</a>
                    {!! Form::close() !!}
                @else
                    <a href="{{ $urlUp }}" class="btn btn-success">ОК</a>
                    <a href="{{ $urlCancel }}" class="btn btn-danger">Отмена</a>
                @endif
            </div>
        </div>
    </div>
@stop