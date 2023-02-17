@php
$name = $element->name;
if(!property_exists($data,$name)){ $data->$name = ''; }
$element_type = $element->ui->type == 'textfield' || $element->ui->type =='external textfield' ? 'text' : $element->ui->type;

$label_exists = !isset($element->ui->disable_label) || isset($element->ui->disable_label) && $element->ui->disable_label == false;
$margin_exists = !isset($element->ui->disable_margin) || isset($element->ui->disable_margin) && $element->ui->disable_margin == false;

@endphp

<div class="{{$element->ui->container}} @if($margin_exists) mb-4 @endif">
    <div class="field">
      @if($label_exists)  <label>{{$element->ui->label}} @if($element->ui->required) * @endif</label> @endif
        <input type="{{$element_type}}" name="{{$name}}" @if((int) $element->ui->required==1) required @endif
        placeholder="Enter {{strtolower($element->ui->label)}}"
        value="@if($data){{ $data->$name }}@endif" @if(isset($element->ui->minimum) && $element->ui->minimum >= 0)
        min="{{ $element->ui->minimum }}" @endif @if(isset($element->ui->maximum) && $element->ui->maximum > 0)
        max="{{ $element->ui->maximum }}" @endif >
    </div>
</div>