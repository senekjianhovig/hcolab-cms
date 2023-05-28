@php

$rows = $page->getRows();
$columns = $page->getColumns();

$related_tables = $page->getRelatedTables();

$compact = request()->has("compact") && request()->has("compact") == 1;


$enable_popup = isset($page->enable_popup) &&  $page->enable_popup == 1;


$enable_operations = isset($page->disable_operations) &&  $page->disable_operations != 1 || !isset($page->disable_operations);



if(isset($page->sections) && count($page->sections) == 0){
    $page->setSections();
}

$has_sections = isset($page->sections) && count($page->sections) > 0;

@endphp


<tbody @if ($page->sortable) class="sortable" @endif >
    @forelse($rows AS $row)

    

        <tr @if(in_array('update' , $actions) && $has_sections && $enable_popup) data-src="@if (isset($page->grid_operations['show']['link'])) {{ str_replace( '{id}' , $row->id , $page->grid_operations['show']['link']) }}&compact=1 @else {{ route('page.show', ['page_slug' => $page->slug, 'id' => $row->id , 'compact' => 1]) }}@endif" onclick="openFancybox($(this))" @endif class="@if($has_sections && $enable_popup) grid-row @endif row-{{ $row->id }} ui-state-default" data-attr-id="{{ $row->id }}"
            @if ($page->sortable) data-attr-order={{ $row->orders }} @endif>

            @if(!$compact)
            <td>
    
                @if((in_array('update' , $actions) || in_array('delete' , $actions) || (isset($page->grid_operations) && is_array($page->grid_operations))) && $enable_operations )
               
                <div class="grid-operations-box">

                    <div class="ui icon button grid-operations" onclick="event.stopPropagation()">
                        <i class="ellipsis  vertical icon"></i>
                      </div>

                <div class="ui flowing popup hidden grid-operations-content" >
                  
                 

                    

                        @if(in_array('update' , $actions) && $enable_operations)

                        <a  
                           href="@if(isset($page->grid_operations['edit']['link'])) {{ str_replace( '{id}' , $row->id , $page->grid_operations['edit']['link']) }} @else {{ route('page.edit', ['page_slug' => $page->slug, 'id' => $row->id ]) }}  @endif"
                            class="item"
                            >
                            @if (isset($page->grid_operations['edit']['label']))
                                {{ $page->grid_operations['edit']['label'] }}
                            @else
                                Edit
                            @endif
                        </a>

                       
                       
                        @if(isset($page->sections) && count($page->sections) > 0)
                        <a  
                        
                       
                        href="@if (isset($page->grid_operations['show']['link'])) 
                        {{ str_replace( '{id}' , $row->id , $page->grid_operations['show']['link']) }} 
                        
                        @else {{ route('page.show', ['page_slug' => $page->slug, 'id' => $row->id]) }}  
                        
                        @endif
                        "
                        class="item"
                            >
                            @if (isset($page->grid_operations['show']['label']))
                                {{ $page->grid_operations['show']['label'] }}
                            @else
                                View
                            @endif
                        </a>
                        @endif
                        @endif

                        @if(in_array('delete' , $actions) && $enable_operations)
                        <a href="javascript:;" data-target="{{$page->slug}}" onclick="deleteRow($(this) ,  '{{ route('page.delete', ['page_slug' => $page->slug, 'id' => $row->id]) }}')"
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
