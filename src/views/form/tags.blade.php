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
    $selected = $element->ui->type == 'select' || $element->ui->type == 'values select' ? [$data->$name] : json_decode($data->$name, 1);
} catch (\Throwable $th) {
    $selected = [];
}


try {
    //code...
} catch (\Throwable $th) {
    //throw $th;
}

$selected = isset($data->$name) ? json_decode_to_array($data->$name, 1) : [];


@endphp

<div class="{{ $element->ui->container }} mb-4">
    <div class="field">
        <label>{{ $element->ui->label }} @if ($element->ui->required)
                *
            @endif
        </label>

        <select id="{{ $name }}"  multiple  name="{{ $name }}[]"
            class="ui fluid search dropdown allow-additions"  @if ($element->ui->required) required @endif>
            <option value="" disabled selected>Enter {{ $element->ui->label }}</option>
            @foreach ($selected as $option)
                    <option selected value="{{$option}}"> {{$option}} </option>
            @endforeach
        </select>
    </div>
</div>
