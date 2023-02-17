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

        {!! (new \hcolab\cms\controllers\GadgetsController)->render() !!}

    </div>
</div>
@endsection

@section('scripts')

@endsection