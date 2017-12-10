$(function() {
    $('.form-admin').submit(function(evt) {
        evt.preventDefault();
        Ignition.sendRequest(this);
    });
});