function addComment(e, buttonId) {
    e.preventDefault();
    var xhr = new XMLHttpRequest();
    xhr.open("POST", window.location, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    if (buttonId !== 'reply') { // root comment
        var form = document.getElementById('mainCommentForm');
    } else { // child Comment
        var form = document.getElementById('replyForm');
    }
    var formData = new FormData(form);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            var response = JSON.parse(xhr.response);
            if (!response.hasOwnProperty('author')) { // server side validation error
                var oldErrorList = document.getElementById('errorList');
                if (oldErrorList) {
                    oldErrorList.parentNode.removeChild(oldErrorList);
                }
                var errorList = document.createElement('ul');
                errorList.setAttribute('class', 'alert alert-danger');
                errorList.setAttribute('id', 'errorList');
                for (var key in response) {
                    var error = document.createElement('li');
                    error.innerHTML = response[key];
                    errorList.appendChild(error);
                }
                var form=document.getElementById('mainCommentForm');
                form.parentNode.insertBefore(errorList,form);
            }
            else { // server side validation succeded
                var depth = response.matpath.split('.').length;
                var newComment = document.createElement('div');
                newComment.setAttribute("class", "m-1 border p-3");
                var commentHeader = document.createElement('div');
                commentHeader.setAttribute('class', "font-italic");
                var commentText = document.createElement('div');
                newComment.style = 'margin-left: ' + (depth * 30) + 'px' + ' !important';
                commentHeader.innerHTML = response.author + " " + response.posted;
                commentText.innerHTML = response.comment_text;
                newComment.appendChild(commentHeader);
                newComment.appendChild(commentText);
                if (depth == 1) { //root
                    var comments = document.getElementById('commentBlock');
                    comments.appendChild(newComment);
                } else { // child
                    var parent = document.getElementById(response.parent_id);
                    parent.parentNode.insertBefore(newComment, parent.nextSibling);
                }
            }
            var textarea = document.getElementById('textarea');
            textarea.value = '';
        }
    };
    xhr.send(formData);
}

function showReplyForm(id) {
    var oldReplyForm = document.getElementById('replyForm');
    if (oldReplyForm) {
        oldReplyForm.parentNode.removeChild(oldReplyForm);
    }
    var form = document.getElementById('mainCommentForm');
    var replyForm = form.cloneNode(true);
    replyForm.id = 'replyForm';
    replyForm.parentId.value = id;
    replyForm.className += " border p-3 border-info";
    replyForm.submitButton.id = 'reply';
    var parent = document.getElementById(id);
    parent.parentNode.insertBefore(replyForm, parent.nextSibling);
}
