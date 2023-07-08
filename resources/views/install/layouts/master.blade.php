<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> @yield('title')</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/install.css') }}" rel="stylesheet">

    <script src="{{ asset('assets/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/jQueryUi/jquery-ui.min.js') }}" type="text/javascript"></script>
</head>
<body class="master">
<div class="box">
    <div class="header">
        <h1 class="header__title">Signal Loans - Setup</h1>
    </div>

    <div class="main">
        <h2>@yield('title')</h2>
        <hr>
        <div class="row">
            <div class="col-md-12">
                @if(\Illuminate\Support\Facades\Session::has('message'))
                    <div class="alert alert-danger }}">
                        {{ \Illuminate\Support\Facades\Session::get("message") }}
                    </div>
                @endif
                @yield('container')
            </div>
        </div>
    </div>
</div>
</body>
</html>
