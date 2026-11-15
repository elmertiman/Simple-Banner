<?php
/**
 * Banner Metabox Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Banner_Metabox {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_simple_banner', array($this, 'save_meta_boxes'), 10, 2);
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'simple_banner_images',
            __('Banner Images', 'simple-banner'),
            array($this, 'render_images_metabox'),
            'simple_banner',
            'normal',
            'high'
        );
        
        add_meta_box(
            'simple_banner_settings',
            __('Banner Settings', 'simple-banner'),
            array($this, 'render_settings_metabox'),
            'simple_banner',
            'side',
            'default'
        );
    }
    
    /**
     * Render images metabox
     */
    public function render_images_metabox($post) {
        wp_nonce_field('simple_banner_metabox', 'simple_banner_metabox_nonce');
        
        $images = get_post_meta($post->ID, '_banner_images', true);
        if (!is_array($images)) {
            $images = array();
        }
        ?>
        <div class="simple-banner-images-container">
            <div class="banner-images-list" id="banner-images-list">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $index => $image): ?>
                        <?php $this->render_image_item($index, $image); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="add-banner-image-wrap">
                <button type="button" class="button button-primary button-large" id="add-banner-image">
                    <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
                    <?php esc_html_e('Add Image', 'simple-banner'); ?>
                </button>
            </div>
        </div>
        
        <script type="text/template" id="banner-image-template">
            <?php $this->render_image_item('{{INDEX}}', array(
                'image_id' => '{{IMAGE_ID}}',
                'image_url' => '{{IMAGE_URL}}',
                'link_url' => '',
                'active' => true
            )); ?>
        </script>
        <?php
    }
    
    /**
     * Render single image item
     */
    private function render_image_item($index, $image) {
        $image_id = isset($image['image_id']) ? $image['image_id'] : '';
        $image_url = isset($image['image_url']) ? $image['image_url'] : '';
        $link_url = isset($image['link_url']) ? $image['link_url'] : '';
        $active = isset($image['active']) ? (bool) $image['active'] : true;
        
        $active_class = $active ? 'active' : 'inactive';
        $active_text = $active ? __('Active', 'simple-banner') : __('Inactive', 'simple-banner');
        $is_template = ('{{IMAGE_URL}}' === $image_url);
        $escaped_image_url = $is_template ? '{{IMAGE_URL}}' : esc_url($image_url);
        ?>
        <div class="banner-image-item <?php echo esc_attr($active_class); ?>" data-index="<?php echo esc_attr($index); ?>">
            <div class="banner-image-handle">
                <span class="dashicons dashicons-move"></span>
            </div>
            
            <div class="banner-image-preview">
                <?php if ($image_url): ?>
                    <img src="<?php echo $escaped_image_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" alt="">
                <?php else: ?>
                    <div class="no-image"><?php esc_html_e('No image', 'simple-banner'); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="banner-image-details">
                <input type="hidden" name="banner_images[<?php echo esc_attr($index); ?>][image_id]" value="<?php echo esc_attr($image_id); ?>" class="banner-image-id">
                <input type="hidden" name="banner_images[<?php echo esc_attr($index); ?>][image_url]" value="<?php echo $escaped_image_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="banner-image-url">
                <input type="hidden" name="banner_images[<?php echo esc_attr($index); ?>][active]" value="<?php echo $active ? '1' : '0'; ?>" class="banner-image-active">
                
                <div class="banner-image-field">
                    <label><?php esc_html_e('Link URL (optional):', 'simple-banner'); ?></label>
                    <input type="url" name="banner_images[<?php echo esc_attr($index); ?>][link_url]" value="<?php echo esc_url($link_url); ?>" placeholder="https://" class="widefat">
                </div>
            </div>
            
            <div class="banner-image-actions">
                <button type="button" class="button button-small change-image" title="<?php esc_attr_e('Change Image', 'simple-banner'); ?>">
                    <span class="dashicons dashicons-format-image"></span>
                </button>
                
                <button type="button" class="button button-small toggle-image" title="<?php echo esc_attr($active ? __('Deactivate', 'simple-banner') : __('Activate', 'simple-banner')); ?>">
                    <span class="dashicons dashicons-<?php echo $active ? 'hidden' : 'visibility'; ?>"></span>
                    <span class="status-text"><?php echo esc_html($active_text); ?></span>
                </button>
                
                <button type="button" class="button button-small delete-image" title="<?php esc_attr_e('Delete Image', 'simple-banner'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render settings metabox
     */
    public function render_settings_metabox($post) {
        $controls_color = get_post_meta($post->ID, '_banner_controls_color', true);
        $indicators_color = get_post_meta($post->ID, '_banner_indicators_color', true);
        $autoplay = get_post_meta($post->ID, '_banner_autoplay', true);
        $interval = get_post_meta($post->ID, '_banner_interval', true);
        
        // Set defaults (rotation on by default, default speed 5 seconds)
        if (empty($controls_color)) {
            $controls_color = '#ffffff';
        }
        if (empty($indicators_color)) {
            $indicators_color = '#ffffff';
        }
        if (empty($interval)) {
            $interval = 5000;
        }
        // Default rotation (autoplay) to on for new banners
        $rotation_checked = ($autoplay === '' || $autoplay === '1');
        $interval_seconds = (int) round(absint($interval) / 1000);
        if ($interval_seconds < 1) {
            $interval_seconds = 5;
        }
        $shortcode = sprintf('[simple_banner id="%d"]', absint($post->ID));
        ?>
        <div class="simple-banner-settings">
            <p>
                <label><strong><?php esc_html_e('Controls Color:', 'simple-banner'); ?></strong></label><br>
                <input type="color" name="banner_controls_color" value="<?php echo esc_attr($controls_color); ?>" style="width: 100%;">
            </p>
            
            <p>
                <label><strong><?php esc_html_e('Indicators Color:', 'simple-banner'); ?></strong></label><br>
                <input type="color" name="banner_indicators_color" value="<?php echo esc_attr($indicators_color); ?>" style="width: 100%;">
            </p>
            
            <p>
                <label><strong><?php esc_html_e('Rotation:', 'simple-banner'); ?></strong></label><br>
                <label>
                    <input type="checkbox" name="banner_autoplay" value="1" <?php checked($rotation_checked); ?>>
                    <?php esc_html_e('Enable image rotation', 'simple-banner'); ?>
                </label>
            </p>
            
            <p>
                <label><strong><?php esc_html_e('Rotation speed (seconds):', 'simple-banner'); ?></strong></label><br>
                <input type="number" name="banner_interval_seconds" value="<?php echo esc_attr($interval_seconds); ?>" min="1" max="60" step="1" style="width: 100%;">
            </p>
            
            <?php do_action('simple_banner_after_settings', $post->ID); ?>
            
            <hr>
            
            <p>
                <strong><?php esc_html_e('Shortcode:', 'simple-banner'); ?></strong><br>
                <code><?php echo esc_html($shortcode); ?></code>
                <button type="button" class="button button-small simple-banner-copy-shortcode" data-shortcode="<?php echo esc_attr($shortcode); ?>" style="margin-top: 5px;">
                    <?php esc_html_e('Copy Shortcode', 'simple-banner'); ?>
                </button>
            </p>
        </div>
        <?php
    }
    
    /**
     * Save meta boxes
     */
    public function save_meta_boxes($post_id, $post) {
        // Check nonce
        if (
            !isset($_POST['simple_banner_metabox_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['simple_banner_metabox_nonce'])), 'simple_banner_metabox')
        ) {
            return;
        }
        
        // Check autosave or revision
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post_id)) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save images
        if (isset($_POST['banner_images']) && is_array($_POST['banner_images'])) {
            $raw_images = wp_unslash($_POST['banner_images']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $images = array();
            foreach ($raw_images as $image_data) {
                if (!is_array($image_data)) {
                    continue;
                }
                $images[] = array(
                    'image_id'  => isset($image_data['image_id']) ? sanitize_text_field($image_data['image_id']) : '',
                    'image_url' => isset($image_data['image_url']) ? esc_url_raw($image_data['image_url']) : '',
                    'link_url'  => isset($image_data['link_url']) ? esc_url_raw($image_data['link_url']) : '',
                    'active'    => isset($image_data['active']) && '1' === (string) $image_data['active'],
                );
            }
            update_post_meta($post_id, '_banner_images', $images);
        } else {
            update_post_meta($post_id, '_banner_images', array());
        }
        
        // Save settings
        $controls_color = isset($_POST['banner_controls_color'])
            ? sanitize_hex_color(wp_unslash($_POST['banner_controls_color']))
            : '#ffffff';
        if (empty($controls_color)) {
            $controls_color = '#ffffff';
        }
        update_post_meta($post_id, '_banner_controls_color', $controls_color);
        
        $indicators_color = isset($_POST['banner_indicators_color'])
            ? sanitize_hex_color(wp_unslash($_POST['banner_indicators_color']))
            : '#ffffff';
        if (empty($indicators_color)) {
            $indicators_color = '#ffffff';
        }
        update_post_meta($post_id, '_banner_indicators_color', $indicators_color);
        
        $autoplay = isset($_POST['banner_autoplay']) ? '1' : '0';
        update_post_meta($post_id, '_banner_autoplay', $autoplay);
        
        // Rotation speed: stored in ms; form uses seconds (default 5 seconds)
        $interval_seconds = isset($_POST['banner_interval_seconds'])
            ? absint(wp_unslash($_POST['banner_interval_seconds']))
            : 5;
        if ($interval_seconds < 1) {
            $interval_seconds = 5;
        }
        $interval = $interval_seconds * 1000;
        update_post_meta($post_id, '_banner_interval', $interval);
    }
}
