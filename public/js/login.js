$(function() {
    $('#form-login').submit(function(evt) {
        evt.preventDefault();
        Ignition.sendRequest(this);
    });
});