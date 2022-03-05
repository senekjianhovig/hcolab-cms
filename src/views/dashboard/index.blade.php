@extends('CMSViews::layout.layout', ['title' => 'Dashboard'])

@section('head')
<title> Dashboard </title>
@endsection

@section('content')
<div class="container-fluid my-5">
    <h3>Welcome to our Content Management System!</h3>
    <div class="ui indicating progress active" data-percent="95">
        <div class="bar" style="transition-duration: 300ms; width: 27%;"></div>
        <div class="label">Completed 27%</div>
    </div>
</div>
@endsection

@section('scripts')

@endsection