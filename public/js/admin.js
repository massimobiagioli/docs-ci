$(function() {
    $('.form-admin').submit(function(evt) {
        evt.preventDefault();
        Core.sendRequest(this);
    });
});