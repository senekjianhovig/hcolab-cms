@extends('CMSViews::layout.layout', ['title' => "SEO Configuration"])
@section('head')
<title>SEO Configuration</title>
@endsection


@section('content')
<form method="POST">
    @csrf
<div class="container-fluid my-3">


    <a href="{{route("sitemap-generate")}}" class="ui button green "> Update Sitemap </a>

    <table class="ui celled striped table">

        <thead>
            <tr>
                <th>
                    
                    <div class="ui checkbox" style="width : 100%">
                        <input type="checkbox"  onchange="toggleAll()">
                        <label>  SEO Routes </label>
                    </div>
                </th>

                <th>
                    Title
                </th>

                <th>
                    Desription
                </th>

                <th>
                    Keywords
                </th>

            <th class="right aligned collapsing">
                <button class="ui mini green button modify-selected" disabled="true" type="submit"> Modify Selected </button>
            </th>
          </tr></thead>
          <tbody>

        @foreach($urls as $url)
       
        <tr>
            <td class="collapsing">
                <div class="ui checkbox checkbox-url" style="width : 100%">
                    <input onchange="checkboxChanged()" type="checkbox" name="url[]" value="{{$url['url']}}">
                    <label>  {{$url['url']}} </label>
                </div>
              </td>

              <td class="collapsing">
                {{$url['title']}}
              </td>
              <td class="collapsing">
                {{$url['description']}}
            </td>

            <td class="collapsing">
                {{$url['keywords']}}
            </td>


            {{-- <td class="collapsing">
              <i class="linkify icon"></i> {{$url['url']}}
            </td> --}}
            <td class="right aligned collapsing">

                {{-- @if(!$url['exist'])
                <div class="ui red horizontal label">Need Setup</div>
                @endif

                @if($url['exist'] && $url['empty'])
                <div class="ui black horizontal label">Incompleted</div>
                @endif

                @if($url['exist'] && !$url['empty'])
                <div class="ui green horizontal label">Completed</div>
                @endif --}}

                <button type="button" class="ui button mini blue" onclick="modifySingle($(this))"> Modify </button>

            </td>
            {{-- <td class="right aligned collapsing">Edit</td> --}}
          </tr>
        @endforeach
         
    </tbody>
</table>
</div>
</form>
@endsection

@section('scripts')
<script>

function modifySingle(elem){
   elem.parents('tr').find('.checkbox-url').checkbox('check');

   $('form').submit();
}

function toggleAll(){
    $('.checkbox-url').checkbox('toggle');
}


function checkboxChanged(){
    checkSelected();
}

function checkSelected(){


    var count = $('.checkbox-url.checked').length;

    console.log(count);

    if(count > 0){
        $('.modify-selected').attr('disabled' , false);
    }else{
        $('.modify-selected').attr('disabled' , true);
    }

}


</script>
@endsection


    
   
  