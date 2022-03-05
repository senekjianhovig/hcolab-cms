@php
$name = Str::slug($element->name,'_');
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }
@endphp


@if($data && !empty($data->$name))
<div class="{{$element->ui->container}} mb-4">
    <div class="field">
        <label>{{$element->ui->label}} </label>
        <input disabled type="text" name="{{$name}}" placeholder="ID" value="@if($data){{ $data->$name }}@endif">
    </div>
</div>
@endif