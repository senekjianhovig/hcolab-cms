@php
$name = $element->name;
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }
@endphp

<div class="{{ $element->ui->container }} mb-3">
    <div class="ui field custom-file">
        <label for="c-label"> {{$element->ui->label}}</label>
        <input type="file" class="filepond" name="upld_{{$name}}" @if($element->ui->type == 'multiple file')
        multiple
        data-allow-reorder="true"
        @endif

        data-max-file-size="3MB">
    </div>
</div>