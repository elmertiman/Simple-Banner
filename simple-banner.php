<?php

/**
 * Plugin Name: Simple Banner
 * Plugin URI: https://wp-simple-components.com/simple-banner
 * Description: A Bootstrap 5 carousel banner plugin with multiple instances, image management, and customizable controls
 * Version: 1.1.0
 * Requires at least: 6.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * Author: Your Name
 * Author URI: https://wp-simple-components.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: simple-banner
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SIMPLE_BANNER_VERSION', '1.1.0');
define('SIMPLE_BANNER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SIMPLE_BANNER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SIMPLE_BANNER_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Simple Banner Class
 */
class Simple_Banner
{

    /**
     * Instance of this class
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files
     */
    private function includes()
    {
        require_once SIMPLE_BANNER_PLUGIN_DIR . 'includes/class-banner-post-type.php';
        require_once SIMPLE_BANNER_PLUGIN_DIR . 'includes/class-banner-metabox.php';
        require_once SIMPLE_BANNER_PLUGIN_DIR . 'includes/class-banner-shortcode.php';
        require_once SIMPLE_BANNER_PLUGIN_DIR . 'includes/class-banner-frontend.php';
        require_once SIMPLE_BANNER_PLUGIN_DIR . 'admin/class-banner-admin.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks()
    {
        add_action('plugins_loaded', array($this, 'init'));
        add_action('init', array($this, 'load_textdomain'), 5);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    /**
     * Load plugin textdomain on init (WordPress 6.7+ compatibility)
     */
    public function load_textdomain()
    {
        load_plugin_textdomain(
            'simple-banner',
            false,
            dirname(SIMPLE_BANNER_PLUGIN_BASENAME) . '/languages'
        );
    }

    /**
     * Initialize plugin
     */
    public function init()
    {
        // Initialize components
        Simple_Banner_Post_Type::get_instance();
        Simple_Banner_Metabox::get_instance();
        Simple_Banner_Shortcode::get_instance();
        Simple_Banner_Frontend::get_instance();
        Simple_Banner_Admin::get_instance();
    }

    /**
     * Register and enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts()
    {
        // Script arguments compatible with WordPress 6.3+ strategy API
        $script_args = version_compare(get_bloginfo('version'), '6.3', '>=')
            ? array('in_footer' => true, 'strategy' => 'defer')
            : true;

        // Bootstrap 5 CSS
        wp_register_style(
            'bootstrap',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
            array(),
            '5.3.3'
        );

        // Bootstrap 5 JS
        wp_register_script(
            'bootstrap',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
            array(),
            '5.3.3',
            $script_args
        );

        // Custom banner styles
        wp_register_style(
            'simple-banner-style',
            SIMPLE_BANNER_PLUGIN_URL . 'assets/css/banner-style.css',
            array('bootstrap'),
            SIMPLE_BANNER_VERSION
        );

        // Custom banner script
        wp_register_script(
            'simple-banner-script',
            SIMPLE_BANNER_PLUGIN_URL . 'assets/js/banner-script.js',
            array('jquery', 'bootstrap'),
            SIMPLE_BANNER_VERSION,
            $script_args
        );

        // Enqueue early if current post content contains the shortcode
        $post = get_post();
        if (
            ($post instanceof WP_Post && has_shortcode($post->post_content, 'simple_banner')) ||
            apply_filters('simple_banner_always_enqueue_assets', false)
        ) {
            wp_enqueue_style('bootstrap');
            wp_enqueue_script('bootstrap');
            wp_enqueue_style('simple-banner-style');
            wp_enqueue_script('simple-banner-script');
        }
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook)
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        $current_post_type = $screen ? $screen->post_type : '';

        if (empty($current_post_type)) {
            global $post_type;
            $current_post_type = $post_type;
        }

        if ('simple_banner' !== $current_post_type) {
            return;
        }

        // Media uploader & sortable only needed on post edit screens
        if (in_array($hook, array('post.php', 'post-new.php'), true)) {
            wp_enqueue_media();
            wp_enqueue_script('jquery-ui-sortable');
        }

        // Admin styles
        wp_enqueue_style(
            'simple-banner-admin-style',
            SIMPLE_BANNER_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            SIMPLE_BANNER_VERSION
        );

        // Admin scripts
        $script_deps = in_array($hook, array('post.php', 'post-new.php'), true)
            ? array('jquery', 'jquery-ui-sortable')
            : array('jquery');

        wp_enqueue_script(
            'simple-banner-admin-script',
            SIMPLE_BANNER_PLUGIN_URL . 'assets/js/admin-script.js',
            $script_deps,
            SIMPLE_BANNER_VERSION,
            true
        );

        // Localize script
        wp_localize_script('simple-banner-admin-script', 'simpleBannerAdmin', array(
            'ajax_url'        => admin_url('admin-ajax.php'),
            'nonce'           => wp_create_nonce('simple_banner_nonce'),
            'confirm_delete'  => __('Are you sure you want to delete this image?', 'simple-banner'),
            'select_image'    => __('Select Banner Image', 'simple-banner'),
            'use_image'       => __('Use this image', 'simple-banner'),
            'active_text'     => __('Active', 'simple-banner'),
            'inactive_text'   => __('Inactive', 'simple-banner'),
            'activate_text'   => __('Activate', 'simple-banner'),
            'deactivate_text' => __('Deactivate', 'simple-banner'),
            'copied_text'     => __('Shortcode copied!', 'simple-banner'),
        ));
    }

    /**
     * Plugin activation
     */
    public function activate()
    {
        Simple_Banner_Post_Type::get_instance()->register_post_type();
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate()
    {
        flush_rewrite_rules();
    }
}

/**
 * Initialize the plugin
 */
function simple_banner_init()
{
    return Simple_Banner::get_instance();
}

// Start the plugin
simple_banner_init();
