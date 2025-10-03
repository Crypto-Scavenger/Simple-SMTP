# Simple SMTP

Lightweight WordPress plugin that redirects all WordPress emails through your SMTP server instead of using the default PHP mail() function. This ensures better email deliverability and allows you to use professional email services like Gmail, Outlook, or dedicated SMTP providers.

## Description

Simple SMTP provides a simple and secure way to configure WordPress to send emails through an SMTP server. The plugin follows WordPress coding standards and best practices, with a focus on security, performance, and ease of use.

## Features

- **Easy Configuration**: Simple settings page accessible from Tools menu
- **Enable/Disable Toggle**: Switch between SMTP and PHP mail() function
- **SMTP Authentication**: Support for username/password authentication
- **Multiple Encryption Types**: None, SSL, and TLS encryption options
- **Test Email Functionality**: Send test emails to verify your configuration
- **Custom From Address**: Set your own "From" email and name
- **Secure Storage**: All settings stored in custom database table
- **Clean Uninstall**: Optional automatic cleanup of all plugin data
- **No External Dependencies**: All functionality built into the plugin

## Installation

1. Upload the `simple-smtp` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to **Tools → Simple SMTP** to configure settings

## Configuration

### Accessing Settings

Go to **Tools → Simple SMTP** in your WordPress admin dashboard.

### SMTP Settings

1. **Enable SMTP**: Check this box to enable SMTP email delivery (uncheck to use default PHP mail())
2. **SMTP Host**: Enter your SMTP server hostname (e.g., smtp.gmail.com)
3. **SMTP Port**: Set the appropriate port (587 for TLS, 465 for SSL, 25 for none)
4. **Encryption**: Choose None, SSL, or TLS based on your server requirements
5. **SMTP Authentication**: Enable if your server requires authentication
6. **Username**: Your SMTP username (usually your email address)
7. **Password**: Your SMTP password or app-specific password
8. **From Email**: The email address that emails will appear to come from
9. **From Name**: The name that will appear as the sender

### Common SMTP Configurations

**Gmail:**
- Host: smtp.gmail.com
- Port: 587
- Encryption: TLS
- Username: your-email@gmail.com
- Password: App-specific password (not your regular Gmail password)

**Outlook/Office 365:**
- Host: smtp-mail.outlook.com or smtp.office365.com
- Port: 587
- Encryption: TLS
- Username: your-email@outlook.com
- Password: Your account password

**Yahoo:**
- Host: smtp.mail.yahoo.com
- Port: 465 or 587
- Encryption: SSL (465) or TLS (587)
- Username: your-email@yahoo.com
- Password: App-specific password

### Testing Configuration

1. After saving your settings, scroll down to the "Send Test Email" section
2. Enter the email address where you want to receive the test
3. Click "Send Test Email"
4. Check your inbox for the test message

If you receive the test email, your configuration is working correctly. If not, review your settings and ensure they match your SMTP provider's requirements.

## File Structure

```
simple-smtp/
├── simple-smtp.php            # Main plugin file, initialization
├── README.md                  # This file
├── uninstall.php              # Cleanup on plugin deletion
├── index.php                  # Security stub
├── includes/
│   ├── class-database.php     # Database operations, settings storage
│   ├── class-core.php         # Core functionality, SMTP configuration
│   ├── class-admin.php        # Admin interface, settings page
│   └── index.php              # Security stub
└── assets/
    ├── admin.css              # Admin page styling
    ├── admin.js               # Admin page JavaScript
    └── index.php              # Security stub
```

### File Descriptions

**simple-smtp.php**
- Plugin header information
- Constant definitions
- Class includes and initialization
- Activation hook

**includes/class-database.php**
- Custom database table creation
- Settings CRUD operations
- Settings caching for performance
- Lazy loading implementation

**includes/class-core.php**
- PHPMailer configuration via phpmailer_init hook
- SMTP server configuration
- From email and name filters
- Test email functionality

**includes/class-admin.php**
- Admin menu registration (Tools submenu)
- Settings page rendering
- Form handling and validation
- Test email interface

**assets/admin.css**
- Clean, minimal styling for settings page
- Responsive design
- WordPress admin theme consistency

**assets/admin.js**
- Show/hide authentication fields based on checkbox
- jQuery-based DOM manipulation

**uninstall.php**
- Checks cleanup preference
- Drops custom database table if enabled
- Clears WordPress caches

## Technical Details

### WordPress APIs Used

- **Plugin API**: Action and filter hooks for WordPress integration
- **Database API**: All queries use `$wpdb->prepare()` with placeholders
- **HTTP API**: Email sending via `wp_mail()` and PHPMailer
- **Admin API**: Settings page via `add_submenu_page()`

### Security Implementation

**SQL Injection Prevention:**
- All database queries use `$wpdb->prepare()` with `%i` for table names
- Compatible with WordPress 6.2+ identifier placeholder syntax
- Zero direct SQL string concatenation

**XSS Prevention:**
- All output escaped with appropriate context functions
- `esc_html()` for text content
- `esc_attr()` for HTML attributes
- `esc_url()` for URLs

**CSRF Protection:**
- WordPress nonces on all forms
- Separate nonces for settings and test email
- Nonce verification before processing

**Input Validation:**
- All POST data sanitized with appropriate functions
- Email validation with `sanitize_email()` and `is_email()`
- Port validation with `absint()`
- Encryption type whitelist validation

**Capability Checks:**
- `manage_options` required for all admin operations
- Double verification in both render and save methods

**Password Security:**
- Passwords stored as-is (not hashed) as they're needed for SMTP authentication
- Autocomplete disabled on password field
- Password field type prevents shoulder surfing

### Performance Optimizations

**Lazy Loading:**
- Settings loaded only when needed
- Prevents unnecessary database queries on every page load
- Settings cached in memory during request

**Conditional Asset Loading:**
- Admin CSS/JS only loads on plugin settings page
- No frontend assets (plugin only affects email sending)

**Database Optimization:**
- Custom table prevents wp_options bloat
- Indexed columns for fast lookups
- Efficient queries with prepared statements

### Database Structure

The plugin creates a custom table `{prefix}_simple_smtp_settings` with the following structure:

```sql
CREATE TABLE wp_simple_smtp_settings (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    setting_key varchar(191) NOT NULL,
    setting_value longtext NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY setting_key (setting_key)
)
```

**Settings stored:**
- smtp_enabled (0 or 1)
- smtp_host (string)
- smtp_port (integer)
- smtp_encryption (none, ssl, tls)
- smtp_auth (0 or 1)
- smtp_username (string)
- smtp_password (string)
- from_email (email address)
- from_name (string)
- cleanup_on_uninstall (0 or 1)

## Requirements

- **WordPress**: 6.2 or higher (uses `%i` placeholder for table names)
- **PHP**: 7.4 or higher
- **MySQL**: 5.6+ or MariaDB 10.0+
- **Permissions**: `manage_options` capability for admin access

## Compatibility

- Works with all properly coded WordPress themes and plugins
- Compatible with WordPress multisite
- No conflicts with caching plugins
- Follows WordPress coding standards
- No external service dependencies

## Privacy & Data

This plugin:
- Stores settings locally in WordPress database
- Does not send data to external services
- Does not use cookies or tracking
- GDPR compliant
- Passwords stored in database (required for SMTP authentication)

## Troubleshooting

### Test Email Not Received

1. **Check Spam Folder**: Test emails may be filtered as spam
2. **Verify Settings**: Ensure all SMTP settings match your provider's requirements
3. **Check Credentials**: Verify username and password are correct
4. **Port Issues**: Try different port numbers (587, 465, or 25)
5. **Firewall**: Ensure your hosting provider doesn't block SMTP ports
6. **SMTP Logs**: Check with your SMTP provider for error logs

### Gmail-Specific Issues

- Use an **App-Specific Password** instead of your regular Gmail password
- Enable "Less secure app access" if not using 2FA
- Check Google's security settings for blocked sign-in attempts

### Common Error Messages

**"Failed to send test email"**: SMTP settings are incorrect or server is unreachable
**"Invalid email address"**: The From Email or test email address format is invalid
**"Security check failed"**: Nonce verification failed, try refreshing the page

## Frequently Asked Questions

### Do I need to enable SMTP?

Not necessarily. If your WordPress site is already sending emails successfully with the default PHP mail() function, you don't need to enable SMTP. However, SMTP typically provides better deliverability.

### Will this work with any SMTP provider?

Yes, Simple SMTP works with any standard SMTP server including Gmail, Outlook, Yahoo, SendGrid, Mailgun, AWS SES, and custom SMTP servers.

### Is my SMTP password secure?

The password is stored in your WordPress database using the same security measures as other WordPress settings. It must be stored unencrypted because it's needed to authenticate with your SMTP server.

### Can I use this on a multisite installation?

Yes, the plugin is multisite compatible. Settings are stored per-site.

### What happens if I disable the plugin?

Emails will revert to using the default PHP mail() function. Your settings remain in the database unless you enable "Cleanup on Uninstall" and delete the plugin.

## Changelog

### 1.0.0
- Initial release
- SMTP configuration with authentication
- Multiple encryption types (None, SSL, TLS)
- Test email functionality
- Custom From email and name
- Enable/disable toggle
- Custom database table implementation
- Clean admin interface
- Secure form handling

## License

This plugin is licensed under the GPL v2 or later.

---

**Plugin Version:** 1.0.0  
**Requires WordPress:** 6.2+  
**Requires PHP:** 7.4+  
**License:** GPL v2 or later
