@php
$rows = $page->getRows();
$columns = $page->getColumns();
$related_tables = $page->getRelatedTables();

$compact = request()->has("compact") && request()->has("compact") == 1;
@endphp


<tbody @if ($page->sortable) class="sortable" data-attr-table="{{ $page->entity }}" @endif>
    @forelse($rows AS $row)
        <tr class="row-{{ $row->id }} ui-state-default" data-attr-id="{{ $row->id }}"
            @if ($page->sortable) data-attr-order={{ $row->orders }} @endif>

            @if(!$compact)
            <td>
                @if((in_array('update' , $actions) || in_array('delete' , $actions) || (isset($page->grid_operations) && is_array($page->grid_operations))) )
               
                <div class="ui icon top left pointing dropdown button">
                    <i class="ellipsis  vertical icon"></i>
                    <div class="menu">


                        @if(in_array('update' , $actions) )

                        <a href="@if (isset($page->grid_operations['edit']['link'])) {{ str_replace( '{id}' , $row->id , $page->grid_operations['edit']['link']) }} @else {{ route('page.edit', ['page_slug' => $page->slug, 'id' => $row->id]) }} @endif"
                            class="item">
                            @if (isset($page->grid_operations['edit']['label']))
                                {{ $page->grid_operations['edit']['label'] }}
                            @else
                                Edit
                            @endif
                        </a>
                        @endif

                        @if(in_array('delete' , $actions))
                        <a href="javascript:;" onclick="deleteRow('{{ route('page.delete', ['page_slug' => $page->slug, 'id' => $row->id]) }}')"
                            class="item">
                                Delete
                        </a>
                        @endif

                        @if (isset($page->grid_operations) && is_array($page->grid_operations))
                            @foreach ($page->grid_operations as $operation_key => $operation_value)
                                @if (!in_array($operation_key, ['edit', 'delete']))
                                    <a href="{{ str_replace(['{id}' , '{token}' ] , [$row->id , md5($row->id.env('APP_KEY'))] , $operation_value['link']) }}"
                                        class="item">{{ $operation_value['label'] }}</a>
                                @endif
                            @endforeach
                        @endif

                    </div>
                </div>
              @endif
            </td>
            @endif

            @foreach ($columns as $column)
          
                <td> {!! process_grid_field($row, $column, $related_tables) !!}</td>
            @endforeach
        </tr>

    @empty

        <tr>
            <td colspan="{{ sizeof($columns) + 1 }}">
                <h5 class="my-2"> No Data Found </h5>
            </td>
        </tr>

    @endforelse
</tbody>
