<?php
/**
 * Banner Custom Post Type
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Banner_Post_Type {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_filter('manage_simple_banner_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_simple_banner_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
    }
    
    /**
     * Register Custom Post Type
     */
    public function register_post_type() {
        $labels = array(
            'name'                     => _x('Banners', 'Post Type General Name', 'simple-banner'),
            'singular_name'            => _x('Banner', 'Post Type Singular Name', 'simple-banner'),
            'menu_name'                => __('Simple Banners', 'simple-banner'),
            'name_admin_bar'           => __('Banner', 'simple-banner'),
            'archives'                 => __('Banner Archives', 'simple-banner'),
            'attributes'               => __('Banner Attributes', 'simple-banner'),
            'parent_item_colon'        => __('Parent Banner:', 'simple-banner'),
            'all_items'                => __('All Banners', 'simple-banner'),
            'add_new_item'             => __('Add New Banner', 'simple-banner'),
            'add_new'                  => __('Add New', 'simple-banner'),
            'new_item'                 => __('New Banner', 'simple-banner'),
            'edit_item'                => __('Edit Banner', 'simple-banner'),
            'update_item'              => __('Update Banner', 'simple-banner'),
            'view_item'                => __('View Banner', 'simple-banner'),
            'view_items'               => __('View Banners', 'simple-banner'),
            'search_items'             => __('Search Banner', 'simple-banner'),
            'not_found'                => __('Not found', 'simple-banner'),
            'not_found_in_trash'       => __('Not found in Trash', 'simple-banner'),
            'item_published'           => __('Banner published.', 'simple-banner'),
            'item_reverted_to_draft'   => __('Banner reverted to draft.', 'simple-banner'),
            'item_scheduled'           => __('Banner scheduled.', 'simple-banner'),
            'item_updated'             => __('Banner updated.', 'simple-banner'),
            'filter_items_list'        => __('Filter banners list', 'simple-banner'),
            'items_list_navigation'    => __('Banners list navigation', 'simple-banner'),
            'items_list'               => __('Banners list', 'simple-banner'),
        );
        
        $args = array(
            'label'                 => __('Banner', 'simple-banner'),
            'description'           => __('Bootstrap 5 Banner Carousel', 'simple-banner'),
            'labels'                => $labels,
            'supports'              => array('title'),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-images-alt2',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'show_in_rest'          => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
        );
        
        register_post_type('simple_banner', $args);
    }
    
    /**
     * Set custom columns for admin list
     */
    public function set_custom_columns($columns) {
        $new_columns = array();
        if (isset($columns['cb'])) {
            $new_columns['cb'] = $columns['cb'];
        }
        if (isset($columns['title'])) {
            $new_columns['title'] = $columns['title'];
        }
        $new_columns['shortcode'] = __('Shortcode', 'simple-banner');
        $new_columns['images'] = __('Images', 'simple-banner');
        if (isset($columns['date'])) {
            $new_columns['date'] = $columns['date'];
        }
        
        return $new_columns;
    }
    
    /**
     * Custom column content
     */
    public function custom_column_content($column, $post_id) {
        $post_id = absint($post_id);

        switch ($column) {
            case 'shortcode':
                $shortcode = sprintf('[simple_banner id="%d"]', $post_id);
                echo '<code>' . esc_html($shortcode) . '</code>';
                echo '<button type="button" class="button button-small simple-banner-copy-shortcode" data-shortcode="' . esc_attr($shortcode) . '" style="margin-left: 10px;">' . esc_html__('Copy', 'simple-banner') . '</button>';
                break;
                
            case 'images':
                $images = get_post_meta($post_id, '_banner_images', true);
                if (!empty($images) && is_array($images)) {
                    $active_count = 0;
                    foreach ($images as $image) {
                        if (isset($image['active']) && $image['active']) {
                            $active_count++;
                        }
                    }
                    echo esc_html($active_count . ' / ' . count($images) . ' ' . __('active', 'simple-banner'));
                } else {
                    echo esc_html__('No images', 'simple-banner');
                }
                break;
        }
    }
}
