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
            var comment = JSON.parse(xhr.response);
            var depth = comment.matpath.split('.').length;
            var div = document.createElement("div");
            var date = new Date();
            div.style = 'margin-left: '+(depth*30)+'px'+ ' !important';
            div.innerHTML = "<hr>" + comment.author + date + "<br>" + comment.text + "<hr>";
            if (depth == 1) { //root
                var comments = document.getElementById('commentBlock');
                comments.appendChild(div);
            } else { // child
                var parent = document.getElementById(comment.parent_id);
                parent.parentNode.insertBefore(div, parent.nextSibling);

            }
        }
        var textarea = document.getElementById('textarea');
        textarea.value = '';
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
    //  replyForm.setAttribute('id','opened');
    replyForm.id = 'replyForm';
    replyForm.parentId.value = id;
    replyForm.className += " border p-3 border-info";
    replyForm.submitButton.id = 'reply';
    var parent = document.getElementById(id);
    parent.parentNode.insertBefore(replyForm, parent.nextSibling);
}
