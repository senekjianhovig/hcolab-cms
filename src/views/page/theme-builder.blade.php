<?php


$page = new \App\Pages\ThemeBuilderPage;
$page->setElements();
$elements = $page->getElements();
$related_tables = $page->getRelatedTables();

$page->getRow($id);

$data = $page->getRow($id);


$prev_url = "";
$data = isset($data) ? $data : new \stdClass();

$opened = false;
if($elements[0]->ui->type == 'open div'){
    $opened = true;
}

if(request()->has("redirect_url")){
    $prev_url = request()->input('redirect_url');
}else{
    if(app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName() == "page"){ $prev_url = $_SERVER['HTTP_REFERER']; }
}

$files = new \Illuminate\Filesystem\Filesystem();
$path = app_path() . '/Sections';

?>

@extends('CMSViews::layout.layout', ['title' => $page->title])

@section('head')

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

<style>
    .ui-state-highlight{
        height: 49.98px !important;
        background-color: rgb(221, 221, 221);
        border : 1px solid rgb(221, 221, 221);
    }
    .section-element {
        display: flex;
    }
    .section-element .info {
        flex : 1
    }
    .remove-element{
        cursor: pointer;
        background: transparent;
        border: 0;
        outline: none;
        width: 40px;
        height: 100%;
    }

   .d-none{
       display: none;
   }

</style>

@endsection

@section('content')

<div class="container-fluid my-3">
    <div class="ui segment raised  mb-3">
        <div class="ui tiny breadcrumb">
            <a href="{{route('page', ['page_slug'=>$page->slug])}}" class="section">{{$page->title}}</a>
            <i class="right arrow icon divider"></i>
            <div class="section active "> @isset($id) Edit Page {{$page->title}} @else Create Page @endisset </div>
        </div>
    </div>

  

    <div class="row">
          
        <div class="col-lg-6">
            <div id="form-container" class="ui segment raised ">

                <h3>Section Settings</h3>

             <div class="section-settings" style="min-height:500px" >
                <div id="add-new-section" class="ui form">
                    <br> 
                    Please select a component which you want to add it to your theme.
                    <br>
                    {{-- <br> --}}
                    After you select a component, you need to fill all required properties. 
                    <br>
                    <br>
                    <div class="field">
                        <label>Add a Component</label>
                        
                        <div class="field ">
                            <select id="section" class="ui sections-dropdown search dropdown" onchange="CreateSection($(this))">
                                <option selected  value="">Select Component</option>
                                @foreach ($files->files($path) as $file)
                                    @php
                                        $entity = str_replace('.php', '', $file->getFileName());
                                        $namespace = 'App\\Sections\\' . $entity;
                                        $section = new $namespace;
                                       
                                    @endphp
                                    <option value="{{ $section->section }}"> {{ $section->title }} </option>
                                @endforeach
                            </select>
                        </div>
                </div>
               
               
                </div>
                <div id="section-form" class="d-none" >
                </div>
             </div>

               
                
            </div>
        </div>
        @php
            try {
                $payload = json_decode($data->payload);
            } catch (\Throwable $th) {
                $payload = [];
            }
            if(is_null($payload)){
                $payload = [];
            }
    
        @endphp
        <div class="col-lg-6">
            
            <div  class="ui segment raised ">
                <h3>Selected Sections</h3>
                <div id="sections" style="min-height:500px">
                    @foreach($payload as $key => $val)
                        @php
                            $val_arr = query_string_to_array($val);
                        @endphp
                        <div class="ui segment section-element" data-key="{{$key}}" data-section="{{$val_arr['section_name']}}" onclick="editElement($(this))">  
                            <div class="info">
                            {{$val_arr['section_title'] }}
                        <input type="hidden" name="{{$key}}" value="{{$val}}" id="{{$key}}" /> 
                            </div>
                            <button class="remove-element" onclick="removeElement($(this))"> <i class="trash alternate outline icon"></i> </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
              
    </div>

    <div id="page-fields-segment" 
    class="@if(!$opened) ui segment raised mb-3 @endif"
    >
    @if(!$opened)
        <h3> {{$page->title}} Settings</h3>
        @endif
        <form id="page" method="POST" action="{{route('page.save' , ['page_slug' => $page->slug ])}}"
            enctype='multipart/form-data' class="ui form py-3">
            @csrf
            <input type="hidden" name="redirect" value="{{$prev_url}}" />
            @isset($id) 
            <input type="hidden" name="id" value="{{$id}}" />
            @endisset

            <div class='col-lg-12'>
            <div data-field="payload">
                @foreach($payload as $key => $val)
                    <input type="hidden" name="payload[{{$key}}]" value="{{$val}}" /> 
                @endforeach
            </div>
        </div>

            @if(!$opened) <div class="row"> @endif

            
                @foreach ($elements as $element)
                {!! process_form_field($element , $data , $related_tables) !!}
                @endforeach
                @if(!$opened)  </div> @endif
      
           
               

        
          
            <div class="ui divider"></div>
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{route('page', ['page_slug'=>$page->slug])}}" class="ui button red"> Cancel </a>
                <button class="ui button" type="submit">Submit</button>
            </div>



          
        </form>
    </div>
</div>




@endsection

@section('scripts') 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>

function CreateConfirmModel(id, title , message){
    $("body").append(`
    <div id="${id}" class="ui basic modal">
        <div class="ui icon header">
            <i class="archive icon"></i>
                ${title}
            </div>
            <div class="content">
      <p style="text-align : center">${message}</p>
    </div>
    <div class="actions" style="display: flex ; justify-content: center">
      <div class="ui red basic cancel inverted button">
        <i class="remove icon"></i>
        No
      </div>
      <div class="ui green ok inverted button">
        <i class="checkmark icon"></i>
        Yes
      </div>
    </div>
    </div>
    `);
}

function removeElement(elem){

    // event.stopPropagation();

    CreateConfirmModel("delete-element" , "Deleting a section" , "Are you sure you want to delete the section");


    $('.ui.modal').modal({
        closable  : false,
        onDeny    : function(){
            // $("#delete-element").remove();
        
        },
        onApprove : function() {
            elem.parents('.section-element').remove();
            processElements();
           
        }
    }).modal('show');

    
}

function editElement(elem) {


    let section = elem.attr('data-section');
    let key = elem.attr('data-key');
    let query = elem.find('input').val();

    $("#form-container").addClass('loading');
    // let section = $('#section').val();

    $.ajax({
        url: `/cms/theme-builder/section/${section}?${query}&edit_mode=true&key=${key}`,
        method : "GET" ,  
        success: function(data){
            $("#section-form").html(data);
            $("#form-container").removeClass('loading');
            DisplayFormSection();
            semanticInit();
        },
        error : function(){
            $("#form-container").removeClass('loading');
        }
    });

}

function sortableInit(){
    $( "#sections" ).sortable({
            placeholder: "ui-state-highlight",
            update: function( event, ui ) {
                processElements();
            }
        });
        $( "#sections" ).disableSelection();
}

function AddSection(elem) {
    event.preventDefault();
   let query = elem.serialize();
   
   let key = Math.floor(Date.now() / 1000);
   let title = elem.find('#section-title').val();
   let section = elem.find('#section-name').val();



   let element = `<div class="ui segment section-element" data-key="${key}" data-section="${section}" onclick="editElement($(this))"> 
    <div class="info"> ${title}
    <input type="hidden" name="${key}" value="${query}" id="${key}" /> 
    </div>
    <button class="remove-element" onclick="removeElement($(this))"> <i class="trash alternate outline icon"></i> </button>
                        
    </div>`;

    $('#sections').append(element);

   
    processElements();
    DisplaySelectSection();
    elem.remove();
}

function EditSection(elem , key){

    event.preventDefault();
    let query = elem.serialize();

    let element = $("[data-key="+key+"]");
    element.find('input').val(query);

    processElements();
    elem.remove();

    DisplaySelectSection();
}

function processElements(){
    var target_result = $('[data-field=payload]');

    target_result.html('');
    
    $('#sections').find('.section-element').each(function(index , elem){
        
        let key = $(elem).attr('data-key');
        let inputVal = $(elem).find('input[type=hidden]').val();
        target_result.append(`<input  name="payload[${key}]" type="hidden" value="${inputVal}" /> `);
    });
   
    
}

function CreateSection(elem) {
    // elem.addClass('loading');
    $("#form-container").addClass('loading');

    let section = $('#section').val();

    if(section == ""){
        $("#form-container").removeClass('loading');
        return;
    }

    $.ajax({
        url: `/cms/theme-builder/section/${section}`,
        method : "GET" ,  
        success: function(data){
            $("#section-form").html(data);
            // elem.removeClass('loading');
            $("#form-container").removeClass('loading');
            DisplayFormSection();
            semanticInit();
        },
        error : function(){
            // elem.removeClass('loading');
            $("#form-container").removeClass('loading');
        }
    });

  

}


function DisplaySelectSection(){
    $('#section-form').addClass('d-none');
    $('#add-new-section').removeClass('d-none');
    $('.sections-dropdown').dropdown('clear');
}

function DisplayFormSection(){
    $('#section-form').removeClass('d-none');
    $('#add-new-section').addClass('d-none');
}


sortableInit();
   

</script>

@endsection
