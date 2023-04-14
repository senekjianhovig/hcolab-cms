@php 
$layout = request()->has("compact") && request()->has("compact") == 1 ? "layout-minimal" : "layout";  
$compact = $layout != "layout"; 


$related_tables = $page->getRelatedTables();

@endphp
@extends('CMSViews::layout.'.$layout, ['title' => $page->title , 'entity' => $page->page])

@section('head')
    {{-- <meta name="entity" content="{{ $page->slug }}"> --}}
    <title> {{ $page->title }} </title>

    <style>
        .popup-panel {
            width: 300px !important;
            max-width: 100% !important;
            padding: 0 !important;
        }

        .popup-trigger{
            position: relative;
        }

        .popup-panel .title{
            font-size: 16px;
            font-weight: bold;
          
          
          
        } 
        .popup-panel .header{
            border-bottom: 1px solid #eee;
            padding: 15px;
            /* margin-bottom:  15px;
            padding-bottom: 15px; */
        }

        .popup-panel .filter-item{
            border-bottom: 1px solid #e8ebf0;
            padding: 10px;
            display: block;
        }

        



        .filter-counter{
            position: absolute;
            top:0;
            right:0;
            transform: translate(65% ,-20%);
        }

    </style>

@endsection

{{-- @dd($page->columns) --}}

@php
    $data = new \stdClass();
    $data->id = request()->input('id');


@endphp

@section('content')
        <div class="container-fluid  my-3 ">

            @if(in_array('read' , $actions))
            
    

            <div class="d-flex justify-content-between align-items-center">
                <div>
                  
                    @if(in_array('create' , $actions))
                    <a class="ui button mr-2" type="button" href="{{route('page.create', ['page_slug' => $page->slug ])}}">Create
                        new
                        Record</a>
                        @endif
                   
                    <div class="filter-panel" style="display: inline-block">
                        <button class="ui icon button filter-button popup-trigger" > <i class="filter icon"></i> <div class="counter"> </div> </button>
                        <div class="popup-panel ui flowing popup hidden"> 
                            <form id="filters-form" class="ui form">
                                <div class="header d-flex align-items-center justify-content-between">
                                    <div class="title">Filters </div>
                                    <div class="d-flex align-items-center">
                                        <button data-target="{{$page->slug}}" type="button" class="ui button tiny" onclick="clearFilterTable($(this))"> Clear </button>
                                        <button data-target="{{$page->slug}}" type="button" class="ui button tiny blue" onclick="filterTable($(this))"> Apply </button>
                                    </div>
                                </div>
                               
                                @foreach($page->columns AS $column)


                                    @php 

                                        if(!$column->label){
                                            continue;
                                        }

                                        if(request()->has($column->name)){ $data->{$column->name} = request()->input($column->name); }

                                        $has_filter = request()->has('filter_'.$column->name) && request()->input('filter_'.$column->name) == 1;
                                      

                                    @endphp
                                    <div class="filter-item">
                                        <div class="ui checkbox @if($has_filter) checked @endif filter-item-checkbox" >
                                            <input class="filter-item-checkbox-input" type="checkbox" @if($has_filter) checked @endif name="filter_{{$column->name}}" value="1" onchange="checkFilter($(this))" />
                                            <label> {{$column->label}} </label>
                                        </div>
                                        <div class="filter-field"  @if(!$has_filter) style="display: none" @endif>
                                            @include('CMSViews::grid_v2.filter-item', ['column'=> $column , 'data'=>$data , 'related_tables' => $related_tables])
                                        </div>
                                    </div>
                                @endforeach
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="pagination-area pagination-area-{{$page->slug}}">
                    @include('CMSViews::grid_v2.pagination', ['page' => $page ])
                </div>
            </div>

            
            @if(isset($page->grid_menu) && is_array($page->grid_menu) && count($page->grid_menu) > 0)

@php
    $active_menu = false;
    foreach(collect($page->grid_menu)->pluck('field') as $field){
        if(request()->input($field) != null || request()->input($field) != ""){
            $active_menu = true;
            break;
        }
    }
@endphp

<div class="ui pointing menu">
    <a href="{{route('page' , ['page_slug' => $page->slug ])}}" class="item @if(!$active_menu) active @endif"> All </a>
    @foreach($page->grid_menu as $grid_menu_item)
        <a href="{{route('page' , ['page_slug' => $page->slug , $grid_menu_item["field"] => $grid_menu_item["value"] , 'filter_'.$grid_menu_item["field"] =>1 ])}}" class="@if(request()->input($grid_menu_item["field"]) == $grid_menu_item["value"] ) active @endif item"> {{ $grid_menu_item["label"] }}</a>
    @endforeach

</div>
@endif
       
            @include('CMSViews::grid_v2.grid', ['page' => $page , 'actions' => $actions])
    

            <div class="pagination-area pagination-area-{{$page->slug}} d-flex justify-content-end">
                @include('CMSViews::grid_v2.pagination', ['page' => $page ])
            </div>

            @else
            You dont have permission to view this page
        @endif
        </div>


     


@endsection



@section('scripts')
    <script>


        calculateFilters();

        function checkFilter(elem){
          elem.parents('.filter-item').find('.filter-field').slideToggle();
          calculateFilters();
        }

        @if (!request()->has('enableFilter') || (int) request()->input('enableFilter') != 1)
            $('#filter-row').slideUp(0);
        @endif
    
   
    
    </script>

@endsection
