<div class="d-flex justify-content-between align-items-center">
    <div>
        <a class="ui button mr-2" type="button" href="{{route('page.create', ['page_slug' => $page->slug ])}}">Create
            new
            Record</a>

        <button class="ui icon button filter-button" data-enable="{{request()->input('enableFilter')}}"
            onclick="toggleFilterBox($(this))">
            <i class="filter icon"></i>
        </button>
    </div>

    <div class="pagination-area">
        @include('CMSViews::grid.pagination', ['page' => $page ])
    </div>

</div>

<form id="filters-form" action="">
    <div class="my-3 table-wrapper ui loading-screen" style="overflow-x:auto;">
        <table class="ui  blue  table">
            @include('CMSViews::grid.grid-header', ['page' => $page ])
            @include('CMSViews::grid.grid-body', ['page' => $page ])
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

