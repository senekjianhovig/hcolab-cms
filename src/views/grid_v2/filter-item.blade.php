@if($column)

@php
$name = $column->name;
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }
if($column->type == 'textfield'){ $column->type = 'text'; }

@endphp

@switch($column->type)
@case('boolean checkbox')
<div class="field my-2">
    <div class="ui toggle @if($data->$name == 1) checked @endif checkbox">
        <input type="checkbox" name="{{$name}}" @if($data->$name == 1) checked @endif id="{{$name}}" class="hidden" value="1" >
    </div>
</div>
@break




@case('values select')
@case('multiple select')
@case('select')

@php

if (isset($related_tables) && !is_null($related_tables) && !empty($related_tables) && isset($related_tables[$name]['data'])) {
    try {
        $options = $related_tables[$name]['data'];
    } catch (\Throwable $th) {
        $options = [];
    }
} else {
    try {

        $options =  $column->details->ui->options;

        $options = json_decode(json_encode($options));
    } catch (\Throwable $th) {
        $options = [];
    }
}

try {
    $selected = $column->type == 'select' || $column->type == 'values select' ? [$data->$name] : json_decode($data->$name, 1);
} catch (\Throwable $th) {
    $selected = [];
}

if(!is_array($selected)){
    $selected = [];
}

@endphp

<div class="field my-2">
        <select id="{{$name}}" name="{{$name}}" class="ui fluid dropdown">
            <option value=""> Select {{strtolower($column->label)}} </option>
           @foreach($options as $option)
            <option value="{{$option->id}}" @if ($selected && in_array($option->id, $selected)) selected @endif>{{$option->label}}</option>
            @endforeach
        </select>
</div>
@break
@default
<div class="field my-2">
   
    <input type="text" id="{{$name}}" name="{{$name}}" placeholder="Search by {{strtolower($column->label)}}"
        value="@if($data){{ $data->$name }}@endif">
</div>
@endswitch
@else
<div class="field my-2">
  
    <input id="id" type="text" name="id" value="@if($data){{$data->id}}@endif" placeholder="Search by id">
</div>
@endif
