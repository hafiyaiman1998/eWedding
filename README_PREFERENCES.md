# User Preferences System

The eWeddingCard platform now includes a comprehensive preferences system that allows both administrators and users to customize their experience.

## Features

### Theme & Appearance
- **Theme Mode**: Light, Dark, or Auto (follows system preference)
- **Color Schemes**: Default Pink, Rose Pink, Royal Purple, Ocean Blue, Garden Green, Sunset Orange
- **Font Size**: Small, Medium, Large
- **Live Preview**: Real-time preview of changes

### Layout & Navigation
- **Layout Density**: Compact, Comfortable, Spacious
- **Sidebar**: Option to collapse by default
- **Card View Mode**: Grid or List view
- **Items Per Page**: 6, 12, 24, or 48 items

### Animations & Effects
- **Floating Hearts**: Romantic background animation (can be disabled)
- **Animations**: Smooth transitions and hover effects
- **Animation Speed**: Slow, Normal, or Fast

### Notifications
- **Email Notifications**: Important updates via email
- **Browser Notifications**: Desktop notifications
- **Marketing Emails**: Feature updates and promotions

### Language & Region
- **Language**: English, Bahasa Malaysia, Chinese, Tamil
- **Date Format**: Multiple formats (YYYY-MM-DD, DD/MM/YYYY, etc.)
- **Timezone**: Various timezone options

## How to Access

### For Administrators
1. Click on your profile avatar in the top-right corner
2. Select "Preferences" from the dropdown menu
3. Or navigate directly to `/admin/preferences`

### For Users
1. Click on your profile avatar in the top-right corner
2. Select "Design Preferences" from the dropdown menu
3. Or navigate directly to `/user/preferences`

## Technical Implementation

### Database Structure
- `user_preferences` table stores all preference settings
- Each user has one preferences record
- Default preferences are automatically created when needed

### Files Created/Modified
- **Models**: `UserPreference.php`, updated `User.php`
- **Controllers**: `AdminPreferenceController.php`, `UserPreferenceController.php`
- **Views**: `admin/preferences/index.blade.php`, `user/preferences/index.blade.php`
- **Routes**: Added preference routes for both admin and user
- **JavaScript**: `preferences.js` for dynamic application
- **Migration**: `create_user_preferences_table.php`

### Key Features
- **Real-time Preview**: Changes are shown immediately
- **Dynamic CSS Variables**: Theme changes applied via CSS custom properties
- **Responsive Design**: Works on all device sizes
- **Form Validation**: Client-side and server-side validation
- **Reset Functionality**: Reset to default values
- **Auto-save**: Preferences are saved to database

### CSS Variables Applied
The system dynamically applies CSS variables based on user preferences:
- `--accent-color`: Primary theme color
- `--accent-light`: Light variant of theme color
- `--bg-primary`, `--bg-secondary`: Background colors
- `--text-primary`, `--text-secondary`: Text colors
- `--base-font-size`: Font size
- `--layout-padding`, `--element-spacing`: Layout spacing

## Usage Examples

### Getting User Preferences in Controllers
```php
$user = Auth::user();
$preferences = $user->getPreferences(); // Creates defaults if none exist
$cssVariables = $preferences->getCssVariables();
```

### Creating Default Preferences
```php
UserPreference::createDefaults($userId);
```

### Checking Specific Preferences
```php
if ($user->preferences->floating_hearts_enabled) {
    // Show floating hearts
}

if ($user->preferences->theme === 'dark') {
    // Apply dark theme styles
}
```

## Customization

### Adding New Preferences
1. Add new columns to the `user_preferences` migration
2. Update the `UserPreference` model fillable array
3. Add validation rules in the controllers
4. Update the preference views with new form fields
5. Add handling in the `getCssVariables()` method if needed

### Adding New Color Schemes
1. Update the color scheme enum in the migration
2. Add new case in `UserPreference::getCssVariables()`
3. Add new option in the preference forms
4. Update the JavaScript `COLOR_SCHEMES` object

### Adding New Languages
1. Update the language enum options in controllers
2. Add new language options in preference forms
3. Implement actual translation files as needed

## Best Practices

1. **Always check for preferences**: Use `$user->getPreferences()` which creates defaults if needed
2. **Validate input**: Both client-side and server-side validation are implemented
3. **Graceful degradation**: System works with default values if preferences fail to load
4. **Performance**: Preferences are cached and applied via CSS variables
5. **User Experience**: Live preview shows changes immediately

## Troubleshooting

### Preferences Not Loading
- Check if user is authenticated
- Verify database connection
- Check if preferences table exists and is migrated

### Styles Not Applying
- Ensure `preferences.js` is loaded
- Check browser console for JavaScript errors
- Verify CSS variables are being set on `:root`

### Reset Not Working
- Check CSRF token in forms
- Verify reset route is properly defined
- Ensure user has permission to reset preferences

## Future Enhancements

Potential improvements that could be added:
- Export/Import preferences
- Preference sharing between users
- Advanced theme customization with custom colors
- More language options
- Preference presets/templates
- Dashboard widget customization
- Email template preferences
- Advanced notification settings 