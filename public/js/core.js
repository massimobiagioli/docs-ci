// Ajax Loader
var $loading = $('.loading').hide();
$(document)
  .ajaxStart(function () {
    $loading.show();
  })
  .ajaxStop(function () {
    $loading.hide();
  });

// Core
var Core = (function() {
    
    var postUpdateFragmentActions = {
        renderDataTable: function(dataTableId) {
            var $dataTable = $('#' + dataTableId);
            $dataTable.DataTable(CustomModule.getDataTableConfig('results', $dataTable));
        }
    };
    
    /**
     * Send request to CI
     * @param object sender
     * @param object csrf
     * @param object additionalData
     */
    var sendRequest = function(sender, csrf, additionalData) {
        var $sender = $(sender);
        var action = $sender.data('action') || $sender.attr('action');
        var formData;
        
        if ($sender.is('form')) {
            formData = new FormData($sender[0]);
        } else {
            formData = new FormData();
        }
        if ($sender.data('update')) {
            formData.append('update', $sender.data('update'));
        }
        
        // csrf protection
        if (csrf) {
            formData.append(csrf['token'], csrf['hash']);
        }
        
        // additional data
        if (additionalData) {
            for (var key in additionalData) {
                formData.append(key, additionalData[key]);
            }
        }
        
        $.ajax({
            url: action,
            type: 'post',
            data: formData,        
            processData: false,
            contentType: false,
            success: function(data) {
                var xml = $(data);

                // Update Fragments
                updateFragments(xml.find('fragments'));

                // Process Messages
                processMessages(xml.find('messages'));
            }
        });
    };
    
    /**
     * Update Fragments
     * @param array fragments Fragments to update
     */
    function updateFragments(fragments) {
        fragments.children().each(function (index, node) {
            var $node = $(node);
            var nodeName = $node.prop('tagName');
            var nodeContent = $node.text();
            var postUpdateAction = $node.attr('post_update_action');
            var postUpdateActionParams = $node.attr('post_update_action_params');
            $('#' + nodeName).html(nodeContent);
            
            // Execute post update action
            if (postUpdateAction) {
                postUpdateFragmentActions[postUpdateAction](postUpdateActionParams);
            }
        });
    }
    
    /**
     * Process Messages
     * @param array messages Messages to process
     */
    function processMessages(messages) {
        messages.children().each(function (index, node) {
            var $node = $(node);
            var nodeName = $node.prop('tagName');
            switch (nodeName) {
                case 'console':
                    resolveConsoleMessage($node);
                    break;
                case 'location':
                    resolveLocationMessage($node);
                    break;
            }
        });
    }
    
    /**
     * Send message to js console
     * @param object $node Node
     */
    function resolveConsoleMessage($node) {
        switch ($node.attr('level')) {
            case 'log':
                console.log($node.text());
                break;
            case 'warn':
                console.warn($node.text());
                break;
            case 'error':
                console.error($node.text());
                break;
            default:
                console.log($node.text());
                break;
        }
    }
    
    /**
     * Change location
     * @param object $node Node
     */
    function resolveLocationMessage($node) {
        window.location.href = $node.text();
    }
    
    return {
        sendRequest: sendRequest
    };
    
})();
