@extends('CMSViews::layout.layout', ['title' => "SEO Modify"])
@section('head')
<title>SEO Modify</title>
@endsection


@php

$page = new \hcolab\cms\pages\CmsSEOPage;
$page->setElements(); 
$elements = $page->getElements();
$related_tables = $page->getRelatedTables();

// $data = new \stdClass();


@endphp

@section('content')

<div class="container-fluid my-3">

    <form action="{{route('seo-modify')}}"  method="POST" class="ui form">
        @csrf
   
        <div class="ui segment raised mb-3 py-3">
        
           

            <h3 class="mb-4">SEO Routes to be affected</h3>

            <div class="ui list">
                @foreach ($urls as $url)
                <div class="item">{{$url}}</div>    
                @endforeach
                
              </div>


        </div>


        <div class="ui segment raised mb-3 py-3">
        
           

            <h3 class="mb-4">SEO modify</h3>

            <input type="hidden" name="urls" value="{{json_encode($urls)}}" />


           
            <div class="row">

        


            @foreach ($elements as $element)
       
            {!! process_form_field($element , $data , $related_tables) !!}
            @endforeach

            
            </div>

        </div>
  
      
     

        <div class="ui divider"></div>
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{route('seo-configuration')}}" class="ui button red"> Cancel </a>
            <button class="ui button" type="submit">Submit</button>
        </div>
    
    
    </form>
    

</div>
@endsection

@section('scripts')
<script>


</script>
@endsection