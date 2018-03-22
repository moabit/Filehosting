(function () {
    var dropzone = document.getElementById('dropzone');
    var input = document.getElementById('inputFileUpload');
    var submitButton = document.getElementById('submitFileUploadButton');
    var label = document.getElementById('labelUpload');
    window.addEventListener("dragover", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
    window.addEventListener("drop", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
//валидация еще
    input.oninput = function (e) {
         label.textContent=input.files[0].name;
        //  alert(input.files.length);
       //  alert (input.files[0].size);
    };
    dropzone.ondrop = function (e) {
        e.preventDefault();
        input.files = e.dataTransfer.files;
        label.textContent=input.files[0].name;
    };

    dropzone.ondragover = function () {
        this.className += 'dropzone.dragover';
        return false;
    };

    dropzone.ondragleave = function () {
        this.className += 'dropzone';
        return false;
    };

})();