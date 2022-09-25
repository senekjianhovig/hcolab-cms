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

?>
<div class="{{$element->ui->container}} mb-4">

    <div class="field">
        <label for="{{$name}}">{{ $element->ui->label }} @if($element->ui->required) * @endif</label>
        <textarea rows="3" name="{{ $name }}" id="{{$name}}"
            @if($element->ui->required == 1) required @endif placeholder="@if(!$data || !property_exists($data,$name)){{"Enter"}} {{strtolower($element->ui->label)}} @endif">{{$value}}</textarea>
    </div>

</div>
