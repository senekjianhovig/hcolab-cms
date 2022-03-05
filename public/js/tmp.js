/*
UploadFiles
*/
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
/*
uploadAndTrackFiles
*/
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
        index = i;

        [...uploadedFiles].forEach((file) => { setFileElement(file , parentElement, index) });

        uploader[index] = uploadFiles(uploadedFiles , {
            url : "/cms/upload",
            onAbort,
            onError,
            onComplete,
            onProgress,
            index : index
        });
   
       

        // parentElement.appendChild(progressBox[index]);
}

})();





$('.file-upload-label').each(function(index,element){
    element.addEventListener('change' , e => {
        uploadAndTrackFiles(e.target.files , $(element).parent()[0] , index);
    });
});
