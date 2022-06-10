const TABLE_DATA_URL = "/cms/page/{page_slug}/query";


function semanticInit(){
    $('.ui.dropdown:not(.allow-additions)').dropdown({
        clearable : true
    });

    $('.ui.dropdown.allow-additions').dropdown({
        clearable : true,
        allowAdditions : true
    });
    

    $('.ui.checkbox').checkbox();
}

function sidebarInit(){
    $('div.nav.main ul ul').addClass('initialized');
    $('div.nav.main a.has-submenu').parent().find('ul').slideUp(0);
    $('div.nav.main a.has-submenu').click(function(){
        $(this).toggleClass('expanded');
        $(this).parent().toggleClass('active-submenu');
        $(this).parent().find('ul').slideToggle(300);
    });
    $("#nav-search input").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $('.nav.main ul li').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
}

function getParams() {
    var url = window.location.href;
    if (url.indexOf("?") == -1) {
        return {};
    }
    var params = {};
    var parser = document.createElement("a");
    parser.href = url;
    var query = parser.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        params[pair[0]] = decodeURIComponent(pair[1]);
    }
    return params;
}

function createQueryString(result , arr , isStart){

    var oldParams = getParams();
    var params = {};
    Object.keys(oldParams).forEach(function(key, index){
     if(arr.includes(key)){
        params[key] = oldParams[key];
     }
    });

    if(result.length > 0){
        result.forEach(function(item, index){
            if(item.value != ""){
                params[item.name] = item.value;
            }
        });
        var str = "";
        var i=0;
        Object.keys(params).forEach((key, index) => {
            str += index == 0 && isStart ? "?" + key + "=" + params[key] : "&" + key + "=" + params[key];
        });
        return str;
    }
}

function makeQueryParams(result , arr){
    var str = createQueryString(result,arr, true);
    var documentTitle = document.title;
    var urlSplit = window.location.href.split("?");
    var final_url = urlSplit[0] + str;
    var obj = { Title: documentTitle, Url: final_url };
    history.pushState(obj, obj.Title, obj.Url);
}

function removeQuerySpecificString(label) {
    var documentTitle = document.title;
    var url = window.location.href;
    var str = "";
    var objectParams = getParams();
    var i=0;
    Object.keys(objectParams).forEach((key, index) => {
        if (key != label) {
            str +=
                i == 0
                    ? "?" + key + "=" + objectParams[key]
                    : "&" + key + "=" + objectParams[key];
                i++;
        }

    });
    var urlSplit = window.location.href.split("?");
    var final_url = urlSplit[0] + str;
    var obj = { Title: documentTitle, Url: final_url };
    history.pushState(obj, obj.Title, obj.Url);
}
function addQueryParamsWithoutReloading(key, value) {
    var documentTitle = document.title;
    var uri = window.location.href;
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf("?") !== -1 ? "&" : "?";

    if (uri.match(re)) {
        var obj = {
            Title: documentTitle,
            Url: uri.replace(re, "$1" + key + "=" + value + "$2")
        };
    } else {
        var obj = {
            Title: documentTitle,
            Url: uri + separator + key + "=" + value
        };
    }
    history.pushState(obj, obj.Title, obj.Url);
}

function sortByColumn(){

    $( "th" ).on( "click", function() {
        var elem = $(this);
        elem.find('.icon').removeClass('up').removeClass('down')

        var sortColumn =   elem.attr('data-attr-column');
        var sortOrder =   elem.attr('data-attr-order');

        switch (sortOrder) {
            case '': sortOrder = 'desc';
            break;
            case 'asc': sortOrder = 'desc';  elem.find('.icon').addClass('down');
            break;
            case 'desc': sortOrder = 'asc'; elem.find('.icon').addClass('up');
            break;
            default: sortOrder = 'desc';
            break;
        }

        var result = [{"name": "sortColumn" , "value": sortColumn }, {"name": "sortOrder" , "value": sortOrder },{"name": "page" , "value": 1 }];
        var str = createQueryString(result,['sortColumn', 'sortOrder' , 'page', 'enableFilter'], true);
        $('.loading-screen').addClass('loading');
        var url = TABLE_DATA_URL.replace("{page_slug}", $('meta[name=entity]').attr('content'))+str;
        $.post( url, {'_token' : $('meta[name=csrf-token]').attr('content')},
            function( data ){
                $('tbody').replaceWith(data.table_body);
                $('.pagination-area').html(data.pagination);
                semanticInit();
                if(sortColumn!=''){
                    makeQueryParams(result , ['sortColumn', 'sortOrder' , 'page' ,'enableFilter']);
                }
                removeQuerySpecificString('page');
                elem.attr('data-attr-order', sortOrder);
                $('.loading-screen').removeClass('loading');
            }
        );
    });
}

function changePage(page){
    $('.loading-screen').addClass('loading');
    var result = [{"name": "page" , "value": page }];
    var str = createQueryString(result,['sortColumn', 'sortOrder' , 'page', 'enableFilter'], true);
    var url = TABLE_DATA_URL.replace("{page_slug}", $('meta[name=entity]').attr('content'))+str;
    $.post( url, {'_token' : $('meta[name=csrf-token]').attr('content')}, function( data ){
        $('tbody').replaceWith(data.table_body);
        $('.pagination-area').html(data.pagination);
        semanticInit();
        makeQueryParams(result , ['sortColumn', 'sortOrder' , 'enableFilter']);
        $('.loading-screen').removeClass('loading');
    });
}

function filterTable(){
    $('.loading-screen').addClass('loading');
    var result = $('#filters-form').serializeArray();
    var str = createQueryString(result,['sortColumn', 'sortOrder' , 'enableFilter'], true);
    var url = TABLE_DATA_URL.replace("{page_slug}", $('meta[name=entity]').attr('content'))+str;

    $.post(url, {'_token' : $('meta[name=csrf-token]').attr('content')} , function( data ){
            $('tbody').replaceWith(data.table_body);
            $('.pagination-area').html(data.pagination);
            semanticInit();
            makeQueryParams(result , ['sortColumn', 'sortOrder' , 'enableFilter']);
            $('.loading-screen').removeClass('loading');
    });
}

function toggleFilterBox(elem){

    $('.loading-screen').addClass('loading');
    $('#filter-row').slideToggle();
    elem.toggleClass('filter-active');

    setTimeout(function(){
        $('.loading-screen').removeClass('loading');
    },400);

    switch (elem.attr('data-enable')) {
        case "1":
            removeQuerySpecificString('enableFilter');
            elem.attr('data-enable', "0");
            break;

        case "0":
            addQueryParamsWithoutReloading('enableFilter' , "1");
            elem.attr('data-enable', "1");
        break;
        default:
            addQueryParamsWithoutReloading('enableFilter' , "1");
            elem.attr('data-enable', "1");
            break;
    }
}

function summernoteInit(){

    $('.summernote').each(function(index,elem){
        $(elem).summernote(
            {
                height: 300,
                focus: false,
                disableDragAndDrop: true,
                shortcuts: false,
                enterHtml: '',
                callbacks: {
                    onPaste: function (e) {
                        var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                        e.preventDefault();
                        document.execCommand('insertText', false, bufferText);
                    }
                },
                toolbar:[
                    ['style', ['style']],
                    ['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'videoAttributes', 'hr','link']],
                    ['HelloButton', ['HelloButton']],
                    ['view', ['fullscreen', 'codeview']],
                    ['help', ['help']],
                ]
            }
        );
    });

}

function copyButtonInit(){
    $('.copy-btn').each(function(index,elem){
        $(elem).click(function(){
            var input = $(elem).parent().find("input[type=text]");
            if(input.val() != ""){
            var copyText = $(elem).parent().find("input[type=text]")[0];
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.val());
            $(elem).find("i").removeClass("copy").addClass("check");
            setTimeout(function(){
                $(elem).find("i").removeClass("check").addClass("copy");
            },350);
            }
        });
    });
}

function fileUploadInit(){

    const uploadFiles = ( () => {

        const fileRequests = new WeakMap();

        const defaultOptions = {
            url : '/cms/upload',
            onAbort() {},
            onError() {},
            onProgress() {},
            onComplete() {},
        }

        const uploadFile = (file , options) => {
            const req = new XMLHttpRequest();
            
            const formData = new FormData();
            formData.append('file' , file);
            formData.append('input_name' , options.inputName);
            formData.append('is_multiple' , options.multiple);

            req.open('POST' , options.url , true);

            req.onload = (e) => {
                if (req.status === 200) {
                    options.onComplete(e, file , options , JSON.parse(req.response) );
                } else {
                    options.onError(e, file , options);
                }
            };

            req.onerror = (e) => options.onError(e, file , options);
            req.ontimeout = (e) =>  options.onError(e,file , options);
            req.upload.onprogress = (e) => options.onProgress(e,file , options);
            req.onabort = (e) =>  options.onAbort(e,file , options);
            
            fileRequests.set(file , {request : req , options});

            req.send(formData);
        }

        const abortFileUpload = file => {
            const fileReq = fileRequests.get(file);
            if(fileReq){ fileReq.request.abort(); }
        }

        const clearFileUpload = file => { abortFileUpload(file); fileRequests.delete(file); }

        return (files , options = defaultOptions) => {
            [...files].forEach(file => uploadFile(file , { ...defaultOptions , ...options}));
            return { abortFileUpload, clearFileUpload }
        }
    })();

    const uploadAndTrackFiles = ((index)=> {

        let uploader = new Array();
        let files = new Array();
        let progressBox = new Array();
        let filesProgressWrapper = new Array();
       
        const FILE_STATUS = { PENDNG : 'pending', UPLOADING : 'uploading', PAUSED : 'cancelled', COMPLETED : 'completed', FAILED : 'failed' };

        files[index] = new Map();
      
        progressBox[index] = document.createElement('div');
        progressBox[index].className = 'file-upload-progress-tracker';
        progressBox[index].innerHTML = `<div class="file-progress-wrapper"> </div>`;
        filesProgressWrapper[index] = progressBox[index].querySelector('.file-progress-wrapper');

        const setFileElement = (file , parentElement ,index) => {
            let fileElement = new Array();
            fileElement[index] = document.createElement('div');
            fileElement[index].className = 'upload-progress-tracker';
    

            fileElement[index].innerHTML = `
                <div class="file-details"> 
                  <div class="circular-progress" style="background: conic-gradient(#5C258D   0deg, #eee 0deg )"> 
                    <div class="value-container"> 0% </div>
                  </div>
                  <div class="file-info"> 
                    <span class="file-name"> ${file.name} </span>  
                    <span class="file-status"> ${FILE_STATUS.PENDNG} </span>
                  </div>
                </div>
                <div class="file-actions">
                    <button type="button" class="pause-btn"> <i class="trash alternate icon"></i> </button>   
                </div>`;
        
            files[index].set(file , { status : FILE_STATUS.PENDNG, size : file.size, percentage : 0, fileElement : fileElement[index], fileResponse : null , parentElement : parentElement});
            const [ , {children : [pauseBtn]} ] = fileElement[index].children;
            pauseBtn.addEventListener('click' , () => { uploader[index].abortFileUpload(file); });
            filesProgressWrapper[index].appendChild(fileElement[index]);
        }

        const updateFileElement = (fileObj,options) => {
            const [ {children : [ progressBar , {children : [ , fileStatus]}]} ] = fileObj.fileElement.children; 

            if(fileObj.fileResponse != null){
                var fileResponse = fileObj.fileResponse;              
                requestAnimationFrame(()=> {
                    fileStatus.textContent = "";
                    fileStatus.className = `status ${fileObj.status}`;              
                });
                fileObj.fileElement.outerHTML = fileResponse.file_element;
            }else{
                requestAnimationFrame(()=> {
                    fileStatus.textContent = fileObj.status;
                    fileStatus.className = `status ${fileObj.status}`;
                    progressBar.style.background = `conic-gradient(#5C258D   ${fileObj.percentage * 3.6}deg, #eee ${fileObj.percentage * 3.6}deg )`;
                    progressBar.innerHTML = `<div class="value-container"> ${parseInt(fileObj.percentage)}% </div>`;
                });
            }
        }

        const  onProgress = (e , file , options) => {
            const fileObj = files[options.index].get(file);
            fileObj.status = FILE_STATUS.UPLOADING;
            fileObj.percentage = e.loaded * 100 / e.total;
            updateFileElement(fileObj , options);
        };

        const  onError = (e , file , options) => { 
            const fileObj = files[options.index].get(file);
            fileObj.status = FILE_STATUS.FAILED;
            fileObj.percentage = 100;
            updateFileElement(fileObj , options);
        };

        const  onAbort = (e , file , options) => { 
            const fileObj = files[options.index].get(file);
            fileObj.status = FILE_STATUS.PAUSED;
            updateFileElement(fileObj , options);
            setTimeout(function(){ $(fileObj.fileElement).slideUp(); $(fileObj.fileElement).remove(); }, 700);
        };
  
        const  onComplete = (e , file , options , response) => {
            const fileObj = files[options.index].get(file);
            fileObj.status = FILE_STATUS.COMPLETED;
            fileObj.percentage = 100;
            fileObj.fileResponse =  response;
            updateFileElement(fileObj , options);
        };

        return (uploadedFiles , parentElement , elem , i) => {
            [...uploadedFiles].forEach((file) => {  setFileElement(file , parentElement, i)  });
            uploader[i] = uploadFiles(uploadedFiles , { url : "/cms/upload", onAbort, onError, onComplete, onProgress, index : i , elem : elem,
            multiple : elem.attr('multiple') == true || elem.attr('multiple') == 'multiple' , 
            inputName : elem.attr('name')
            });
            parentElement.appendChild(progressBox[i]);
        }   

    });

    $('.file-upload-label').each(function(index,element){
        
        element.addEventListener('change' , e => {

          let multiple =  $(e.target).attr('multiple') == 'multiple' || $(e.target).attr('multiple') == true;
          console.log(multiple);
          if(!multiple){
            $(element).parent().find('.file-upload-progress-tracker').slideUp();
          }

          uploadAndTrackFiles(index)(e.target.files , $(element).parent()[0] , $(e.target) ,index);
        });
    });
}

function fileElementDelete(elem){
    elem.parents('.file-upload-progress-tracker').slideUp(); 
    setTimeout(function(){ elem.parents('.file-upload-progress-tracker').remove(); },1000);
}

function validateBeforeSubmit(){
  

    // $('#page').submit(function(event) {
        
    //     var array = new Array;
    //     var valid = true;

    //     $(this).find("input[name^='upld_']").each(function(index , elem){
    //         var element = $(elem);
        
    //         if(element.attr('multiple') == 'multiple' || element.attr('multiple') == true){
    //             var name_old = element.attr('name').replace("upld", "tmp")    +'[]'; 
    //             var name_new = element.attr('name').replace("upld", "") +'[]'; 
    //         }else{
    //             var name_old = element.attr('name').replace("upld", "tmp"); 
    //             var name_new = element.attr('name').replace("upld", ""); 
    //         }

    //         var name_old_length = $('input[name='+name_old+']').length;
    //         var name_new_length = $('input[name='+name_new+']').length;
    //         var sum = name_new_length + name_old_length;

    //         valid = valid && (sum != 0);

          
    //     });

    //         if(!valid){
    //             console.log("not valid");
    //             return false;
    //         }   


    //         return false;
    // });

   
}



function updateRelatedFields(elem){
  
    var is_checked = elem.prop('checked') == true;

    var related_fields = elem.attr("data-related-fields");
    var array = related_fields.split(',');

    array.forEach(function(item , index){
        var array2 = item.split('|');
        var input = $('input[name='+array2[0]+']');
        var component = input.parents('.related');
        
        if(is_checked){
            component.removeClass('d-none');
        }else{
            component.addClass('d-none');
        }

        if(array2.length == 2){
            if(!is_checked){
                input.prop('required' , array2[1] == 'required');
            }else{
                input.prop('required' , false);
            }
        }
    });
}

function relatedFieldsInit(){
    $('input[data-related-fields]').each(function(index , elem){
        updateRelatedFields($(elem));
    });
}

$( document ).ready(function() {
    semanticInit();
    sidebarInit();
    sortByColumn();
    summernoteInit();
    copyButtonInit();
    fileUploadInit();
    validateBeforeSubmit();
    relatedFieldsInit();
    try {
        $('#complex-variants').slideUp(0);
        $('.variation-area').slideUp(0);
    } catch (error) {
        
    }
    
    
   

});
