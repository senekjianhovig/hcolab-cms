<?php

$table = $page->getRows();

$window = Illuminate\Pagination\UrlWindow::make($table);
$elements =  array_filter([
    $window['first'],
    is_array($window['slider']) ? '...' : null,
    $window['slider'],
    is_array($window['last']) ? '...' : null,
    $window['last'],
]);
?>

@if ($table->hasPages())
<div class="ui pagination menu" role="navigation">

    @if ($table->onFirstPage())
    <a class="icon item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')"> <i
            class="left chevron icon"></i> </a>
    @else
    <a class="icon item" href="javascript:;" onclick="changePage({{$table->currentPage()-1}})" rel="prev"
        aria-label="@lang('pagination.previous')"> <i class="left chevron icon"></i> </a>
    @endif

    @foreach ($elements as $element)
    @if (is_string($element))
    <a class="icon item disabled" aria-disabled="true">{{ $element }}</a>
    @endif
    @if (is_array($element))
    @foreach ($element as $page_number => $url)
    @if ($page_number == $table->currentPage())
    <a class="item active" href="javascript:;" onclick="changePage('{{$page_number}}')" aria-current="page">{{
        $page_number }}</a>
    @else
    <a class="item" href="javascript:;" onclick="changePage('{{$page_number}}')">{{ $page_number }}</a>
    @endif
    @endforeach
    @endif
    @endforeach

    @if ($table->hasMorePages())
    <a class="icon item" href="javascript:;" onclick="changePage({{$table->currentPage()+1}})" rel="next"
        aria-label="@lang('pagination.next')">
        <i class="right chevron icon"></i> </a>
    @else
    <a class="icon item disabled" aria-disabled="true" aria-label="@lang('pagination.next')"> <i
            class="right chevron icon"></i> </a>
    @endif
</div>
@endif
