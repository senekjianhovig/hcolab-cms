<?php
    $fieldname = $element->name;
    $value = "";
    if($data && property_exists($data,$fieldname)){
        $value = date("Y-m-d",strtotime($data->$fieldname));
    }
    if($data && property_exists($data,$fieldname) && strtotime($data->$fieldname) == 0){
        $value = "";
    }

    $label_exists = !isset($element->ui->disable_label) || isset($element->ui->disable_label) && $element->ui->disable_label == false;
    $margin_exists = !isset($element->ui->disable_margin) || isset($element->ui->disable_margin) && $element->ui->disable_margin == false;
?>
<div class="{{$element->ui->container}} @if($margin_exists) mb-4 @endif">
    <div class="field">
        @if($label_exists) <label>{{ $element->ui->label }} @if($element->ui->required) * @endif</label> @endif
        <input type="date" name="{{ $fieldname }}" id="{{ $fieldname }}" value="{{ $value }}"
            @if($element->ui->required)
        required @endif >
    </div>
</div>
