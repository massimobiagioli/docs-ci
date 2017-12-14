$(function () {
    var rowIndex = 1;
    
    // Form submit
    $('.form-home').submit(function (evt) {
        evt.preventDefault();
        Ignition.sendRequest(this);
    });
    
    // Add row to metadata
    $("#add_row_metadata").click(function () {
        var rowContent = '<td><input name="key' + rowIndex + 
                '" type="text" class="form-control input-md" />';
        rowContent += '<td><input name="value' + rowIndex + 
                '" type="text" class="form-control input-md" />';
        $('#metadata_row_' + rowIndex).html(rowContent);
        $('#document_metadata').append('<tr id="document_metadata_' + (rowIndex + 1) + '"></tr>');
        rowIndex++;
    });
    
    // Delete row from metadata
    $("#delete_row_metadata").click(function () {
        if (rowIndex > 1) {
            $("#metadata_row_" + (rowIndex - 1)).html('');
            rowIndex--;
        }
    });

});