<?php
    $name = $element->name;
    $value = "";

    if($data && property_exists($data,$name)){
        $value = $data->$name;
    }

    if(strpos(" ".$value , '["') > 0 && strpos(" ".$value , '"]')){
        
        try{
            $value = json_decode($value);
        }catch(\Throwable $th){
        
        }
        
    }

    if(is_array($value)){
        $value = implode(", " , $value);
    }

    
$label_exists = !isset($element->ui->disable_label) || isset($element->ui->disable_label) && $element->ui->disable_label == false;
$margin_exists = !isset($element->ui->disable_margin) || isset($element->ui->disable_margin) && $element->ui->disable_margin == false;
?>
<div class="{{$element->ui->container}} @if($margin_exists) mb-4 @endif">

    <div class="field">
        @if($label_exists)
        <label for="{{$name}}">{{ $element->ui->label }} @if($element->ui->required) * @endif</label>
        @endif
        <textarea rows="3" name="{{ $name }}" id="{{$name}}" placeholder="Enter {{strtolower($element->ui->label)}}"
            @if($element->ui->required == 1) required @endif >{{$value}}</textarea>
    </div>

</div>
