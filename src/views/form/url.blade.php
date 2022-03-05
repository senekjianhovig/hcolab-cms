@php
$name = $element->name;
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }
@endphp


<div class="{{$element->ui->container}} mb-4">
    <label style="display: block;
    margin: 0 0 .28571429rem 0;
    color: rgba(0,0,0,.87);
    font-size: .92857143em;
    font-weight: 700;
    text-transform: none;">{{$element->ui->label}} @if($element->ui->required) * @endif</label>
    <div class="ui fluid action input">
        <input type="text" name="{{$name}}" @if($element->ui->required) required @endif
        value="@if($data){{ $data->$name }}@endif">
        <button type="button" class="ui right labeled icon button copy-btn">
            <i class="copy icon"></i>
            Copy
        </button>
    </div>
</div>
