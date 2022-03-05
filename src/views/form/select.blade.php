@php

$name = $element->name;
if(!property_exists($data,$name)){ $data->$name = ''; }


if(isset($related_tables) && !is_null($related_tables) && !empty($related_tables)){


try {
$options = $related_tables[$name]["data"];
} catch (\Throwable $th) {
$options = [];
}
}else{
   
    try {
        $options = json_decode(json_encode($element->ui->options));
    } catch (\Throwable $th) {
        $options = [];
    }

    
}


try {
$selected = $element->ui->type == "select" || $element->ui->type == "values select" ? [ $data->$name ] : json_decode($data->$name ,1);
} catch (\Throwable $th) {
$selected = [];
}


@endphp

<div class="{{$element->ui->container}} mb-4">
    <div class="field">
        <label>{{$element->ui->label}} @if($element->ui->required) * @endif</label>

        <select id="{{$name}}" @if($element->ui->type == "multiple select") multiple @endif
            name="{{$name}}@if($element->ui->type == "multiple select")[]@endif" class="ui fluid search dropdown"
            @if($element->ui->required) required @endif >
            <option value="" disabled selected>Choose {{ $element->ui->label }}</option>
            @foreach($options AS $option)
            <option value="{{$option->id}}" @if($selected && in_array($option->id,$selected)) selected @endif>
                {{ $option->label }}</option>
            @endforeach
        </select>
    </div>
</div>