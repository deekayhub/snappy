<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png')}}" type="image/x-icon">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="{{ asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <style>
            .register-login {
                background: url('{{ asset('assets/images/hero-img.png') }}') no-repeat center center;
                background-size: contain;
                background-repeat: no-repeat;
                height: 100%;
                background-position: center;
            }
        </style>
        <!-- Scripts -->
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    </head>
    <body class="font-sans text-gray-900 antialiased">
        @include('layouts.header')
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            {{-- <div class="text-center">
                <a href="/">
                    <img src="{{ asset('assets/images/snappy-logo.png') }}" width="150" alt="snappy logo">
                </a>
            </div> --}}

            <div class="container py-3">
                <div class="row align-items-center" style="min-height: 60vh;">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @include('layouts.footer')
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </body>
</html>
