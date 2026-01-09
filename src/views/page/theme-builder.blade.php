<?php


$page = new \hcolab\cms\pages\CmsThemeBuilderPage;
$page->setElements();
$elements = $page->getElements();
$related_tables = $page->getRelatedTables();

$page->getRow($id);

$data = $page->getRow($id);


$sections = \hcolab\cms\models\CmsThemeBuilderSection::where('theme_builder_id' , $id)->where(function($q){
    $q->whereNull('deleted_at');
    $q->orWhere('deleted' , 0);
  })->orderBy('orders' , 'ASC')->get();


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
                                     
                                        if(!in_array($data->cms_theme_builder_location , $section->locations) && !empty($section->locations)){
                                           
                                            continue;
                                        }

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
    
        <div class="col-lg-6">
            
            <div  class="ui segment raised ">
                <h3>Selected Sections</h3>
                <div id="sections" style="min-height:500px">
                    @foreach($sections as $section)
                        <div class="ui segment section-element" data-key="{{$section->id}}" data-section="{{$section->name}}" onclick="editElement($(this))">  
                            <div class="info">
                            {{$section->title }}
                            
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
            
            let key = elem.parents('.section-element').attr('data-key');

            $.ajax({
                url: `/cms/theme-builder/section/${section}/delete/${key}`,
                method : "POST",
                data : {
                    _token : $('meta[name=csrf-token]').attr("content")
                },   
                success: function(data){

                    elem.parents('.section-element').remove();
                    processElements();
                   
                },
                error : function(){
                   
                }
             });

          
           
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
        url: `/cms/theme-builder/section/${section}?edit_mode=true&key=${key}`,
        method : "GET" ,  
        success: function(data){
            $("#section-form").html(data);
            $("#form-container").removeClass('loading');
            DisplayFormSection();
            semanticInit();
            fileUploadInit();
        },
        error : function(){
            $("#form-container").removeClass('loading');
        }
    });

}


function processElements2(sorting){

   
    $.ajax({
                url: `/cms/theme-builder/{{$id}}/section/sorting`,
                method : "POST",
                data : {
                    _token : $('meta[name=csrf-token]').attr("content"),
                    sorting : sorting
                },   
                success: function(data){

                    
                   
                },
                error : function(){
                   
                }
             });


    // data-key

    //section/ordering

}

function sortableInit(){
    $( "#sections" ).sortable({
            placeholder: "ui-state-highlight",
            update: function( event, ui ) {

                var sorting = []; 
                $('#sections').children().each(function(){
                    sorting.push($(this).attr('data-key'));
                });
                processElements2(sorting);
            }
        });
        $( "#sections" ).disableSelection();
}

function AddSection(elem) {
    event.preventDefault();
    let query = elem.serializeArray();

    // var data = query.split("&");
    // var obj={};
    // for(var key in data){ obj[data[key].split("=")[0]] = data[key].split("=")[1]; }


    let title = elem.find('#section-title').val();
    let section = elem.find('#section-name').val();

    $.ajax({
        url: `/cms/theme-builder/section/temporary/create`,
        method : "POST" , 
        data : {
            payload : query,
            title : title,
            name : section,
            theme_builder_id : '{{$id}}',
            _token : $('meta[name=csrf-token]').attr("content")
        }, 
        success: function(data){
            
            // let key = Math.floor(Date.now() / 1000);
           

            let element = `<div class="ui segment section-element" data-key="${data}" data-section="${section}" onclick="editElement($(this))"> 
                <div class="info"> ${title}
                 </div>
                    <button class="remove-element" onclick="removeElement($(this))"> <i class="trash alternate outline icon"></i> </button>
                </div>`;

            $('#sections').append(element);

   
            processElements();
            DisplaySelectSection();
            elem.remove();
            $.toast({ class: 'success', message: `Successfully created!` });
            },
            error : function(){
                $.toast({ class: 'error', message: `Unable to create section` });
            }
    });
    


    
}

function EditSection(elem , key){

    event.preventDefault();
    let query = elem.serializeArray();

    // var data = query.split("&");
    // var obj={};
    // for(var key in data){ obj[data[key].split("=")[0]] = data[key].split("=")[1]; }

    $.ajax({
        url: `/cms/theme-builder/section/temporary/edit`,
        method : "POST" ,  
        data : {
            id : key,
           
            payload : query,
            _token : $('meta[name=csrf-token]').attr("content")
        },
        success: function(data){
            processElements();
            elem.remove();
            DisplaySelectSection();

            $.toast({ class: 'success', message: `Successfully updated!` });
        },
        error : function(){
                $.toast({ class: 'error', message: `Unable to create section` });
        }
    });

    // let element = $("[data-key="+key+"]");
    // element.find('input').val(JSON.stringify(query));

  
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
            fileUploadInit();
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
