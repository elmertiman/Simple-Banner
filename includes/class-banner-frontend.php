<?php
/**
 * Banner Frontend Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Banner_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Frontend initialization if needed
    }
}

