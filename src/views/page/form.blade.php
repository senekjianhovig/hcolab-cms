<?php
$elements = $page->getElements();
$related_tables = $page->getRelatedTables();

$prev_url = "";
$data = isset($data) ? $data : new \stdClass();

$opened = false;
if($elements[0]->ui->type == 'open div'){
    $opened = true;
}

if(request()->has("redirect_url")){
    $prev_url = request()->input('redirect_url');
}else{
    if(in_array(app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName() , ["page" , "page.show"])){ $prev_url = url()->previous(); }
}

?>
@php 
$layout = request()->has("compact") && request()->has("compact") == 1 ? "layout-minimal" : "layout";  
$compact = $layout != "layout"; 
@endphp
@extends('CMSViews::layout.'.$layout, ['title' => $page->title])

@section('head') @endsection

@section('content')

<div class="container-fluid my-3">
    @if(!$compact)
    <div class="ui segment raised  mb-3">
        <div class="ui tiny breadcrumb">
            <a href="{{route('page', ['page_slug'=>$page->slug])}}" class="section">{{$page->title}}</a>
            <i class="right arrow icon divider"></i>
            <div class="section active "> @isset($id) Edit Page {{$page->title}} @else Create Page @endisset </div>
        </div>
    </div>
    @endif

    <div id="page-fields-segment" 
    class="@if(!$opened) ui  @if(!$compact) segment @endif raised mb-3 @endif"
    >
    @if(!$opened)
        <h3> Add new record </h3>
        @endif
        <form id="page" method="POST" action="{{route('page.save' , ['page_slug' => $page->slug ])}}"
            enctype='multipart/form-data' class="ui form py-3">
            @csrf
            <input type="hidden" name="redirect" value="{{$prev_url}}" />
            @isset($id) 
            <input type="hidden" name="id" value="{{$id}}" />
            @endisset
            @if(!$opened) <div class="row"> @endif

            
                @foreach ($elements as $element)
                {!! process_form_field($element , $data , $related_tables) !!}
                @endforeach
                @if(!$opened)  </div> @endif
      
              

            <div class="ui divider"></div>
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ $prev_url == "" ? route('page', ['page_slug'=>$page->slug]) :  $prev_url}}" class="ui button red"> Cancel </a>
                <button class="ui button" type="submit">Submit</button>
            </div>



            {{-- <div class="ui error message"></div> --}}
        </form>
    </div>
</div>
@endsection

@section('scripts') 

@endsection