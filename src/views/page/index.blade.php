@extends('CMSViews::layout.layout', ['title' => $page->title])

@section('head')
<meta name="entity" content="{{$page->entity}}">
<title> {{$page->title}} </title>
@endsection

@section('content')
<div class="container-fluid my-3">
    @include('CMSViews::grid.grid', ['page'=> $page ])
</div>
@endsection

@section('scripts')
<script>
    @if(!request()->has("enableFilter") || (int) request()->input("enableFilter") != 1)
        $('#filter-row').slideUp(0);
    @endif
</script>
@endsection