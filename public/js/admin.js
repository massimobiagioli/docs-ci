$(function() {
    $('#form-admin-create-index').submit(function(evt) {
        evt.preventDefault();
        Ignition.sendRequest(this);
    });
});