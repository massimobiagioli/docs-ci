<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ignition client communication
 * @author Massimo Biagioli <biagiolimassimo@gmail.com>
 */
class Ignition_client {

    private $CI;
    private $messages;
    private $fragments_data;

    public function __construct() {
        $this->CI = & get_instance();
        $this->clearMessages();
        $this->clearFragmentsData();
    }

    /**
     * Clean all messages
     */
    public function clearMessages() {
        $this->messages = [];
    }

    /**
     * Clean fragments data
     */
    public function clearFragmentsData() {
        $this->fragments_data = [];
    }

    /**
     * Set fragment data
     * @param string $fragment_name Fragment name
     * @param string $fragments_data Fragment data
     */
    public function set_fragment_data($fragment_name, $fragments_data) {
        $this->fragments_data[$fragment_name] = $fragments_data;
    }

    /**
     * Add message
     * @param array $message Message to insert
     *              Message prototype:
     *              - type => Message type
     *                          - console
     *              - metadata => Message metadata
     *                              - console:
     *                                  - level (log, warn, error)
     *                                  - message
     *                              - location:
     *                                  - href
     */
    public function addMessage($message) {
        $this->messages[] = $message;
    }

    /**
     * Prepare xml response
     */
    public function xmlResponse() {
        $xml = new SimpleXMLElement('<response/>');

        // Fragments
        $this->addUpdateFragmentsToXml($xml);

        // Messages
        $this->addMessagesToXml($xml);
        foreach ($this->messages as $message) {
            switch ($message['type']) {
                case 'console':
                    $this->addConsoleOutputToXml($xml, $message);
                    break;
                case 'location':
                    $this->addLocationOutputToXml($xml, $message);
                    break;
            }
        }

        $this->CI->output
                ->set_content_type('application/xml')
                ->set_output($xml->asXML());
    }

    private function addUpdateFragmentsToXml(&$xml) {
        $update = $this->CI->input->post('update');
        if ($update == null) {
            return;
        }
        $xml->fragments = new SimpleXMLElement('<fragments/>');
        $fragments = explode(' ', $update);
        foreach ($fragments as $fragment) {
            $xml->fragments->$fragment = null;
            $this->addCData($xml->fragments->$fragment, $this->CI->load->view('fragments/' . $fragment, isset($this->fragments_data[$fragment]) ? $this->fragments_data[$fragment] : null, true));
        }
    }

    private function addMessagesToXml(&$xml) {
        $xml->messages = new SimpleXMLElement('<messages/>');
    }

    private function addConsoleOutputToXml(&$xml, $message) {
        if (!isset($message['metadata']['message'])) {
            return;
        }
        $console = $xml->messages->addChild('console');
        $this->addCData($console, $message['metadata']['message']);
        $level = (isset($message['metadata']['level'])) ? $message['metadata']['level'] : 'log';
        if (!in_array($level, ['log', 'warn', 'error'])) {
            $level = 'log';
        }
        $console['level'] = $level;
    }

    private function addLocationOutputToXml(&$xml, $message) {
        if (!isset($message['metadata']['href'])) {
            return;
        }
        $location = $xml->messages->addChild('location');
        $this->addCData($location, $message['metadata']['href']);
    }

    private function addCData(&$root, $cdataText) {
        $node = dom_import_simplexml($root);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdataText));
    }

}
