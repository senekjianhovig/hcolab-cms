const TABLE_DATA_URL = "/cms/page/{page_slug}/query";


function semanticInit(){
    $('.ui.dropdown').dropdown();
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

    $('.filepond').each(function(index,elem){
        var inputElement = elem; 
        FilePond.create(inputElement, {
                server : { 
                url : '/upload',
                name: 'hovig',
                headers : { 
                    'X-CSRF-TOKEN' : $('meta[name=csrf-token]').attr('content')  
                }
            }
        });
    });
      
}


function initializeCustomFileUpload(){

    const url = '/upload'; 

    $(".hco-file-upload").each(function(index,elem){
    
      var mainElement = $(elem); 

      var form = mainElement.find(".frm");
      
      var fileInput = mainElement.find(".file-input");
      var progressArea = mainElement.find(".progress-area");
      var uploadedArea = mainElement.find(".uploaded-area");
    
      fileInput.on('change' , function(e){
        
        e.preventDefault();
   
        var files = fileInput[0].files;
     

        for (let i = 0; i < files.length; i++) {
            let file = files.item(i);
            
         
    
            let fileName = file.name;
            if(fileName.length >= 12){
                let splitName = fileName.split('.');
                fileName = splitName[0].substring(0, 13) + "... ." + splitName[1];
            }

           console.log(file);

            let formData = new FormData();
            
            formData.append(fileInput[0].name, file);

         
            uploadFile(fileName, formData , uploadedArea , progressArea);
        }

        

      });
    
    });



}

// function uploadFile(name , formData , uploadedArea , progressArea){
//   let xhr = new XMLHttpRequest();
//   xhr.open("POST", "/upload");
//   xhr.upload.addEventListener("progress", ({loaded, total}) =>{
//     let fileLoaded = Math.floor((loaded / total) * 100);
//     let fileTotal = Math.floor(total / 1000);
//     let fileSize;
//     (fileTotal < 1024) ? fileSize = fileTotal + " KB" : fileSize = (loaded / (1024*1024)).toFixed(2) + " MB";


// let progressHTML = `
// <div class="upload-box upload-box-active">
// <div class="type">
//     <i class="fas fa-file-alt"></i>
// </div>
// <div class="middle">
//     <div class="info">
//         <span class="label"> ${name}</span>
//         <span class="percentage">${fileLoaded}%</span>
//     </div>
//     <div class="bar-wrapper">
//         <div class="bar"></div>
//         <div class="bar-filled" style="right: ${100-fileLoaded}%"></div>
//     </div>
// </div>
// <div class="remove">
//     <button type="button">
//         <i class="fas fa-times"></i>
//     </button>
// </div>
// </div>
// `;

//     uploadedArea.addClass("onprogress");
//     progressArea.html(progressHTML);
//     if(loaded == total){
//         progressArea.html('');
//     let uploadedHTML = `<div class="upload-box ">
//         <div class="type">
//             <i class="fas fa-file-alt"></i>
//         </div>
//         <div class="middle">
//             <div class="info">
//                 <span class="label"> ${name}</span>
//                 <span class="percentage"></span>
//             </div>
//             <div class="file-size">
//             ${fileSize}
//             </div>
//         </div>
//         <div class="check">
//             <i class="fas fa-check"></i>
//         </div>
//     </div>`;
//       uploadedArea.removeClass("onprogress");
//       uploadedArea.append(uploadedHTML);
//     }
//   });
 
//   xhr.send(formData);
// }

function initBluImp(){


   
    const uploadFiles = ( () => {

        const fileRequests = new WeakMap();

        const defaultOptions = {
            url : '/upload',
            onAbort() {},
            onError() {},
            onProgress() {},
            onComplete() {},
        }

        const uploadFile = (file , options) => {
            const req = new XMLHttpRequest();

            const formData = new FormData();
            formData.append('upld_files' , file);

            req.open('POST' , options.url , true);

            req.onload = (e) => {
                if (req.status === 200) {
                    options.onComplete(e, file , options.index);
                } else {
                    options.onError(e, file , options.index);
                }
            };

            req.onerror = (e) => options.onError(e, file , options.index);
            req.ontimeout = (e) =>  options.onError(e,file , options.index);
            req.upload.onprogress = (e) => options.onProgress(e,file , options.index);
            req.onabort = (e) =>  options.onAbort(e,file , options.index);
            
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

    const uploadAndTrackFiles = (()=> {

        let uploader = new Array();
        let files = new Array();
        let progressBox = new Array();
        let filesProgressWrapper = new Array();
        let index;

        const FILE_STATUS = {
            PENDNG : 'pending',
            UPLOADING : 'uploading',
            PAUSED : 'paused',
            COMPLETED : 'completed',
            FAILED : 'failed'
        };

        files[index] = new Map();
        console.log(files);
        progressBox[index] = document.createElement('div');
        progressBox[index].className = 'upload-progress-tracker';
        progressBox[index].innerHTML = `
            <h3> Upload </h3>
            <div class="file-progress-wrapper"> </div>
        `;
    
        filesProgressWrapper[index] = progressBox[index].querySelector('.file-progress-wrapper');

        const setFileElement = (file , parentElement ,index) => {
        let fileElement = new Array();
        fileElement[index] = document.createElement('div');
        fileElement[index].className = 'upload-progress-tracker';
        
        fileElement[index].innerHTML = `
            <div class="file-details"> 
                <p> 
                    <span class="file-name"> ${file.name} </span>  
                    <span class="file-status"> ${FILE_STATUS.PENDNG} </span>
                </p>
                <div class="progress-bar" style="width : 0 ; height:2px ; background: green"> </div>
            </div>
            <div class="file-actions">
                <button type="button" class="pause-btn"> Pause </button>
            </div>
        `;

        files[index].set(file , {
            status : FILE_STATUS.PENDNG,
            size : file.size,
            percentage : 0,
            fileElement : fileElement[index]
        });

        const [ , {children : [pauseBtn]} ] = fileElement[index].children;
        pauseBtn.addEventListener('click' , () => uploader[index].abortFileUpload(file));
        filesProgressWrapper[index].appendChild(fileElement[index]);
        }

        const updateFileElement = fileObj => {
            const [ {children : [ {children : [ , fileStatus]} , progressBar]} ] = fileObj.fileElement.children; 

            requestAnimationFrame(()=> {
                fileStatus.textContent = fileObj.status;
                fileStatus.className = `status ${fileObj.status}`;
                progressBar.style.width = fileObj.percentage + '%';
            });
        }

        const  onProgress = (e , file , i) => {
        const fileObj = files[i].get(file);
        fileObj.status = FILE_STATUS.UPLOADING;
        fileObj.percentage = e.loaded * 100 / e.total;
        updateFileElement(fileObj);
        };

        const  onError = (e , file , i) => { 
        const fileObj = files[i].get(file);
        fileObj.status = FILE_STATUS.FAILED;
        fileObj.percentage = 100;
        updateFileElement(fileObj);
        };

        const  onAbort = (e , file , i) => { 
        const fileObj = files[i].get(file);
        fileObj.status = FILE_STATUS.PAUSED;
        updateFileElement(fileObj);
        };
  
        const  onComplete = (e , file , i) => {
        const fileObj = files[i].get(file);
        fileObj.status = FILE_STATUS.COMPLETED;
        fileObj.percentage = 100;
        updateFileElement(fileObj);
        };

        return (uploadedFiles , parentElement , i) => {

            [...uploadedFiles].forEach((file) => { setFileElement(file , parentElement, index) });

            index = i;

            uploader[index] = uploadFiles(uploadedFiles , {
                url : "/upload",
                onAbort,
                onError,
                onComplete,
                onProgress,
                index : index
            });
       
           

            // parentElement.appendChild(progressBox[index]);
    }

    })();

    
    const uploadBtn = document.getElementsByClassName('file-upload-label');

    Array.from(uploadBtn).forEach(function(element , index) {
        let parentElement = element.parentElement;
        
        
        element.addEventListener('change' , e => {
           uploadAndTrackFiles(e.target.files , parentElement , index);
        });
    });

   
    

}




$( document ).ready(function() {
    semanticInit();
    sidebarInit();
    sortByColumn();
    summernoteInit();
    copyButtonInit();
    // fileUploadInit();
    initBluImp();
});
