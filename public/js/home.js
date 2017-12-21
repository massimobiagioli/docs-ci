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
                '" type="text" class="form-control input-md" />' +
                '<td><input name="value' + rowIndex + 
                '" type="text" class="form-control input-md" />' +
                '<td><div class="metadata-icons">' +
                '<i class="delete_row_metadata fa fa-trash fa-2x" data-rowid="' + (rowIndex) + '" ' + 
                'aria-hidden="true"></i></div></td>'; 
        $('#metadata_row_' + rowIndex).html(rowContent);
        $('#document_metadata').append('<tr id="metadata_row_' + (rowIndex + 1) + '"></tr>');
        rowIndex++;
    });
    
    // Delete row from metadata
    $("body").on('click', '.delete_row_metadata', function() {
        var index = $(this).data('rowid');        
        $("#metadata_row_" + index).html('');        
    });
});