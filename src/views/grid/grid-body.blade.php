@php
$rows = $page->getRows();
$columns = $page->getColumns();
$related_tables = $page->getRelatedTables();
@endphp


<tbody @if($page->sortable) class="sortable" data-attr-table="{{$page->entity}}" @endif>
    @forelse($rows AS $row)
    <tr class="row-{{ $row->id }} ui-state-default" data-attr-id="{{ $row->id }}" @if($page->sortable)
        data-attr-order={{$row->orders}} @endif>

        <td>
            <div class="ui icon top left pointing dropdown button">
                <i class="ellipsis  vertical icon"></i>
                <div class="menu">
                    <a href="{{route('page.edit', ['page_slug'=> $page->slug , 'id'=> $row->id])}}"
                        class="item">Edit</a>
                    <a href="javascript:;" onclick="" class="item">Delete</a>
                </div>
            </div>
        </td>

        @foreach($columns AS $column)
        <td> {!! process_grid_field($row, $column, $related_tables) !!}</td>
        @endforeach
    </tr>

    @empty

    <tr>
        <td colspan="{{sizeof($columns)+1}}">
            <h5 class="my-2"> No Data Found </h5>
        </td>
    </tr>

    @endforelse
</tbody>