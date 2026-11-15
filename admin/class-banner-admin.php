<?php
/**
 * Banner Admin Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Banner_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_filter('post_updated_messages', array($this, 'updated_messages'));
    }
    
    /**
     * Custom updated messages
     */
    public function updated_messages($messages) {
        $post = get_post();
        $post_date = ($post instanceof WP_Post) ? strtotime($post->post_date) : current_time('timestamp');
        $revision_id = isset($_GET['revision']) ? absint(wp_unslash($_GET['revision'])) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        
        $messages['simple_banner'] = array(
            0  => '',
            1  => __('Banner updated.', 'simple-banner'),
            2  => __('Custom field updated.', 'simple-banner'),
            3  => __('Custom field deleted.', 'simple-banner'),
            4  => __('Banner updated.', 'simple-banner'),
            5  => $revision_id ? sprintf(__('Banner restored to revision from %s', 'simple-banner'), wp_post_revision_title($revision_id, false)) : false,
            6  => __('Banner published.', 'simple-banner'),
            7  => __('Banner saved.', 'simple-banner'),
            8  => __('Banner submitted.', 'simple-banner'),
            9  => sprintf(
                __('Banner scheduled for: <strong>%1$s</strong>.', 'simple-banner'),
                date_i18n(__('M j, Y @ G:i', 'simple-banner'), $post_date)
            ),
            10 => __('Banner draft updated.', 'simple-banner'),
        );
        
        return $messages;
    }
}
