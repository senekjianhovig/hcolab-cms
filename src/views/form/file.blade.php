@php
$name = $element->name;
if(!is_object($data)){ $data = new StdClass(); }
if(!property_exists($data,$name)){ $data->$name = ''; }

$is_multiple = $element->ui->type == 'multiple file';
$input_name = $is_multiple ? $element->name.'[]' : $element->name;
$previews = [];
if($data && !empty($data->$name)){
    if($is_multiple){
       $ids =  json_decode($data->$name);
    }else{
       $ids = explode(',', $data->$name); 
    }

    $previews = \hcolab\cms\models\File::whereIn('name', $ids)->where('deleted',0)->get();
}


@endphp

<div class="{{ $element->ui->container }} mb-4">
    <div class="c-label">
        {{$element->ui->label}} @if($element->ui->required) * @endif
    </div>
    <div class="file-upload-wrapper">
        <label class="file-upload-label">
            <input @if(!is_null($element->ui->accept)) accept="{{$element->ui->accept}}" @endif type='file' name="upld_{{$element->name}}" @if($is_multiple) multiple @endif style="display: none" />
            <div class="drag-text">
                <i class="fas fa-cloud-upload-alt icon"></i>
                <div class="label">@if($is_multiple) Choose multiple files @else Choose a file @endif</div>
            </div>
        </label>
        @foreach ($previews as $preview)
            @include('CMSViews::form.file-preview', [
                'value'=> $preview->name,
                'name' => $input_name, 
                'mime_category' => $preview->mime_category, 
                'url' => (bool) $preview->external ? $preview->url : env('DATA_URL').'/'.$preview->url, 
                'display_name' => $preview->original_name,
                'preview' => true
                ])
            @endforeach
    </div>
</div>