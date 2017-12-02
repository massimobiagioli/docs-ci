/**
 * Ignition Module
 * @author Massimo Biagioli
 * @version 0.1
 */
var Ignition = (function() {
    
    /**
     * Send request to CI
     * @param object sender
     */
    var sendRequest = function(sender) {
        var $sender = $(sender);
        var params = '';
        var action = $sender.data('action') || $sender.attr('action');
        
        if ($sender.is('form')) {
            params = $sender.serialize();
        }
        if ($sender.data('update')) {
            if (params.length > 0) {
                params += '&';
            }
            params += 'update=' + $sender.data('update');
        }
        
        $.post(action, params, function(data) {
            var xml = $(data);

            // Update Fragments
            updateFragments(xml.find('fragments'));

            // Process Messages
            processMessages(xml.find('messages'));
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
            $('#' + nodeName).html(nodeContent);
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
