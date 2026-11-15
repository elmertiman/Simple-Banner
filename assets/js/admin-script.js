/**
 * Simple Banner - Admin Scripts
 */

(function($) {
    'use strict';
    
    var i18n = window.simpleBannerAdmin || {};
    
    var SimpleBannerAdmin = {
        
        imageIndex: 0,
        mediaFrame: null,
        currentImageItem: null,
        
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initSortable();
            this.setImageIndex();
        },
        
        /**
         * Bind events
         */
        bindEvents: function() {
            var self = this;
            
            // Add image button
            $('#add-banner-image').on('click', function(e) {
                e.preventDefault();
                self.currentImageItem = null;
                self.openMediaUploader();
            });
            
            // Change image button
            $(document).on('click', '.change-image', function(e) {
                e.preventDefault();
                self.currentImageItem = $(this).closest('.banner-image-item');
                self.openMediaUploader();
            });
            
            // Toggle active/inactive
            $(document).on('click', '.toggle-image', function(e) {
                e.preventDefault();
                self.toggleImage($(this));
            });
            
            // Delete image
            $(document).on('click', '.delete-image', function(e) {
                e.preventDefault();
                self.deleteImage($(this));
            });
            
            // Copy shortcode button
            $(document).on('click', '.simple-banner-copy-shortcode', function(e) {
                e.preventDefault();
                var shortcode = $(this).data('shortcode');
                if (shortcode) {
                    self.copyToClipboard(shortcode);
                }
            });
        },
        
        /**
         * Copy text to clipboard with fallback for non-HTTPS local environments
         */
        copyToClipboard: function(text) {
            var copiedMessage = i18n.copied_text || 'Shortcode copied!';
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    window.alert(copiedMessage);
                });
                return;
            }
            var tempInput = $('<textarea>');
            $('body').append(tempInput);
            tempInput.val(text).trigger('select');
            try {
                document.execCommand('copy');
                window.alert(copiedMessage);
            } catch (err) {
                // Ignore copy failure
            }
            tempInput.remove();
        },
        
        /**
         * Initialize sortable
         */
        initSortable: function() {
            var $list = $('#banner-images-list');
            if (!$list.length || typeof $.fn.sortable !== 'function') {
                return;
            }
            $list.sortable({
                handle: '.banner-image-handle',
                placeholder: 'banner-image-placeholder',
                cursor: 'move',
                opacity: 0.8,
                update: function() {
                    SimpleBannerAdmin.updateIndices();
                }
            });
        },
        
        /**
         * Set initial image index
         */
        setImageIndex: function() {
            var maxIndex = -1;
            $('.banner-image-item').each(function() {
                var index = parseInt($(this).attr('data-index'), 10);
                if (!isNaN(index) && index > maxIndex) {
                    maxIndex = index;
                }
            });
            this.imageIndex = maxIndex + 1;
        },
        
        /**
         * Open media uploader
         */
        openMediaUploader: function() {
            var self = this;
            
            if (typeof wp === 'undefined' || !wp.media) {
                return;
            }
            
            // Create media frame if it doesn't exist
            if (this.mediaFrame) {
                this.mediaFrame.open();
                return;
            }
            
            // Create a new media frame
            this.mediaFrame = wp.media({
                title: i18n.select_image || 'Select Banner Image',
                button: {
                    text: i18n.use_image || 'Use this image'
                },
                multiple: false
            });
            
            // When an image is selected
            this.mediaFrame.on('select', function() {
                var attachment = self.mediaFrame.state().get('selection').first().toJSON();
                
                if (self.currentImageItem) {
                    // Update existing image
                    self.updateImageItem(self.currentImageItem, attachment);
                } else {
                    // Add new image
                    self.addImageItem(attachment);
                }
            });
            
            // Open the media frame
            this.mediaFrame.open();
        },
        
        /**
         * Add new image item
         */
        addImageItem: function(attachment) {
            var template = $('#banner-image-template').html();
            if (!template) {
                return;
            }
            /* Use full-size image to preserve resolution */
            var imageUrl = attachment.url;
            
            // Replace placeholders
            template = template.replace(/{{INDEX}}/g, this.imageIndex);
            template = template.replace(/{{IMAGE_ID}}/g, attachment.id);
            template = template.replace(/{{IMAGE_URL}}/g, imageUrl);
            
            // Add to list
            $('#banner-images-list').append(template);
            
            this.imageIndex++;
            this.updateIndices();
        },
        
        /**
         * Update existing image item
         */
        updateImageItem: function(item, attachment) {
            /* Use full-size image to preserve resolution */
            var imageUrl = attachment.url;
            
            item.find('.banner-image-id').val(attachment.id);
            item.find('.banner-image-url').val(imageUrl);
            item.find('.banner-image-preview img').attr('src', imageUrl);
            
            // Show image if no-image placeholder exists
            if (item.find('.no-image').length) {
                item.find('.banner-image-preview').empty().append(
                    $('<img>', { src: imageUrl, alt: '' })
                );
            }
        },
        
        /**
         * Toggle image active/inactive
         */
        toggleImage: function(button) {
            var item = button.closest('.banner-image-item');
            var activeInput = item.find('.banner-image-active');
            var isActive = activeInput.val() === '1';
            
            if (isActive) {
                // Deactivate
                item.removeClass('active').addClass('inactive');
                activeInput.val('0');
                button.find('.dashicons').removeClass('dashicons-hidden').addClass('dashicons-visibility');
                button.find('.status-text').text(i18n.inactive_text || 'Inactive');
                button.attr('title', i18n.activate_text || 'Activate');
            } else {
                // Activate
                item.removeClass('inactive').addClass('active');
                activeInput.val('1');
                button.find('.dashicons').removeClass('dashicons-visibility').addClass('dashicons-hidden');
                button.find('.status-text').text(i18n.active_text || 'Active');
                button.attr('title', i18n.deactivate_text || 'Deactivate');
            }
        },
        
        /**
         * Delete image
         */
        deleteImage: function(button) {
            var confirmMsg = i18n.confirm_delete || 'Are you sure you want to delete this image?';
            if (!window.confirm(confirmMsg)) {
                return;
            }
            
            var item = button.closest('.banner-image-item');
            item.fadeOut(300, function() {
                $(this).remove();
                SimpleBannerAdmin.updateIndices();
            });
        },
        
        /**
         * Update all image indices
         */
        updateIndices: function() {
            $('#banner-images-list .banner-image-item').each(function(index) {
                var item = $(this);
                
                // Update data attribute and jQuery data cache
                item.attr('data-index', index).data('index', index);
                
                // Update all input names
                item.find('input[name^="banner_images"]').each(function() {
                    var name = $(this).attr('name');
                    name = name.replace(/banner_images\[[^\]]+\]/, 'banner_images[' + index + ']');
                    $(this).attr('name', name);
                });
            });
        }
    };
    
    // Initialize when document is ready
    $(function() {
        SimpleBannerAdmin.init();
    });
    
})(jQuery);
