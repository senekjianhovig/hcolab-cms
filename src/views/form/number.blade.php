@php
$name = Str::slug($element->db_name,'_');
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }
@endphp

<div class="{{$element->container_class}} mb-4">
    <div class="field">
        <label>{{$element->label}} @if((int) $element->required==1) * @endif</label>
        <input type="{{$element->type}}" name="{{$name}}" @if((int) $element->required==1) required @endif
        placeholder="Enter {{strtolower($element->label)}}"
        value="@if($data){{ $data->$name }}@endif">
    </div>
</div>