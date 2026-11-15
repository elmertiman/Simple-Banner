<?php
/**
 * Banner Shortcode Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class Simple_Banner_Shortcode {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('simple_banner', array($this, 'render_shortcode'));
    }
    
    /**
     * Render shortcode
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0
        ), $atts, 'simple_banner');
        
        $banner_id = absint($atts['id']);
        
        if (!$banner_id) {
            return '<p>' . esc_html__('Please provide a valid banner ID.', 'simple-banner') . '</p>';
        }
        
        // Check if banner exists and is published
        $banner = get_post($banner_id);
        if (!$banner || 'simple_banner' !== $banner->post_type || 'publish' !== $banner->post_status) {
            return '<p>' . esc_html__('Banner not found.', 'simple-banner') . '</p>';
        }
        
        // Ensure frontend styles and scripts are enqueued whenever shortcode renders
        wp_enqueue_style('bootstrap');
        wp_enqueue_script('bootstrap');
        wp_enqueue_style('simple-banner-style');
        wp_enqueue_script('simple-banner-script');
        
        // Get banner data
        $images = get_post_meta($banner_id, '_banner_images', true);
        $controls_color = get_post_meta($banner_id, '_banner_controls_color', true);
        $indicators_color = get_post_meta($banner_id, '_banner_indicators_color', true);
        $autoplay = get_post_meta($banner_id, '_banner_autoplay', true);
        $interval = get_post_meta($banner_id, '_banner_interval', true);
        
        // Set defaults (rotation on, 5 second speed)
        if (empty($controls_color)) {
            $controls_color = '#ffffff';
        }
        if (empty($indicators_color)) {
            $indicators_color = '#ffffff';
        }
        if (empty($interval)) {
            $interval = 5000;
        }
        if ($autoplay === '') {
            $autoplay = '1';
        }
        
        // Filter active images only
        $active_images = array();
        if (is_array($images)) {
            foreach ($images as $image) {
                if (isset($image['active']) && $image['active'] && !empty($image['image_url'])) {
                    $active_images[] = $image;
                }
            }
        }
        
        if (empty($active_images)) {
            return '<p>' . esc_html__('No active images in this banner.', 'simple-banner') . '</p>';
        }
        
        // Generate unique ID for this carousel instance (supports multiple same-ID shortcodes on a page)
        $carousel_id = function_exists('wp_unique_id')
            ? wp_unique_id('simple-banner-' . $banner_id . '-')
            : 'simple-banner-' . $banner_id;
        
        // Start output buffering
        ob_start();
        ?>
        <div id="<?php echo esc_attr($carousel_id); ?>" class="carousel slide simple-banner-carousel" data-bs-ride="<?php echo $autoplay === '1' ? 'carousel' : 'false'; ?>" data-bs-interval="<?php echo esc_attr($interval); ?>">
            <!-- Indicators -->
            <div class="carousel-indicators">
                <?php foreach ($active_images as $index => $image): ?>
                    <button type="button" 
                            data-bs-target="#<?php echo esc_attr($carousel_id); ?>" 
                            data-bs-slide-to="<?php echo esc_attr($index); ?>" 
                            <?php echo 0 === $index ? 'class="active" aria-current="true"' : ''; ?>
                            aria-label="<?php echo esc_attr(sprintf(__('Slide %d', 'simple-banner'), $index + 1)); ?>">
                    </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Slides -->
            <div class="carousel-inner">
                <?php foreach ($active_images as $index => $image):
                    $image_id = !empty($image['image_id']) ? absint($image['image_id']) : 0;
                    $alt_text = $image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : '';
                    if (empty($alt_text)) {
                        $alt_text = sprintf(__('Slide %d', 'simple-banner'), $index + 1);
                    }
                    $loading_attr = $index === 0 ? 'eager' : 'lazy';
                ?>
                    <div class="carousel-item <?php echo 0 === $index ? 'active' : ''; ?>">
                        <?php if (!empty($image['link_url'])): ?>
                            <a href="<?php echo esc_url($image['link_url']); ?>">
                                <img src="<?php echo esc_url($image['image_url']); ?>" class="d-block w-100" alt="<?php echo esc_attr($alt_text); ?>" decoding="async" loading="<?php echo esc_attr($loading_attr); ?>">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo esc_url($image['image_url']); ?>" class="d-block w-100" alt="<?php echo esc_attr($alt_text); ?>" decoding="async" loading="<?php echo esc_attr($loading_attr); ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Controls: thick custom arrow icons -->
            <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo esc_attr($carousel_id); ?>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon sb-arrow-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                </span>
                <span class="visually-hidden"><?php esc_html_e('Previous', 'simple-banner'); ?></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#<?php echo esc_attr($carousel_id); ?>" data-bs-slide="next">
                <span class="carousel-control-next-icon sb-arrow-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </span>
                <span class="visually-hidden"><?php esc_html_e('Next', 'simple-banner'); ?></span>
            </button>
        </div>
        
        <style>
            #<?php echo esc_attr($carousel_id); ?> .sb-arrow-icon svg {
                color: <?php echo esc_attr($controls_color); ?>;
            }
            
            #<?php echo esc_attr($carousel_id); ?> .carousel-indicators button {
                background-color: <?php echo esc_attr($indicators_color); ?>;
            }
        </style>
        <?php
        
        $html = ob_get_clean();
        return apply_filters('simple_banner_html', $html, $banner_id);
    }
}
