@extends('CMSViews::layout.layout', ['title' => $page->title])

@section('head')
    <meta name="entity" content="{{ $page->entity }}">
    <title> {{ $page->title }} </title>
@endsection

@section('content')
    <div class="container-fluid my-3">
        @include('CMSViews::grid.grid', ['page' => $page])
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
