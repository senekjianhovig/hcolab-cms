@php
$name = $element->name;
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }

$label_exists = !isset($element->ui->disable_label) || isset($element->ui->disable_label) && $element->ui->disable_label == false;
$margin_exists = !isset($element->ui->disable_margin) || isset($element->ui->disable_margin) && $element->ui->disable_margin == false;

@endphp

<div class="{{$element->ui->container}} @if($margin_exists) mb-4 @endif">
    <div class="field">
        @if($label_exists)
        <label>{{$element->ui->label}} @if($element->ui->required) * @endif</label>
        @endif
        <div class="ui slider checkbox">
            <input type="checkbox" name="{{$name}}" @if((int) $element->ui->required==1) required @endif tabindex="0"
            class="hidden" @if($data && property_exists($data,$name) && (int) $data->$name == 1) checked @endif
            value="1"
            @if(!is_null($element->ui->related_fields))
            data-related-fields = "{{$element->ui->related_fields}}"
            onchange="updateRelatedFields($(this))"
            @endif
            >
        </div>
    </div>
</div>

