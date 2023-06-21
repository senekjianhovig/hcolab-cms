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

    <div class="dropzone-upload-wrapper " name="upld_{{$element->name}}"  @if($is_multiple) is_multiple ="true" @else is_multiple= "false" @endif>
        {{-- <div class="drag-text">
            <i class="fas fa-cloud-upload-alt icon"></i>
            <div class="label">@if($is_multiple) Choose multiple files @else Choose a file @endif</div>
        </div> --}}

        <div class="upload-area" onclick="$(this).parent().click()">
            @if($is_multiple) Choose multiple files @else Choose a file @endif
        </div>


        <div class="">
        <div class="dropzone-preview-wrapper dropzone-preview-wrapper-upld_{{$element->name}}" >
           
        </div>
        <div class="preview-area">
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
    </div>

    {{-- <div class="file-upload-wrapper">
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
    </div> --}}
</div>


<style>

.dropzone-upload-wrapper{
    
    background: transparent;
    border: 1px solid rgba(34, 36, 38, 0.15);
    color: rgba(0, 0, 0, 0.87);
    border-radius: 0.28571429rem;
    box-shadow: 0 0 0 0 transparent inset;
    transition: color 0.1s ease, border-color 0.1s ease;
    position: relative;
    cursor: pointer;
}


.dropzone-upload-wrapper .upload-area{
   min-height: 100px;
    text-align: center;
    align-items: center;
    justify-content: center;
    display: flex;
    color: #c1c3c5;
    position: relative;
    z-index: 0;
    cursor: pointer;
}

.dropzone-preview-wrapper{
    
}

.dz-preview{
    padding: 10px;
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid #eee;
    background-color: #f8f7f7;
    border-radius: 0.28571429rem;
    gap:10px;
    box-shadow: 0 0 0 0 transparent inset;

}

.video-thumb-remove::after{
    display: none !important;
}

.dz-details{
    /* width: 200px; */
}

.dz-image{
    /* background-color: #eee; */
}
.dz-image img{
    width: 60px;
    object-fit: contain;
}

.dz-preview-info{
    display: flex;
    flex: 1 !important;
    gap:10px;
}

/* .dz-progress {
      width: 100%;
      height: 10px;
      background-color: #f2f2f2;
      margin-top: 5px;
    } */

/* .dz-progress .dz-upload {
      height: 100%;
      background-color: #4CAF50 !important;
    } */

    .dz-progress {
      position: relative;
      width: 100%;
      height: 3px;
     
      background-color: #f2f2f2;
      
    }

    .dz-progress .dz-upload {
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background-color: blue;
      transition: all 0.35s ease-in-out;
    }


    .dz-details{
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap:12px;
        flex:1
    }


    .dz-success-mark { color: #4CAF50 }


    .dz-success-mark{ display: none; font-size: 12px; }

    .dz-complete .dz-success-mark{ display: block !important }


    .dz-complete .dz-upload{
        background: #4CAF50 !important;
    }

    .filename-wrapper{
        font-size: 14px;
        
        display: flex;
        gap:10px;
    }

    .dz-filename{
        font-weight: bold
    }

    a.dz-remove{
        display: none;
    }
    .dz-remove{
        background: transparent;
    border: none;
    outline: 0;
    cursor: pointer;
    color: indianred;
    }
</style>

