<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('head')

    <link rel="stylesheet" type="text/css" href="{{env('APP_URL')}}/hcolab/cms/css/loader-start.css">
    <link rel="stylesheet" type="text/css" href="{{env('APP_URL')}}/hcolab/cms/css/app.css">
    <link rel="stylesheet" type="text/css" href="{{env('APP_URL')}}/hcolab/cms/css/grid-system.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.0/semantic.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

</head>

<body style="background-color: #f8f7f7">
    <div class="screen-loader-init screen-loader-hide"></div>

    @include('CMSViews::partials.sidebar')
    <div class="main-content">
        @include('CMSViews::partials.header', ["title" => $title ])
        @yield('content')
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/2d0d0c6705.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.0/semantic.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script src="{{env('APP_URL')}}/hcolab/cms/js/script.js"></script>
    @yield ('scripts')

    <link rel="stylesheet" type="text/css" href="{{env('APP_URL')}}/hcolab/cms/css/loader-end.css">
</body>

</html>