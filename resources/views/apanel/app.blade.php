<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>@yield('title', 'Apanel')</title>

    <script src="{{ asset('js/app.js') }}"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

@if (Auth::guard('admin')->check())
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ url('apanel') }}"><i class="glyphicon glyphicon-globe"></i> GHOST</a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li @if(Request::is('apanel/goods')) class="active" @endif><a href="{{ url('apanel/goods') }}">Города и товар</a></li>
                    <li @if(Request::is('apanel/goods-price*')) class="active" @endif><a href="{{ url('apanel/goods-price') }}">Прайс товаров</a></li>
                    <li @if(Request::is('apanel/client*')) class="active" @endif><a href="{{ url('apanel/client') }}">Клиенты</a></li>
                    <li @if(Request::is('apanel/miner*')) class="active" @endif><a href="{{ url('apanel/miner') }}">Курьеры</a></li>
                    <li @if(Request::is('apanel/purchase*')) class="active" @endif><a href="{{ url('apanel/purchase') }}">Покупки</a></li>
                    <li @if(Request::is('apanel/notebook*')) class="active" @endif><a href="{{ url('apanel/notebook') }}">Блокнот</a></li>
                </ul>
            </div>
        </div>
    </nav>
@endif

@yield('content')

<script type="text/javascript">
    (new App()).run();
</script>
</body>
</html>