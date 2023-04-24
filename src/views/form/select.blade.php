@php

$name = $element->name;
if (!property_exists($data, $name)) {
    $data->$name = '';
}




if (isset($related_tables) && !is_null($related_tables) && !empty($related_tables) && isset($related_tables[$name]['data'])) {
    try {
        $options = $related_tables[$name]['data'];
    } catch (\Throwable $th) {
        $options = [];
    }
} else {
    try {
        $options = json_decode(json_encode($element->ui->options));
    } catch (\Throwable $th) {
        $options = [];
    }
}


try {
    if($element->ui->type == 'select' || $element->ui->type == 'values select'){
      $selected = [$data->$name];
    }else{
        $selected = is_array($data->$name) ? $data->$name : json_decode($data->$name, 1);
    }
} catch (\Throwable $th) {
    $selected = [];
}


if(!is_array($selected)){
    $selected = [];
}

if ($element->ui->type == 'multiple select') {
    $fieldname = $name . '[]';
} else {
    $fieldname = $name;
}

$label_exists = !isset($element->ui->disable_label) || isset($element->ui->disable_label) && $element->ui->disable_label == false;
$margin_exists = !isset($element->ui->disable_margin) || isset($element->ui->disable_margin) && $element->ui->disable_margin == false;


@endphp

<div class="{{ $element->ui->container }} @if($margin_exists) mb-4 @endif">
    <div class="field">
        @if($label_exists)
        <label>{{ $element->ui->label }} @if ($element->ui->required)
                *
            @endif
        </label>
        @endif

        <select id="{{ $name }}" @if ($element->ui->type == 'multiple select') multiple @endif name="{{ $fieldname }}"
            class="ui fluid search dropdown" @if ($element->ui->required) required @endif>
            <option value="" disabled selected>Choose {{ $element->ui->label }}</option>
            @foreach ($options as $option)
                <option value="{{ $option->id }}" @if ($selected && in_array($option->id, $selected)) selected @endif>
                    {{ $option->label }}</option>
            @endforeach
        </select>
    </div>
</div>
