@extends('CMSViews::layout.layout', ['title' => 'Dashboard'])

@section('head')
<title> Dashboard </title>

<style>

h3 {
    margin-top: 30px !important;
}

    </style>

@endsection

@php
    $charts = [];
@endphp

@section('content')

<div class="container-fluid mt-4 mb-5">
    <div class="row">

        @if(env('ENABLE_CMS_DASHBOARD_COPYRIGHT') == 1)
        <div class="col-lg-12 mb-3">
            <div class="ui segment  px-4 py-5 ">
                <h2 class="my-0 mb-3">Welcome {{session('admin')->first_name}}!</h2> 
                <p>
                    You are currently logged in and using the content management system.
                </p>
            </div>
        </div>
        @endif

        {!! (new \hcolab\cms\controllers\GadgetsController)->render() !!}

    </div>
</div>
@endsection

@section('scripts')

@endsection