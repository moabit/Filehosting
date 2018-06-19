function copyToClipboard(e) {
    e.preventDefault();
    var link = document.getElementById('downloadLink');
    link.select();
    document.execCommand("copy");
}