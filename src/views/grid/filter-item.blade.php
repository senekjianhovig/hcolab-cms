@if($column)

@php
$name = $column->name;
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }
if($column->type == 'textfield'){ $column->type = 'text'; }
@endphp

@switch($column->type)
@case('boolean checkbox')
<div class="field">
    <div class="ui toggle checkbox">
        <input type="checkbox" name="{{$name}}" id="{{$name}}" class="hidden" value="1">
    </div>
</div>
@break
@case('multiple select')
@case('select')
<div class="field mb-0">
    <input type="text" id="{{$name}}" name="{{$name}}" placeholder="Search by {{strtolower($column->label)}}"
        value="@if($data){{ $data->$name }}@endif">
</div>
@break
@default
<div class="field mb-0">
    <input type="text" id="{{$name}}" name="{{$name}}" placeholder="Search by {{strtolower($column->label)}}"
        value="@if($data){{ $data->$name }}@endif">
</div>
@endswitch
@else
<div class="field mb-0">
    <input id="id" type="text" name="id" value="@if($data){{$data->id}}@endif" placeholder="Search by id">
</div>
@endif
