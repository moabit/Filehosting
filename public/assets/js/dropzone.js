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
    var validateFiles=function (files) {
        var errors=[];
        if (files.length > 1) {
            errors.push('Загрузка нескольких файлов одновременно невозможна');
        }
        if (files[0].size > 90000000000000000000000000000000000000000000000000000000000000) {
            errors.push ('Ваш файл слишком большой');
        }
       return errors;
    };
    inputFile.oninput = function (e) {
        e.preventDefault();
        var errors = validateFiles(inputFile.files);
        if (errors.length > 0) {
            errorList.removeAttribute('hidden');
            for (var i=0; i<errors.length; i++) {
                var item = document.createElement('li');
                item.appendChild(document.createTextNode(errors[i]));
                errorList.appendChild(item);
            }
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
