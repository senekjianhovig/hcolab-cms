@extends('CMSViews::layout.layout', ['title' => "Settings"])
@section('head')
<title>Settings</title>
@endsection


@php
$settings = \hcolab\cms\models\Setting::where(function($q){
            $q->whereNull('deleted_at');
            $q->orWhere('deleted' , 0);
          })->get();

@endphp


@section('content')

<div class="container-fluid my-3">

    <form  method="POST" class="ui form">
        @csrf
   

     


        @foreach($settings->groupBy('group_label') as $group => $setting)
        <div class="ui segment raised mb-3 py-3">
        
           

            <h3 class="mb-4">{{$group}}</h3>

            @foreach($setting as $item)
        
                    <div class="field">
                        <label> {{$item->label}} </label>
                        <input type="text" name="{{$item->key}}" value="{{$item->value}}" placeholder="{{$item->label}}"/> 
                    </div>
                @endforeach

            
           

        </div>
  
        @endforeach
     

        <div class="ui divider"></div>
        <div class="d-flex justify-content-end align-items-center">
            {{-- <a href="/" class="ui button red"> Cancel </a> --}}
            <button class="ui button" type="submit">Submit</button>
        </div>
    
    
    </form>
    

</div>
@endsection

@section('scripts')
<script>


</script>
@endsection