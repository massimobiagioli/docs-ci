$(function() {
    $('#form-login').submit(function(evt) {
        evt.preventDefault();
        Core.sendRequest(this);
    });
});