@php

$columns = $page->getColumns();

if(!function_exists('checkDirection')){
    function checkDirection($fieldname){
        if(request()->input('sortColumn')==$fieldname && request()->input('sortOrder') == 'desc'){ return "down"; }
        if(request()->input('sortColumn')==$fieldname && request()->input('sortOrder') == 'asc'){ return "up"; }
    }
}
if(!function_exists('checkOrder')){
    function checkOrder($fieldname){
        if(request()->input('sortColumn')==$fieldname && request()->input('sortOrder') == 'desc'){ return "desc"; }
        if(request()->input('sortColumn')==$fieldname && request()->input('sortOrder') == 'asc'){ return "asc"; }
    }
}


$data = new \stdClass();
$data->id = request()->input('id');
$compact = request()->has("compact") && request()->has("compact") == 1;

@endphp
<thead>
    <tr>
        @if(!$compact)
        <th class="text-center" data-attr-column=""></th>
        @endif
        @foreach ($columns as $column)
        <th data-attr-column="{{ Str::slug($column->name,'_') }}"
            data-attr-order="{{checkOrder(Str::slug($column->name,'_'))}}">{{$column->label}}
            <i class="sort {{checkDirection(Str::slug($column->name,'_'))}} icon"></i>
        </th>
        @endforeach

    </tr>

    <tr class="ui form no-animation filter-row-initial" id="filter-row">
        <td class="text-center">
            <button class="blue ui button" type="button" onclick="filterTable()"> Filter</button>
            </th>
            @foreach($columns AS $column)
            @php $data->{$column->name} = request()->input($column->name); @endphp
        <td class="text-center"> @include('CMSViews::grid.filter-item', ['column'=> $column , 'data'=>$data]) </td>
        @endforeach
    </tr>

</thead>