<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Instudy API')</title>

    <link rel="stylesheet" href="{{ asset('css/docs.css') }}">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css"
    />
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"
    ></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"
    ></script>
</head>
<body>
@include('partials.header')

<div class="container">
    @include('partials.nav')

    <div class="main-content">
        @yield('content')
    </div>
</div>

@include('partials.footer')
</body>
</html>
