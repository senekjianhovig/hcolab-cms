@php

if(isset($data)){
    $display_name = $data['display_name'];
    $icon = $data['icon'];
    $name = $data['name'];
    $value = $data['value'];
 }else{
    $display_name = '';
    $icon = '';
    $name = '';
    $value = '';
}

@endphp

<div class="dz-preview">

    @if(!empty($name))
        <input type="hidden" name="{{$name}}" value="{{$value}}" />
    @endif

    <div class="dz-preview-info">
    <div class="dz-image">
    
        @if(!empty($name))
        {!! $icon !!}
        @else
        <img data-dz-thumbnail />
        @endif
       
    </div>
    <div class="dz-details">    
        <div class="filename-wrapper">
            <div class="dz-filename"><span @empty($display_name) data-dz-name @endif> {{$display_name}} </span></div>
            <div class="dz-size" data-dz-size></div>
        </div>
        @if(empty($display_name))
        <div>
        
            <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
         
        </div> 
        @endif            
      </div>
    </div>
    <button type="button" @if(!empty($display_name)) onclick="$(this).parents('.dz-preview').remove()" @else   data-dz-remove @endif class="dz-remove"><i class="trash alternate icon"></i></button>
  </div>