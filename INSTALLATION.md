# Simple Banner - Installation & Setup Guide

## 📦 Installation Methods

### Method 1: Direct Upload (Recommended)
1. Copy the entire `simple-banner` folder to your WordPress installation:
   ```
   /wp-content/plugins/simple-banner/
   ```

2. Navigate to WordPress Admin → Plugins

3. Find "Simple Banner" in the plugin list

4. Click "Activate"

5. You'll see "Simple Banners" appear in the left admin menu

### Method 2: Upload via WordPress Admin
1. Zip the `simple-banner` folder into `simple-banner.zip`

2. Go to WordPress Admin → Plugins → Add New

3. Click "Upload Plugin"

4. Choose `simple-banner.zip`

5. Click "Install Now"

6. Click "Activate Plugin"

### Method 3: FTP Upload
1. Connect to your server via FTP

2. Navigate to `/wp-content/plugins/`

3. Upload the entire `simple-banner` folder

4. Go to WordPress Admin → Plugins

5. Activate "Simple Banner"

## ✅ Verify Installation

After activation, check:
- [ ] "Simple Banners" menu appears in WordPress admin
- [ ] No error messages displayed
- [ ] Can access "Add New" banner page
- [ ] Media uploader works when clicking "Add Image"

## 🚀 Quick Setup (5 Minutes)

### Step 1: Create First Banner (2 min)
```
1. Click "Simple Banners" → "Add New"
2. Enter title: "Test Banner"
3. Click "Add Image"
4. Select 2-3 images
5. Click "Publish"
```

### Step 2: Copy Shortcode (1 min)
```
1. Look at the right sidebar
2. Find the shortcode box
3. Click "Copy Shortcode" button
   Example: [simple_banner id="1"]
```

### Step 3: Add to Page (2 min)
```
1. Edit any page or post
2. Add a "Shortcode" block (Gutenberg)
   Or paste directly in Classic Editor
3. Paste: [simple_banner id="1"]
4. Preview/Publish the page
5. View on frontend
```

## 🔧 Configuration

### Required Configuration
✅ None! Works out of the box with defaults

### Optional Configuration
- Customize control colors (default: white `#ffffff`)
- Customize indicator colors (default: white `#ffffff`)
- Enable/disable image rotation (default: enabled)
- Set rotation speed in seconds (default: 5 seconds, range: 1–60)

## 📋 System Requirements

### Minimum Requirements
- WordPress: 6.0+ (Tested up to 6.8)
- PHP: 7.4+
- MySQL: 5.6+

### Recommended
- WordPress: 6.8+
- PHP: 8.0+
- MySQL: 8.0+

### Server Requirements
- Memory: 64MB minimum (128MB recommended)
- Disk Space: 5MB for plugin files
- JavaScript: Enabled in browser
- Cookies: Enabled for admin

### Browser Compatibility
**Desktop:**
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

**Mobile:**
- iOS Safari 14+
- Chrome Mobile 90+
- Samsung Internet 14+

## 🔌 Dependencies

### Automatically Loaded
✅ Bootstrap 5.3.3 (CSS & JS) - Loaded conditionally from CDN with deferred script strategy
✅ jQuery - WordPress core
✅ jQuery UI Sortable - WordPress core (banner edit screen only)

### No Additional Plugins Required
The plugin is completely standalone!

## 🗂️ File Structure

```
simple-banner/
├── simple-banner.php                 # Main plugin file (entry point)
│
├── includes/                         # Core functionality
│   ├── class-banner-post-type.php   # Custom post type
│   ├── class-banner-metabox.php     # Admin interface
│   ├── class-banner-shortcode.php   # Shortcode handler
│   └── class-banner-frontend.php    # Frontend display
│
├── admin/                            # Admin-specific
│   └── class-banner-admin.php       # Admin helpers
│
├── assets/                           # Static assets
│   ├── css/
│   │   ├── admin-style.css          # Admin styling
│   │   └── banner-style.css         # Frontend styling
│   └── js/
│       ├── admin-script.js          # Admin JavaScript
│       └── banner-script.js         # Frontend JavaScript
│
├── README.md                         # Full documentation
├── QUICK-START.md                    # Quick start guide
├── FEATURES.md                       # Feature details
├── INSTALLATION.md                   # This file
└── .gitignore                        # Git ignore rules
```

## 🔐 Permissions Required

### WordPress Capabilities
The plugin respects WordPress user roles:

**Required for banner management:**
- `edit_posts` - Create/edit banners
- `upload_files` - Upload images
- `publish_posts` - Publish banners

**Administrator:** Full access ✅
**Editor:** Full access ✅
**Author:** Full access ✅
**Contributor:** View only ❌
**Subscriber:** No access ❌

## 🌐 Multisite Compatibility

### Network Installation
1. Upload to `/wp-content/plugins/`
2. Network Activate from Network Admin
3. Each site gets independent banners

### Site-Specific Installation
1. Activate on individual sites
2. Separate banner instances per site

## 🔄 Updating

### Manual Update
1. Deactivate plugin
2. Delete old `simple-banner` folder
3. Upload new version
4. Reactivate plugin
5. All banners preserved

### Data Safety
- Banner data stored in database
- Images stored in media library
- Settings preserved on update
- No data loss during updates

## 🛠️ Troubleshooting Installation

### Issue: Plugin not appearing in list
**Solution:**
- Check folder name is exactly `simple-banner`
- Check folder is in `/wp-content/plugins/`
- Check file permissions (755 for folders, 644 for files)
- Check PHP version meets requirements

### Issue: Activation fails
**Solution:**
- Check PHP error logs
- Verify all files uploaded
- Check memory limit (increase if needed)
- Disable conflicting plugins temporarily

### Issue: White screen after activation
**Solution:**
- Increase PHP memory limit in `wp-config.php`:
  ```php
  define('WP_MEMORY_LIMIT', '256M');
  ```
- Check PHP error logs
- Enable WordPress debug mode

### Issue: Images not uploading
**Solution:**
- Check PHP `upload_max_filesize`
- Check PHP `post_max_size`
- Verify `wp-content/uploads/` is writable
- Check available disk space

### Issue: Styles not loading
**Solution:**
- Clear browser cache
- Clear WordPress cache (if using cache plugin)
- Check if Bootstrap conflicts exist
- Verify CDN is accessible

## 📊 Database Tables

### Tables Created
None! Plugin uses WordPress core tables:
- `wp_posts` - Banner post type
- `wp_postmeta` - Banner settings & images

### Data Stored
- Banner titles and status
- Image IDs and URLs
- Link URLs
- Color settings
- Autoplay settings

## 🔙 Uninstallation

### Deactivation (keeps data)
1. Go to Plugins
2. Click "Deactivate" under Simple Banner
3. All data remains in database

### Complete Removal
1. Deactivate plugin first
2. Click "Delete" under Simple Banner
3. WordPress will remove:
   - Plugin files
   - Banner posts (optional in future version)
   - All settings

### Manual Cleanup
If needed, remove data manually:
```sql
DELETE FROM wp_posts WHERE post_type = 'simple_banner';
DELETE FROM wp_postmeta WHERE post_id IN (
    SELECT ID FROM wp_posts WHERE post_type = 'simple_banner'
);
```

## 📞 Getting Help

### Installation Issues
1. Check this guide first
2. Verify system requirements
3. Check PHP error logs
4. Contact support with:
   - WordPress version
   - PHP version
   - Error messages
   - Steps to reproduce

### Additional Resources
- `README.md` - Full documentation
- `QUICK-START.md` - Usage guide
- `FEATURES.md` - Feature details

## ✨ Post-Installation

### Recommended Next Steps
1. ✅ Create a test banner
2. ✅ Upload 2-3 test images
3. ✅ Customize colors
4. ✅ Test on frontend
5. ✅ Try mobile view
6. ✅ Test touch gestures
7. ✅ Create production banners

### Best Practices
- Use optimized images (< 500KB)
- Keep consistent image dimensions
- Test across different browsers
- Backup before major updates
- Use staging site for testing

## 🎉 Congratulations!

Your Simple Banner plugin is now installed and ready to use!

**Next:** Open `QUICK-START.md` to create your first banner.

---

**Need help? Check README.md for detailed documentation.**

