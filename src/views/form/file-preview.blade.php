
@php
    $preview = isset($preview) && $preview; 

    

    switch($mime_category){
        case 'image':
        $icon = '<a href="'.$url.'" data-fancybox data-type="image">
        <img src="'.$url.'"  width="50" height="50" style="object-fit: contain ; background-color: #eee" />
        </a>';
        break;
        case 'application':
        $icon = '<div style="width:50px;height:50px;display:flex ; align-items : center ; justify-content:center"><i class="file alternate  icon" style="color:#5C258D;font-size:30px ;height:initial "></i></div>';
        break;
        case 'video':
        $icon = '<a href="'.$url.'" data-fancybox data-type="video" class="video-thumb-remove">
        <div style="width:50px;height:50px;display:flex ; align-items : center ; justify-content:center"><i class="file video    icon" style="color:#4389A2;font-size:30px ; height: initial"></i></div></a>';
        break;
        case 'audio' :
        $icon = '<div style="width:50px;height:50px;display:flex ; align-items : center ; justify-content:center"><i class="file audio    icon" style="color:#c7c5c3;font-size:30px ; height: initial"></i></div>';
        break;
        default:
        $icon = '<div style="width:50px;height:50px;display:flex ; align-items : center ; justify-content:center"><i class="file icon" style="color:#c7c5c3;font-size:30px ; height: initial"></i></div>';
            
      }


@endphp


@include('CMSViews::form.preview-template' , [ 'data' => [ 
    'icon' => $icon , 
    'name' => $name , 
    'value' => $value ,
    'display_name' => $display_name 
    ]   
])




{{-- <div class="file-upload-progress-tracker"> 
  <div class="file-progress-wrapper">

<div class="upload-progress-tracker">
    <input type="hidden" name="{{$name}}" value="{{$value}}" />
<div class="file-details"> 
  
    @switch($mime_category)
        @case('image')
        <a href="{{$url}}" data-fancybox data-type="image">
        <img src="{{$url}}"  width="50" height="50" style="object-fit: contain ; background-color: #eee" />
        </a>
        @break
        @case('application')
        <div style="width:50px;height:50px;display:flex ; align-items : center ; justify-content:center"><i class="file alternate  icon" style="color:#5C258D;font-size:30px ;height:initial "></i></div>
        @break
        @case('video')
        <div style="width:50px;height:50px;display:flex ; align-items : center ; justify-content:center"><i class="file video    icon" style="color:#4389A2;font-size:30px ; height: initial"></i></div>
        @break
        @case('audio')
        <div style="width:50px;height:50px;display:flex ; align-items : center ; justify-content:center"><i class="file audio    icon" style="color:#c7c5c3;font-size:30px ; height: initial"></i></div>
        @break
        @default
        <div style="width:50px;height:50px;display:flex ; align-items : center ; justify-content:center"><i class="file icon" style="color:#c7c5c3;font-size:30px ; height: initial"></i></div>
            
    @endswitch

    <div class="file-info"> 
      <span class="file-name"> {{$display_name}} </span>  
    
      <span class="file-status status completed"> completed </span>
     
    </div>
  </div>
  <div class="file-actions">
      <button type="button" class="pause-btn" onclick="fileElementDelete($(this))"> <i class="trash alternate icon"></i> </button>   
  </div>
</div>


  </div>
</div> --}}


