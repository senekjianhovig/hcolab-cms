<?php
$elements = $page->getElements();
$related_tables = $page->getRelatedTables();

$prev_url = "";
$data = isset($data) ? $data : new \stdClass();

$opened = false;
if($elements[0]->ui->type == 'open div'){
    $opened = true;
}

?>
@php 
$layout = request()->has("compact") && request()->has("compact") == 1 ? "layout-minimal" : "layout";  
$compact = $layout != "layout"; 

$sections = $page->sections;

@endphp
@extends('CMSViews::layout.'.$layout, ['title' => $page->title])

@section('head')
<style>

   
    </style>

@endsection

@section('content')

<div class="container-fluid @if(!$compact) my-3 @endif">
    @if(!$compact)
    <div class="ui segment raised  mb-3">
        <div class="ui tiny breadcrumb">
            <a href="{{route('page.show', ['page_slug'=>$page->slug , 'id' => $id])}}" class="section">{{$page->title}}</a>
            <i class="right arrow icon divider"></i>
            <div class="section active ">  Show Page {{$page->title}} </div>
        </div>
    </div>
    @endif

    <form id="page" method="POST" @if($compact) onsubmit="parent.jQuery.fancybox.close();" @endif action="{{route('page.save' , ['page_slug' => $page->slug ])}}"
        enctype='multipart/form-data' class="ui form @if($compact) py-3 @endif">
        @csrf
        <input type="hidden" name="redirect" value="{{$prev_url}}" />
        @isset($id) 
        <input type="hidden" name="id" value="{{$id}}" />
        @endisset

    
    @foreach($sections as $section)
    
        <div class="ui @if(!$compact) segment @endif raised mt-0 mb-3">  
            <h3>{{$section->title}}</h3>

            <table class="ui very basic table"> 
                
                @foreach ($section->fields as $element)
                <?php try { 


                    $title = $element->ui->label;
                   
                    $element->ui->container = "";
                    $element->ui->disable_label = true;
                    $element->ui->disable_margin = true;
                    ?>
                 {!! $element->is_editable ? "<tr> <td><b>".$title."</b></td> <td>". process_form_field($element , $data , $related_tables) ."</td></tr>" : render_form_field($element , $data , $related_tables) !!}  
                 <?php
                } catch (\Throwable $th) {
                    
                    dd($th);
                    //throw $th;
                }
                ?>
               
                 @endforeach
            </table> 

        </div>
    @endforeach


    @if(isset($page->many_relationships) && is_array($page->many_relationships))
    @foreach($page->many_relationships as $relationship)
    
    @php
        $entity = new $relationship;
        $actions = \hcolab\cms\models\CmsUserRolePermission::getPermissions($entity->entity);

        $entity->grid_url = route('page' , ['page_slug' => $entity->slug , $page->many_relationship_key => $id , 'filter_'.$page->many_relationship_key => 1]);
        $entity->request_params = [ $page->many_relationship_key => $id , 'filter_'.$page->many_relationship_key => 1 ];
        $entity->disable_push_url = 1;
        $entity->enable_popup = 1;
        // $entity->disable_operations = 1;
   @endphp
   

    @if(in_array('read' , $actions))
    <div class="ui segment raised mb-3">
        <h3 class="mb-4"> {{$entity->title}} </h3>

        @include('CMSViews::grid_v2.grid', ['page' => $entity , 'actions' => $actions])

        {{-- <iframe onload="resizeIframe(this)" style="width:100%; border:0; padding:0; margin:0" src="{{route('page' , ['page_slug' => $entity->slug , $page->many_relationship_key => $id  ,'compact'=> true])}}"> </iframe> --}}
    </div>
    @endif 


    @endforeach
@endif

@if(isset($page->sections_editable) && $page->sections_editable)

<div class="ui divider"></div>
<div class="d-flex justify-content-between align-items-center">
    <a href="{{ $prev_url == "" ? route('page', ['page_slug'=>$page->slug]) :  $prev_url}}" class="ui button red"> Cancel </a>
    <button class="ui button" type="submit">Submit</button>
</div>

@endif
    </form>
</div>
@endsection

@section('scripts') 
<script>
    function resizeIframe(obj) {
      obj.style.height = obj.contentWindow.document.documentElement.scrollHeight+ 'px';
      obj.contentWindow.document.body.style = ""; 
    }
  </script>
@endsection