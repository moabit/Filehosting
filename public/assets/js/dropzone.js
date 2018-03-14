(function () {
    var dropzone = document.getElementById('dropzone');
    var input=document.getElementById('inputFileUpload');
    var submitButton = document.getElementById('submitFileUploadButton');
    var label=document.getElementById('labelUpload');
    window.addEventListener("dragover", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
    window.addEventListener("drop", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);

    dropzone.ondrop = function (e) {
        e.preventDefault();
        input.files = e.dataTransfer.files;
    };

    dropzone.ondragover = function () {
        this.className = 'dropzone dragover';
        return false;
    };

    dropzone.ondragleave = function () {
        this.className = 'dropzone';
        return false;
    };

})();