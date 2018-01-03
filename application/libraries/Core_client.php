<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Core client communication
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
class Core_client {

    private $CI;
    private $messages;
    private $fragments_data;

    public function __construct() {
        $this->CI = & get_instance();
        $this->clear_messages();
        $this->clear_fragments_data();
    }

    /**
     * Clean all messages
     */
    public function clear_messages() {
        $this->messages = [];
    }

    /**
     * Clean fragments data
     */
    public function clear_fragments_data() {
        $this->fragments_data = [];
    }

    /**
     * Set fragment data
     * @param string $fragment_name Fragment name
     * @param string $fragments_data Fragment data
     * @param string $post_update_action Action to execute after fragment render
     * @param string $post_update_action_params Post fragment render action params
     */
    public function set_fragment_data($fragment_name, $fragments_data, $post_update_action = null, $post_update_action_params = null) {
        $this->fragments_data[$fragment_name] = $fragments_data;
        if ($post_update_action) {
            $this->fragments_data[$fragment_name]['post_update_action'] = $post_update_action;
            $this->fragments_data[$fragment_name]['post_update_action_params'] = $post_update_action_params;
        }
    }

    /**
     * Add message
     * @param array $message Message to insert
     *              Message prototype:
     *              - type => Message type
     *                          - console
     *                          - location
     *              - metadata => Message metadata
     *                              - console:
     *                                  - level (log, warn, error)
     *                                  - message
     *                              - location:
     *                                  - href
     */
    public function add_message($message) {
        $this->messages[] = $message;
    }

    /**
     * Prepare xml response
     */
    public function xml_response() {
        $xml = new SimpleXMLElement('<response/>');

        // Fragments
        $this->add_update_fragments_to_xml($xml);

        // Messages
        $this->add_messages_to_xml($xml);
        foreach ($this->messages as $message) {
            switch ($message['type']) {
                case 'console':
                    $this->add_console_output_to_xml($xml, $message);
                    break;
                case 'location':
                    $this->add_location_output_to_xml($xml, $message);
                    break;
            }
        }

        $this->CI->output
                ->set_content_type('application/xml')
                ->set_output($xml->asXML());
    }

    private function add_update_fragments_to_xml(&$xml) {
        $update = $this->CI->input->post('update');
        if ($update == null) {
            return;
        }
        $xml->fragments = new SimpleXMLElement('<fragments/>');
        $fragments = explode(' ', $update);
        foreach ($fragments as $fragment) {
            $xml->fragments->$fragment = null;
            $this->add_cdata($xml->fragments->$fragment, $this->CI->load->view('fragments/' . $fragment, isset($this->fragments_data[$fragment]) ? $this->fragments_data[$fragment] : null, true));
            if (isset($this->fragments_data[$fragment]['post_update_action'])) {
                $xml->fragments->$fragment['post_update_action'] = $this->fragments_data[$fragment]['post_update_action'];
            }
            if (isset($this->fragments_data[$fragment]['post_update_action_params'])) {
                $xml->fragments->$fragment['post_update_action_params'] = $this->fragments_data[$fragment]['post_update_action_params'];
            }
        }
    }

    private function add_messages_to_xml(&$xml) {
        $xml->messages = new SimpleXMLElement('<messages/>');
    }

    private function add_console_output_to_xml(&$xml, $message) {
        if (!isset($message['metadata']['message'])) {
            return;
        }
        $console = $xml->messages->addChild('console');
        $this->add_cdata($console, $message['metadata']['message']);
        $level = (isset($message['metadata']['level'])) ? $message['metadata']['level'] : 'log';
        if (!in_array($level, ['log', 'warn', 'error'])) {
            $level = 'log';
        }
        $console['level'] = $level;
    }

    private function add_location_output_to_xml(&$xml, $message) {
        if (!isset($message['metadata']['href'])) {
            return;
        }
        $location = $xml->messages->addChild('location');
        $this->add_cdata($location, $message['metadata']['href']);
    }

    private function add_cdata(&$root, $cdataText) {
        $node = dom_import_simplexml($root);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdataText));
    }

}
