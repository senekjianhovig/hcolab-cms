

<div class="d-flex justify-content-between align-items-center">
    <div>

        @if(in_array('create' , $actions))
        <a class="ui button mr-2" type="button" href="{{route('page.create', ['page_slug' => $page->slug ])}}">Create
            new
            Record</a>
            @endif

        <button class="ui icon button filter-button" data-enable="{{request()->input('enableFilter')}}"
            onclick="toggleFilterBox($(this))">
            <i class="filter icon"></i>
        </button>
    </div>

    <div class="pagination-area">
        @include('CMSViews::grid.pagination', ['page' => $page ])
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
        <a href="{{route('page' , ['page_slug' => $page->slug , $grid_menu_item["field"] => $grid_menu_item["value"] ])}}" class="@if(request()->input($grid_menu_item["field"]) == $grid_menu_item["value"] ) active @endif item"> {{ $grid_menu_item["label"] }}</a>
    @endforeach

</div>
@endif

<form id="filters-form" action="">
    <div class="my-3 table-wrapper ui loading-screen" style="overflow-x:auto;">
        <table class="ui  blue  table">
            @include('CMSViews::grid.grid-header', ['page' => $page ])
            @include('CMSViews::grid.grid-body', ['page' => $page , 'actions' => $actions ])
        </table>
    </div>
</form>

<div class="pagination-area d-flex justify-content-end">
    @include('CMSViews::grid.pagination', ['page' => $page ])
</div>


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

