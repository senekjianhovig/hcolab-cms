@extends('CMSViews::layout.layout', ['title' => "Role Permissions"])
@section('head')
<title>Role Permissions</title>
@endsection


@php
$actions = ['create', 'read' , 'update' , 'delete' , 'export' , 'import'];

@endphp

@section('content')

<div class="container-fluid my-3">

    <div class="ui segment raised mb-3">

        <form  method="POST" class="ui form">
            @csrf

            <div class="field">
                <label>Role Label</label>
                <input type="text" readonly value="{{$role->label}}" placeholder="Role Label">
            </div>


            <table class="ui compact table">
                <thead>
                  <tr>
                    <th style="width: 300px">Toggle All</th>
                    @foreach($actions as $action)
                    <th>
                        <div class="ui slider checkbox" onchange="toggleAll($(this) ,'{{$action}}')">
                            <input type="checkbox" class="hidden ">
                          
                        </div>
                    </th>
                    @endforeach
                   
                  </tr>
                </thead>
            </table>

            <table class="ui compact table">
                <thead>
                  <tr>
                    <th style="width: 300px">Page</th>

                    @foreach($actions as $action)
                    <th>{{ucfirst($action)}}</th>
                    @endforeach
                    
                  </tr>
                </thead>
                <tbody>

                    @foreach($menu as $index => $menu_item)
                        <tr>
                            <td> {{ $menu_item['label'] }}</td>

                            @foreach($actions as $action)
                           
                            @php
                                $checked = isset($role_permissions[$menu_item['name']]) && in_array($action , $role_permissions[$menu_item['name']]['actions']);
                            @endphp

                            <td> 
                                <div class="ui slider checkbox checkbox-{{$action}} @if($checked) checked @endif ">
                                 <input type="checkbox" name="{{$action}}_{{$menu_item['name']}}" class="hidden "  @if($checked) checked @endif  >
                               </div>
                            </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
              </table>


       

        <div class="ui divider"></div>
        <div class="d-flex justify-content-end align-items-center">
            {{-- <a href=""" class="ui button red"> Cancel </a> --}}
            <button class="ui button" type="submit">Submit</button>
        </div>
    
    
    </form>
    </div>
  

</div>
@endsection

@section('scripts')
<script>

function toggleAll(el , action){
    event.preventDefault();
    var result = el.checkbox('is checked');
    var todo = result == true ? 'check' : 'uncheck';

    $('.checkbox-'+action).each(function(index , elem){
         $(elem).checkbox(todo);
    });

}

</script>
@endsection