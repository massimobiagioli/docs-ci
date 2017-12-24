// Custom configuration
var CustomModule = (function() {

    /**
     * Return custom configuration for each datatable
     * @param string dataTableId DataTable Id
     * @param object $dataTable DataTable object
     */
    var getDataTableConfig = function(dataTableId, $dataTable) {
        dataTableConfig = {
            results: {
                "searching": false,
                "lengthChange": false,
                "info": false,
                "pageLength": $dataTable.data('pagelength'),
                "pagingType": "full_numbers",
                "language": {
                    "paginate": {
                        "first": "<i class=\"fa fa-fast-backward fa-fw\"></i>",
                        "previous": "<i class=\"fa fa-step-backward fa-fw\"></i>",
                        "next": "<i class=\"fa fa-step-forward fa-fw\"></i>",
                        "last": "<i class=\"fa fa-fast-forward fa-fw\"></i>"
                    },
                    "emptyTable" : $dataTable.data('noresultsmsg')
                },
                "columnDefs": [
                    { orderable: false, targets: 2 }
                ],
                "serverSide": true,
                ajax: {
                    url: $dataTable.data('url'),
                    type: 'POST'
                }
            }
        };
        return dataTableConfig[dataTableId];
    };
    
    return {
        getDataTableConfig: getDataTableConfig
    };
    
})();