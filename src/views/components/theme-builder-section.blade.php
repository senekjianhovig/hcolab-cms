@php
     $namespace = 'App\\Sections\\' . $section;
     $Section = new $namespace;
     $Section->setElements();
     $elements = $Section->elements;

   //   $data = new \StdClass;

   //   foreach (request()->all() as $key => $value) {
   //      $data->{$key} = is_array($value) ? json_encode($value) : $value;
   //   }
     
     if(isset($Section->foreign_keys)){
        $related_tables = \hcolab\cms\repositories\ForeignKey::getRelatedTables($Section->foreign_keys);
     }else{
        $related_tables = [];
     }

// dd($data);   



$edit_mode = request()->has('edit_mode') && request()->input('edit_mode') == true;

@endphp
<br> 
<form
enctype='multipart/form-data' class="ui form"
@if($edit_mode)
onsubmit="EditSection($(this) , '{{request()->key}}')"
@else
onsubmit="AddSection($(this))"
@endif
>
<input id="section-title" name="section_title" value="{{$Section->title}}" type="hidden">
<input id="section-name" name="section_name" value="{{$Section->section}}" type="hidden">

 <div class="row"> 
    @foreach ($elements as $element)
    {!! process_form_field($element , $data , $related_tables) !!}
    @endforeach
    <div class="col-lg-12 d-flex align-items-center  @if($edit_mode) justify-content-end @else justify-content-between @endif">
        @if($edit_mode)
        <button class="ui button green"> Edit </button>
        @else
        <button type="button" onclick="DisplaySelectSection()" class="ui button red"> Cancel </button>
        <button class="ui button green"> Create </button>
        @endif
    </div>
</div> 
</form>



