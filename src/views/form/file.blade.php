@php
$name = $element->name;
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }
@endphp

{{-- <div class="{{ $element->ui->container }} mb-3 hco-file-upload">
    <label class="image-upload-label"> {{$element->ui->label}}</label>
    <div class="image-upload-wrap">
        <form class="frm">
            <input class="file-upload-input file-input" name="upld_{{$element->name}}" multiple type='file' />
            <div class="drag-text">
                <i class="fas fa-cloud-upload-alt icon"></i>
                <div class="label">Drag and drop a file</div>
            </div>
        </form>
    </div>
    <div class="progress-area uploaded-wrap"></div>
    <div class="uploaded-area uploaded-wrap"></div>


</div> --}}

<div class="{{ $element->ui->container }} mb-3 hco-file-upload">
    {{-- <label class="image-upload-label"> {{$element->ui->label}}</label> --}}
    <label class="file-upload-label">
        <input type='file' {{-- class="file-upload-input file-input" --}} name="upld_{{$element->name}}" multiple
            style="display: none" />
        <div class="drag-text">
            <i class="fas fa-cloud-upload-alt icon"></i>
            <div class="label">Drag and drop a file</div>
        </div>
    </label>


</div>