<?php
$elements = $page->getElements();
$related_tables = $page->getRelatedTables();

$prev_url = "";
$data = isset($data) ? $data : new \stdClass();



if(app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName() == "page"){ $prev_url = $_SERVER['HTTP_REFERER']; }
?>

@extends('CMSViews::layout.layout', ['title' => $page->title])

@section('head') @endsection

@section('content')

<div class="container-fluid my-3">
    <div class="ui segment raised  mb-3">
        <div class="ui tiny breadcrumb">
            <a href="{{route('page', ['page_slug'=>$page->slug])}}" class="section">{{$page->title}}</a>
            <i class="right arrow icon divider"></i>
            <div class="section active "> @isset($id) Edit Page {{$page->title}} @else Create Page @endisset </div>
        </div>
    </div>

    <div id="page-fields-segment" class="ui segment raised mb-3">
        <h3> Add new record </h3>
        <form id="page" method="POST" action="{{route('page.save' , ['page_slug' => $page->slug ])}}"
            enctype='multipart/form-data' class="ui form py-3">
            @csrf
            <input type="hidden" name="redirect" value="{{$prev_url}}" />
            <div class="row">
                @foreach ($elements as $element)
                @switch($element->ui->type)
                @case("textfield")
                @case("email")
                @case("number")
                @include('CMSViews::form.textfield', [ "element" => $element, "data" => $data ])
                @break
                @case("disabled_textfield")
                @include('CMSViews::form.disabled-textfield', [ "element" => $element, "data" => $data ])
                @break
                @case("select")
                @case("multiple select")
                @include('CMSViews::form.select', [ "element" => $element, "data" => $data , 'related_tables' =>
                $related_tables])
                @break

                @case("boolean checkbox")
                @include('CMSViews::form.boolean-checkbox', [ "element" => $element, "data" => $data ])
                @break

                @case("textarea")
                @include('CMSViews::form.textarea', [ "element" => $element, "data" => $data ])
                @break

                @case("password")
                @include('CMSViews::form.password', [ "element" => $element, "data" => $data ])
                @break
                @case("date picker")
                @include('CMSViews::form.datepicker', [ "element" => $element, "data" => $data ])
                @break
                @case("date time picker")
                @include('CMSViews::form.datetimepicker', [ "element" => $element, "data" => $data ])
                @break

                @case("wysiwyg")
                @include('CMSViews::form.wysiwyg', [ "element" => $element, "data" => $data ])
                @break

                @case("url")
                @include('CMSViews::form.url', [ "element" => $element, "data" => $data ])
                @break

                @case("file")
                @case("multiple file")
                @case("image")
                @include('CMSViews::form.file', [ "element" => $element, "data" => $data])
                @break

                {{--
                @case("file")
                @case("image")
                @include('form.file', [ "element" => $element, "data" => $data ])
                @break

                @case("multiple image")
                @case("multiple file")
                @include('form.multiple-file', [ "element" => $element, "data" => $data ])
                @break



                @case("urls")
                @include('form.urls', [ "element" => $element, "data" => $data ])
                @break --}}
                @endswitch
                @endforeach
            </div>
            <div class="ui divider"></div>
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{route('page', ['page_slug'=>$page->slug])}}" class="ui button red"> Cancel </a>
                <button class="ui button" type="submit">Submit</button>
            </div>



            {{-- <div class="ui error message"></div> --}}
        </form>
    </div>
</div>
@endsection

@section('scripts') @endsection