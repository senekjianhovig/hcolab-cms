@php
$name = $element->name;
if(!property_exists($data,$name)){ $data->$name = ''; }
$element_type = $element->ui->type == 'readonly_textfield' || $element->ui->type == 'textfield' || $element->ui->type =='external textfield' ? 'text' : $element->ui->type;
@endphp

<div class="{{$element->ui->container}} mb-4">
    <div class="field">
        <label>{{$element->ui->label}} @if($element->ui->required) * @endif</label>
        <input readonly type="{{$element_type}}" name="{{$name}}" @if((int) $element->ui->required==1) required @endif
        placeholder="Enter {{strtolower($element->ui->label)}}"
        value="@if($data){{ $data->$name }}@endif">
    </div>
</div>