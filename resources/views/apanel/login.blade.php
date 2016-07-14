@extends('apanel.app')

@section('title', 'Логин')

@section('content')
    <br>
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">Аутентификация</div>
            <div class="panel-body">

                @if (!$errors->isEmpty())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                @endif

                <form method="POST">
                    <label>Login</label>
                    <input type="text" name="login" placeholder="login" class="form-control" value="{{ old('login') }}">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="password">
                    <label>
                        Remember me
                        <input type="checkbox" value="1" name="remember" checked>
                    </label>

                    {{ csrf_field() }}

                    <br><button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>
        </div>
    </div>
@stop