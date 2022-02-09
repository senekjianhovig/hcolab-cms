<?php
    $name = $element->name;
?>
<div class="{{$element->ui->container}} mb-3">

    <div class="field">
        <label for="{{$name}}">{{ $element->ui->label }} @if($element->ui->required) * @endif</label>
        <textarea rows="3" name="{{ $name }}" id="{{$name}}"
            @if($element->ui->required == 1) required @endif placeholder="@if(!$data || !property_exists($data,$name)){{"Enter"}} {{strtolower($element->ui->label)}} @endif">@if($data && property_exists($data,$name)){{ $data->$name }}@endif</textarea>
    </div>

</div>
