<!DOCTYPE html>
<html dir="ltr" lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GiáVàng.vn — Admin</title>
    <link rel="stylesheet" href="{{ asset('backend/css/theme/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/fonts/font-awesome/css/font-awesome.min.css') }}">
    @stack('css-3rd')
    <link rel="stylesheet" href="{{ asset('backend/css/common.css') }}">
    @stack('style')
</head>
<body>
<div class="main-wrapper">
    <div class="preloader" style="display: none;">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    @yield('content')
</div>

<script src="{{ asset('backend/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('backend/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('backend/js/theme/custom.min.js') }}"></script>
@stack('script')
</body>
</html>
