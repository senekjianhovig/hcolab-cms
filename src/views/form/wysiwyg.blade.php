<?php
    $fieldname = $element->name;
?>
<div class="{{ $element->ui->container }} mb-3">
    <div class="field wysiwyg ">
        <label for="{{$fieldname}}">{{ $element->ui->label }} @if($element->ui->required) * @endif</label>
        <textarea class="summernote" name="{{$fieldname}}" id="{{$fieldname}}"
            @if($element->ui->required) required @endif>@if($data && property_exists($data,$fieldname)){{ $data->$fieldname }}@endif</textarea>
    </div>
</div>
