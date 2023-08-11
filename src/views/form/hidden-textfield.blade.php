@php
$name = Str::slug($element->name,'_');
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }
@endphp

<input  type="hidden" name="{{$name}}"  value="@if($data){{ $data->$name }}@endif">
