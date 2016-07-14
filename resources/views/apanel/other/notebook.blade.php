@extends('apanel.app')

@section('title', 'Блокнот')

@section('content')
    <script type="text/javascript" src="{{ asset('js/gibberish-aes-1.0.0.min.js') }}"></script>
    <script>

        var key = '{{ env('K5') }}';

        $(function () {
            notebook('#notebook-form');
        })
    </script>

    <div class="row wrap">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Блокнот
                </div>
                <div class="panel-body">
                    <form action="{{ url('apanel/notebook-save') }}" id="notebook-form">
                        <label>Содержимое</label>
                        <textarea name="content" id="notebook-content" rows="20" class="form-control">{{ $content }}</textarea>
                        <br>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop