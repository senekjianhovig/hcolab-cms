<?php
    $fieldname = $element->name;
    $value = "";
    if($data && property_exists($data,$fieldname)){
        $value = date("Y-m-d",strtotime($data->$fieldname));
    }
    if($data && property_exists($data,$fieldname) && strtotime($data->$fieldname) == 0){
        $value = "";
    }
?>
<div class="{{$element->ui->container}} mb-3">
    <div class="field">
        <label>{{ $element->ui->label }} @if($element->ui->required) * @endif</label>
        <input type="date" name="{{ $fieldname }}" id="{{ $fieldname }}" value="{{ $value }}"
            @if($element->ui->required)
        required @endif >
    </div>
</div>
