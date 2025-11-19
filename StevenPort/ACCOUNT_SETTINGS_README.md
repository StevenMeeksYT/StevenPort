# Account Settings & Security Management System

This comprehensive account management system provides users with complete control over their profile, security settings, and preferences.

## Features

### 🔐 Profile Management
- **Username editing** with uniqueness validation
- **Email address updates** with duplicate checking
- **Personal information** (first name, last name, bio)
- **Real-time validation** and error handling

### 🛡️ Security Features
- **Password change** with current password verification
- **Password strength indicator** with real-time feedback
- **Forgot password** functionality with secure token-based reset
- **Email verification** system (ready for implementation)
- **Account security information** display
- **Secure password reset** with expiration tokens

### ⚙️ User Preferences
- **Theme selection** (Light, Dark, Auto)
- **Language preferences** (English, Spanish, French, German)
- **Timezone configuration** with major timezone support
- **Notification settings** (in-app and email notifications)
- **Real-time preference updates**

### ⚠️ Account Management
- **Account deletion** with password confirmation
- **Security confirmation** dialogs for destructive actions
- **Activity logging** for security auditing

## Files Created/Modified

### New Files
- `account_settings.php` - Main account settings page
- `forgot_password.php` - Password reset request page
- `reset_password.php` - Password reset form
- `update_database.sql` - Database schema updates

### Modified Files
- `func.php` - Added user management functions
- `profile.php` - Added link to account settings
- `login.php` - Added forgot password link

## Database Schema Updates

The system extends the existing `users` table with:

### Profile Fields
- `first_name` - User's first name
- `last_name` - User's last name
- `bio` - User biography/description

### Security Fields
- `last_login` - Last login timestamp
- `email_verified` - Email verification status
- `email_verification_token` - Email verification token
- `password_reset_token` - Password reset token
- `password_reset_expires` - Password reset expiration

### Preference Fields
- `theme` - UI theme preference
- `language` - Language preference
- `timezone` - Timezone setting
- `notifications` - In-app notifications setting
- `email_notifications` - Email notifications setting

### Security & Status Fields
- `account_status` - Account status (active, suspended, banned)
- `failed_login_attempts` - Failed login counter
- `locked_until` - Account lock expiration
- `two_factor_enabled` - 2FA status
- `two_factor_secret` - 2FA secret

### Additional Tables
- `user_sessions` - Session management
- `user_activity_logs` - Security audit trail

## Installation Instructions

1. **Update Database Schema**
   ```sql
   -- Run the SQL commands in update_database.sql
   mysql -u root -p stevenport < update_database.sql
   ```

2. **Verify File Permissions**
   - Ensure PHP has write access to the images directory
   - Check that session handling is properly configured

3. **Test the System**
   - Register a new account
   - Test profile updates
   - Test password changes
   - Test forgot password flow
   - Test preference changes

## Security Features

### Password Security
- **BCrypt hashing** for password storage
- **Minimum 8 character** password requirement
- **Real-time strength validation**
- **Secure token-based** password reset

### Session Security
- **Secure session handling**
- **CSRF protection** through form tokens
- **Input validation** and sanitization
- **SQL injection prevention** with prepared statements

### Account Security
- **Email uniqueness** validation
- **Username uniqueness** validation
- **Account lockout** protection (ready for implementation)
- **Activity logging** for security auditing

## User Experience Features

### Responsive Design
- **Mobile-friendly** interface
- **Tabbed navigation** for easy access
- **Real-time feedback** for all actions
- **Consistent styling** with existing theme

### Accessibility
- **Semantic HTML** structure
- **ARIA labels** for screen readers
- **Keyboard navigation** support
- **High contrast** color schemes

### Performance
- **Optimized database queries**
- **Efficient form handling**
- **Minimal JavaScript** dependencies
- **Fast page load times**

## Customization Options

### Themes
- Light theme (default)
- Dark theme
- Auto theme (system preference)

### Languages
- English (default)
- Spanish
- French
- German

### Timezones
- UTC (default)
- Major US timezones
- European timezones
- Asian timezones

## Future Enhancements

### Planned Features
- **Two-factor authentication** (2FA)
- **Email verification** system
- **Account lockout** after failed attempts
- **Password history** tracking
- **Social login** integration
- **Profile picture** upload
- **Advanced notification** preferences

### Security Improvements
- **Rate limiting** for password reset
- **IP-based** security monitoring
- **Advanced audit** logging
- **Security alerts** via email

## Support

For issues or questions about the account settings system:
1. Check the database schema is properly updated
2. Verify file permissions are correct
3. Test with a fresh user account
4. Check PHP error logs for any issues

## License

This account management system is part of the StevenPort project and follows the same licensing terms.
