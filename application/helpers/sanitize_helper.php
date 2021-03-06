<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('sanitize_index')) {
    /**
     * Sanitize index name
     * @param string $string Input string
     * @return Sanitized index name
     */
    function sanitize_index($string) {
        return preg_replace('/\s+/', '_', $string);
    }

}

if (!function_exists('sanitize_metadata')) {
    /**
     * Sanitize metadata string
     * @param string $string Input string
     * @return Sanitized metadata
     */
    function sanitize_metadata($string) {
        return preg_replace('/\s+/', '_', $string);
    }

}
