@php $layout = request()->has("compact") && request()->has("compact") == 1 ? "layout-minimal" : "layout";  @endphp


@extends('CMSViews::layout.'.$layout, ['title' => $page->title])

@section('head')
    <meta name="entity" content="{{ $page->slug }}">
    <title> {{ $page->title }} </title>
@endsection

@section('content')
    <div class="@if($layout == "layout") container-fluid my-3 @endif ">

        @if(in_array('read' , $actions))
            @include('CMSViews::grid_v2.grid', ['page' => $page , 'actions' => $actions])
        
            @else
            You dont have permission to view this page
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        @if (!request()->has('enableFilter') || (int) request()->input('enableFilter') != 1)
            $('#filter-row').slideUp(0);
        @endif
    
        function deleteRow(url){
            $('.confirm-delete').modal('show');

            $('.confirm-delete').modal({
                closable  : true,
                onApprove : function() {
                    $('.loading-screen').addClass('loading');
                    $.post( url, {_token : '{{csrf_token()}}'}, 
                        function( data ){ 
                            $('tbody').replaceWith(data.table_body);
                            $('.pagination-area').html(data.pagination);
                            semanticInit();
                            $('.loading-screen').removeClass('loading');
                           
                        });        
                    }}).modal('show');
        }
    
    </script>

@endsection
