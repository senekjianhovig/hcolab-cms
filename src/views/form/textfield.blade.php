@php
$name = $element->name;
if(!property_exists($data,$name)){ $data->$name = ''; }
$element_type = $element->ui->type == 'textfield' ? 'text' : $element->ui->type;
@endphp

<div class="{{$element->ui->container}} mb-3">
    <div class="field">
        <label>{{$element->ui->label}} @if($element->ui->required) * @endif</label>
        <input type="{{$element_type}}" name="{{$name}}" @if((int) $element->ui->required==1) required @endif
        placeholder="Enter {{strtolower($element->ui->label)}}"
        value="@if($data){{ $data->$name }}@endif" @if(isset($element->ui->minimum) && $element->ui->minimum >= 0)
        min="{{ $element->ui->minimum }}" @endif @if(isset($element->ui->maximum) && $element->ui->maximum > 0)
        max="{{ $element->ui->maximum }}" @endif >
    </div>
</div>