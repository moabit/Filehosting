(function () {
    window.addEventListener("dragover", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
    window.addEventListener("drop", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
    var dropzone = document.getElementById('dropzone');
    var inputFile = document.getElementById('inputFileUpload');
    var submitButton = document.getElementById('submitFileUploadButton');
    var labelFile = document.getElementById('labelUpload');
    var errorList = document.getElementById('error-message');
    var uploadBlock=document.getElementById('uploadBlock');
    var validateFiles=function (files) {
        var errors=[];
        if (files.length > 1) {
            errors.push('Загрузка нескольких файлов одновременно невозможна');
        }
        if (files[0].size > 900000000000000000000000000000000000000) {
            errors.push ('Ваш файл слишком большой');
        }
       return errors;
    };
    inputFile.oninput = function (e) {
        e.preventDefault();
        var errors = validateFiles(inputFile.files);
        if (errors.length > 0) {
            var oldErrorList=document.getElementById('errorList');
            if (oldErrorList) {
                oldErrorList.parentNode.removeChild(oldErrorList);
            }
            var errorList=document.createElement('ul');
            errorList.setAttribute('class', 'alert alert-danger');
            errorList.setAttribute('id', 'errorList');
            for (var key in errors) {
                var error = document.createElement('li');
                error.innerHTML = errors[key];
                errorList.appendChild(error);
            }
            uploadBlock.parentNode.insertBefore(errorList, uploadBlock);
        }
        else {
            dropzone.style.cssText="background-color:  #e6e6ff";
            labelFile.textContent=inputFile.files[0].name;}
    };
    dropzone.ondrop = function (e) {
        e.preventDefault();
        inputFile.files = e.dataTransfer.files;
        labelFile.textContent=inputFile.files[0].name;
    };
    dropzone.ondragover = function (e) {
        e.preventDefault();
        this.style.cssText="background-color:  #e6e6ff";
        return false;
    };
    dropzone.ondragleave = function (e) {
        e.preventDefault();
        this.style.cssText = '';
        return false;
    };
})();
