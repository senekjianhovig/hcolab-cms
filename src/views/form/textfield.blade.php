@php
$name = $element->name;
// Handle both object and array data
if(is_object($data)){
    if(!property_exists($data,$name)){ $data->$name = ''; }
} else if(is_array($data)){
    if(!isset($data[$name])){ $data[$name] = ''; }
} else {
    $data = new StdClass();
    $data->$name = '';
}
$element_type = $element->ui->type == 'textfield' || $element->ui->type =='external textfield' ? 'text' : $element->ui->type;

// Handle numeric field types
if($element->ui->type == 'big_integer' || $element->ui->type == 'decimal' || $element->ui->type == 'number'){
    $element_type = 'number';
}

$label_exists = !isset($element->ui->disable_label) || isset($element->ui->disable_label) && $element->ui->disable_label == false;
$margin_exists = !isset($element->ui->disable_margin) || isset($element->ui->disable_margin) && $element->ui->disable_margin == false;

// For decimal fields, add step attribute
$step = null;
if($element->ui->type == 'decimal' && isset($element->ui->scale)){
    $step = '0.' . str_repeat('0', $element->ui->scale - 1) . '1';
}

@endphp

<div class="{{$element->ui->container}} @if($margin_exists) mb-4 @endif">
    <div class="field">
      @if($label_exists)  <label>{{$element->ui->label}} @if($element->ui->required) * @endif</label> @endif
        <input type="{{$element_type}}" name="{{$name}}" @if((int) $element->ui->required==1) required @endif
        placeholder="Enter {{strtolower($element->ui->label)}}"
        value="@if($data){{ is_object($data) ? ($data->$name ?? '') : ($data[$name] ?? '') }}@endif" @if(isset($element->ui->minimum) && $element->ui->minimum >= 0)
        min="{{ $element->ui->minimum }}" @endif @if(isset($element->ui->maximum) && $element->ui->maximum > 0)
        max="{{ $element->ui->maximum }}" @endif @if($step) step="{{$step}}" @endif>
    </div>
</div>