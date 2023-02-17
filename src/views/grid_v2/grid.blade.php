

@php
    if(count($page->columns) == 0){
       $page->setColumns();
    }

    if(count($page->elements) == 0){
        $page->setElements();
    }

    if(isset($page->sections) && count($page->elements) == 0){
        $page->setElements();
    }
    
    if(isset($page->request_params) && is_array($page->request_params)){
        request()->merge($page->request_params);
    }

    

@endphp

<div class="my-3  ui loading-screen" style="overflow-x:auto;">
        <table id="grid-{{$page->slug}}" @if(isset($page->disable_push_url) && $page->disable_push_url == 1) disable-push-url="1" @endif data-url="@if(isset($page->grid_url) && $page->grid_url) {{$page->grid_url}} @else {{route('page' , ['page_slug' => $page->slug])}}@endif" class="ui  blue  table">
            @include('CMSViews::grid_v2.grid-header', ['page' => $page ])
            @include('CMSViews::grid_v2.grid-body', ['page' => $page , 'actions' => $actions  ])
        </table>
</div>

@once
<div class="ui basic modal mini confirm-delete">
    <div class="ui icon header pb-0">
        <i class="trash alternate outline icon"></i>

    </div>
    <div class="content d-flex justify-content-center">
        <h4> Are you sure you want to delete?</h4>
    </div>
    <div class="actions d-flex justify-content-center">
        <div class="ui red basic cancel inverted button negative">
            <i class="remove icon"></i>
            No
        </div>
        <div class="ui green ok inverted button positive">
            <i class="checkmark icon"></i>
            Yes
        </div>
    </div>
</div>
@endonce