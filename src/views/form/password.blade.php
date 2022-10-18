@php
$name = $element->name;
if(!property_exists($data,$name)){ $data->$name = ''; }
@endphp

<div class="{{$element->ui->container}} mb-4">
    <div class="field">
        <label>{{$element->ui->label}} @if($element->ui->required) * @endif</label>
        <input type="password" name="{{$name}}" @if((int) $element->ui->required==1) required @endif
        placeholder="Enter {{strtolower($element->ui->label)}}"
        value="">
    </div>
</div>
