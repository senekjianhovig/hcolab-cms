@extends('CMSViews::layout.layout', ['title' => "Settings"])
@section('head')
<title>Super Import</title>
@endsection


@section('content')

<div class="container-fluid my-3">

    <form  method="POST" class="ui form" enctype="multipart/form-data">
        @csrf
   

     


        


        <div class="ui segment raised mb-3 py-3">
        
        
            <h3 class="mb-4">Import data to the following table: {{$page->title}}</h3>


            {{-- <a class="ui button green mr-2 mb-4" type="button" href="">
                Export Sample</a>
            --}}

            <div class="field">
                <label>Select Primary Field</label>
                <select name="primary_field" class="ui fluid dropdown">
                    <option value="" selected disabled>Select Primary Field</option>
                    @foreach($page->columns as $column)
                    <option value="{{$column->name}}">{{$column->label}}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Upload CSV file</label>
                <input type="file" name="upload_file" placeholder="">
            </div>

    
        </div>
  
       
    
        <div class="ui divider"></div>
        <div class="d-flex justify-content-end align-items-center">
            <a href="/" class="ui button red"> Cancel </a>
            <button class="ui button" type="submit">Submit</button>
        </div>
    
    
    </form>
    

</div>
@endsection

@section('scripts')
<script>


</script>
@endsection