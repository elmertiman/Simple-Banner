/**
 * Simple Banner - Frontend Scripts
 */

(function($) {
    'use strict';
    
    var SimpleBanner = {
        
        /**
         * Initialize
         */
        init: function() {
            this.initCarousels();
        },
        
        /**
         * Get or create Bootstrap 5 Carousel instance safely
         */
        getCarouselInstance: function(element) {
            if (typeof bootstrap === 'undefined' || !bootstrap.Carousel || !element) {
                return null;
            }
            if (typeof bootstrap.Carousel.getOrCreateInstance === 'function') {
                return bootstrap.Carousel.getOrCreateInstance(element);
            }
            return bootstrap.Carousel.getInstance(element) || new bootstrap.Carousel(element);
        },
        
        /**
         * Initialize all carousels
         */
        initCarousels: function() {
            $('.simple-banner-carousel').each(function() {
                var carousel = $(this);
                
                if (carousel.data('sb-initialized')) {
                    return;
                }
                carousel.data('sb-initialized', true);
                
                // Add touch swipe support for mobile
                SimpleBanner.addTouchSupport(carousel);
                
                // Add keyboard navigation
                SimpleBanner.addKeyboardNavigation(carousel);
            });
        },
        
        /**
         * Add touch swipe support
         */
        addTouchSupport: function(carousel) {
            var touchStartX = 0;
            var touchEndX = 0;
            var element = carousel[0];
            
            if (!element) {
                return;
            }
            
            element.addEventListener('touchstart', function(e) {
                if (e.changedTouches && e.changedTouches.length > 0) {
                    touchStartX = e.changedTouches[0].screenX;
                }
            }, { passive: true });
            
            element.addEventListener('touchend', function(e) {
                if (e.changedTouches && e.changedTouches.length > 0) {
                    touchEndX = e.changedTouches[0].screenX;
                    SimpleBanner.handleSwipe(carousel, touchStartX, touchEndX);
                }
            }, { passive: true });
        },
        
        /**
         * Handle swipe gesture
         */
        handleSwipe: function(carousel, startX, endX) {
            var swipeThreshold = 50;
            var diff = startX - endX;
            
            if (Math.abs(diff) > swipeThreshold) {
                var carouselInstance = SimpleBanner.getCarouselInstance(carousel[0]);
                
                if (!carouselInstance) {
                    return;
                }
                
                if (diff > 0) {
                    // Swipe left - next slide
                    carouselInstance.next();
                } else {
                    // Swipe right - previous slide
                    carouselInstance.prev();
                }
            }
        },
        
        /**
         * Add keyboard navigation
         */
        addKeyboardNavigation: function(carousel) {
            carousel.on('keydown', function(e) {
                var carouselInstance = SimpleBanner.getCarouselInstance(this);
                
                if (!carouselInstance) {
                    return;
                }
                
                var key = e.key || e.keyCode;
                if (key === 'ArrowLeft' || key === 37) {
                    e.preventDefault();
                    carouselInstance.prev();
                } else if (key === 'ArrowRight' || key === 39) {
                    e.preventDefault();
                    carouselInstance.next();
                }
            });
            
            // Make carousel focusable
            carousel.attr('tabindex', '0');
        }
    };
    
    // Initialize when document is ready
    $(function() {
        SimpleBanner.init();
    });
    
    // Reinitialize after AJAX or dynamic content load
    $(document).on('simple_banner_loaded', function() {
        SimpleBanner.init();
    });
    
})(jQuery);
