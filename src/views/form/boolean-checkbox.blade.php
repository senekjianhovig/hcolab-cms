@php
$name = $element->name;
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }


@endphp

<div class="{{$element->ui->container}} mb-3">
    <div class="field">
        <label>{{$element->ui->label}} @if($element->ui->required) * @endif</label>
        <div class="ui toggle checkbox">
            <input type="checkbox" name="{{$name}}" @if((int) $element->ui->required==1) required @endif tabindex="0"
            class="hidden" @if($data && property_exists($data,$name) && (int) $data->$name == 1) checked @endif
            value="1">
        </div>
    </div>
</div>
