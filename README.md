# Simple Banner - WordPress Bootstrap 5 Carousel Plugin

A powerful and user-friendly WordPress plugin for creating beautiful Bootstrap 5 carousel banners with multiple instances, image management, and customizable controls.

## Features

✅ **Multiple Banner Instances** - Create unlimited banner carousels, each with its own unique shortcode  
✅ **Image Upload** - Easy image upload using WordPress media library  
✅ **Image URL Links** - Add clickable links to each banner image  
✅ **Left/Right Controls** - Navigation arrows with customizable colors  
✅ **Pagination Dots** - Swiper-style indicator dots with customizable colors  
✅ **Image Management** - Delete and deactivate images without removing them permanently  
✅ **Drag & Drop Sorting** - Easily reorder images by dragging  
✅ **Bootstrap 5** - Built with the latest Bootstrap framework  
✅ **Responsive Design** - Mobile-friendly and touch-enabled  
✅ **Autoplay Options** - Configure automatic slide transitions  

## Installation

1. Upload the `simple-banner` folder to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'Simple Banners' in the admin menu to create your first banner

## Usage

### Creating a Banner

1. Go to **Simple Banners** → **Add New** in your WordPress admin
2. Enter a title for your banner (for internal reference)
3. Click **Add Image** to upload banner images
4. For each image:
   - Upload or select an image from media library
   - Optionally add a URL link
   - Toggle active/inactive status
   - Drag to reorder
5. Configure banner settings in the right sidebar:
   - Controls color (arrows)
   - Indicators color (dots)
   - Rotation (Enable image rotation)
   - Rotation speed (seconds)
6. Click **Publish**
7. Copy the generated shortcode

### Using the Shortcode

Insert the shortcode into any post, page, or widget:

```
[simple_banner id="123"]
```

Replace `123` with your actual banner ID.

### Managing Images

**Add Images:**
- Click "Add Image" button
- Select from media library or upload new
- Image will be added to the banner

**Reorder Images:**
- Click and drag the handle icon (≡) on the left
- Images will display in the order shown

**Edit Image Link:**
- Enter a URL in the "Link URL" field
- Leave empty if no link needed

**Deactivate/Activate:**
- Click the eye icon button
- Deactivated images won't show in the carousel
- They remain saved and can be reactivated

**Delete Image:**
- Click the trash icon button
- Confirm deletion
- Image is permanently removed from the banner

### Customizing Colors

In the Banner Settings sidebar:

**Controls Color:**
- Sets the color of left/right arrow buttons
- Use the color picker to choose any color

**Indicators Color:**
- Sets the color of pagination dots
- Use the color picker to choose any color

### Rotation Settings

**Rotation:**
- Check the "Enable image rotation" checkbox (enabled by default for new banners)
- Banner will automatically transition between slides

**Rotation speed (seconds):**
- Set the time (in seconds) between transitions (1–60 seconds)
- Default: 5 seconds
- Minimum: 1 second

## File Structure

```
simple-banner/
├── simple-banner.php          # Main plugin file
├── includes/
│   ├── class-banner-post-type.php    # Custom post type registration
│   ├── class-banner-metabox.php      # Admin metabox handler
│   ├── class-banner-shortcode.php    # Shortcode renderer
│   └── class-banner-frontend.php     # Frontend display handler
├── admin/
│   └── class-banner-admin.php        # Admin interface
├── assets/
│   ├── css/
│   │   ├── admin-style.css           # Admin styles
│   │   └── banner-style.css          # Frontend styles
│   └── js/
│       ├── admin-script.js           # Admin JavaScript
│       └── banner-script.js          # Frontend JavaScript
└── README.md
```

## Requirements

- WordPress 6.0 or higher (Tested up to 6.8)
- PHP 7.4 or higher
- Modern web browser with JavaScript enabled

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Features in Detail

### Bootstrap 5 Integration

The plugin uses Bootstrap 5's carousel component, providing:
- Smooth transitions
- Touch/swipe support on mobile devices
- Keyboard navigation (arrow keys)
- Responsive design
- Accessibility features

### Image Management System

- **Drag & Drop Sorting**: Reorder images visually with jQuery UI sortable
- **Active/Inactive Toggle**: Control which images display without deleting
- **Image Preview**: See thumbnails of all images in admin
- **URL Linking**: Make images clickable with custom URLs
- **Media Library Integration**: Full WordPress media library support

### Shortcode System

Each banner instance has a unique shortcode:
```
[simple_banner id="123"]
```

This allows you to:
- Use different banners on different pages
- Insert banners anywhere shortcodes are supported
- Use multiple banners on the same page

## Frequently Asked Questions

**Q: Can I use multiple banners on the same page?**  
A: Yes! Create multiple banner instances and use different shortcodes.

**Q: Can I customize the carousel height?**  
A: The carousel automatically adapts to image sizes. For custom heights, add CSS to your theme.

**Q: Do deactivated images get deleted?**  
A: No, they're just hidden. You can reactivate them anytime.

**Q: Can I add captions to images?**  
A: Currently, the plugin supports images and links. Captions may be added in a future version.

**Q: Is it compatible with page builders?**  
A: Yes! Use the shortcode block in Gutenberg, or shortcode widget in page builders like Elementor, WPBakery, etc.

## Changelog

### Version 1.1.0
- Updated WordPress compatibility headers (`Requires at least: 6.0`, `Tested up to: 6.8`, `Requires PHP: 7.4`)
- Updated textdomain loading to run on `init` for WordPress 6.7+ compatibility
- Updated Bootstrap 5 CDN to 5.3.3 with WordPress 6.3+ `defer` script loading strategy
- Added conditional frontend asset loading (only enqueues Bootstrap and banner assets when a banner is rendered)
- Added unique carousel IDs via `wp_unique_id()` to support multiple instances of the same banner on one page
- Added Media Library alt text support, `decoding="async"`, and lazy loading for secondary slides
- Hardened PHP 8.x compatibility, nonce/input unslashing, and clipboard copy fallback

### Version 1.0.0
- Initial release
- Bootstrap 5 carousel integration
- Multiple banner instances
- Image upload and management
- Drag & drop sorting
- Active/inactive toggle
- Delete functionality
- Customizable control colors
- Customizable indicator colors
- Autoplay options
- Shortcode system

## Support

For support, feature requests, or bug reports, please contact the plugin author.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

- Built with Bootstrap 5
- Uses WordPress Media Library
- jQuery UI Sortable for drag & drop

---

**Developed with ❤️ for WordPress**

