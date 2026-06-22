<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme',
        'color_scheme',
        'sidebar_collapsed',
        'layout_density',
        'font_size',
        'floating_hearts_enabled',
        'animations_enabled',
        'animation_speed',
        'background_theme',
        'background_animation_enabled',
        'background_opacity',
        'background_blur_enabled',
        'email_notifications',
        'browser_notifications',
        'marketing_emails',
        'dashboard_widgets',
        'card_view_mode',
        'items_per_page',
        'language',
        'timezone',
        'date_format',
    ];

    protected $casts = [
        'sidebar_collapsed' => 'boolean',
        'floating_hearts_enabled' => 'boolean',
        'animations_enabled' => 'boolean',
        'background_animation_enabled' => 'boolean',
        'background_blur_enabled' => 'boolean',
        'email_notifications' => 'boolean',
        'browser_notifications' => 'boolean',
        'marketing_emails' => 'boolean',
        'dashboard_widgets' => 'array',
        'items_per_page' => 'integer',
    ];

    /**
     * Get the user that owns the preferences.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get default preferences for a new user.
     */
    public static function getDefaults(): array
    {
        return [
            'theme' => 'light',
            'color_scheme' => 'default',
            'sidebar_collapsed' => false,
            'layout_density' => 'comfortable',
            'font_size' => 'medium',
            'floating_hearts_enabled' => true,
            'animations_enabled' => true,
            'animation_speed' => 'normal',
            'background_theme' => 'romantic',
            'background_animation_enabled' => true,
            'background_opacity' => 'medium',
            'background_blur_enabled' => false,
            'email_notifications' => true,
            'browser_notifications' => false,
            'marketing_emails' => false,
            'dashboard_widgets' => null,
            'card_view_mode' => 'grid',
            'items_per_page' => 12,
            'language' => 'en',
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
        ];
    }

    /**
     * Create default preferences for a user.
     */
    public static function createDefaults(int $userId): self
    {
        return self::create(array_merge(
            ['user_id' => $userId],
            self::getDefaults()
        ));
    }

    /**
     * Get CSS variables for theme customization.
     */
    public function getCssVariables(): array
    {
        $variables = [];

        // Theme variables
        if ($this->theme === 'dark') {
            $variables['--bg-primary'] = '#1a1a1a';
            $variables['--bg-secondary'] = '#2d2d2d';
            $variables['--text-primary'] = '#ffffff';
            $variables['--text-secondary'] = '#cccccc';
        }

        // Color scheme variables
        switch ($this->color_scheme) {
            case 'pink':
                $variables['--accent-color'] = '#e91e63';
                $variables['--accent-light'] = '#f8bbd9';
                break;
            case 'purple':
                $variables['--accent-color'] = '#9c27b0';
                $variables['--accent-light'] = '#e1bee7';
                break;
            case 'blue':
                $variables['--accent-color'] = '#2196f3';
                $variables['--accent-light'] = '#bbdefb';
                break;
            case 'green':
                $variables['--accent-color'] = '#4caf50';
                $variables['--accent-light'] = '#c8e6c9';
                break;
            case 'orange':
                $variables['--accent-color'] = '#ff9800';
                $variables['--accent-light'] = '#ffe0b2';
                break;
        }

        // Font size variables
        switch ($this->font_size) {
            case 'small':
                $variables['--base-font-size'] = '14px';
                break;
            case 'large':
                $variables['--base-font-size'] = '18px';
                break;
            default:
                $variables['--base-font-size'] = '16px';
        }

        // Layout density variables
        switch ($this->layout_density) {
            case 'compact':
                $variables['--layout-padding'] = '15px';
                $variables['--element-spacing'] = '10px';
                break;
            case 'spacious':
                $variables['--layout-padding'] = '40px';
                $variables['--element-spacing'] = '30px';
                break;
            default:
                $variables['--layout-padding'] = '30px';
                $variables['--element-spacing'] = '20px';
        }

        // Background variables
        $backgroundData = $this->getBackgroundVariables();
        $variables['--bg-main'] = $backgroundData['gradient'];
        $variables['--bg-animation'] = $this->background_animation_enabled ? 'gradientShift 15s ease infinite' : 'none';
        $variables['--bg-opacity'] = $backgroundData['opacity'];
        $variables['--bg-blur'] = $this->background_blur_enabled ? 'blur(5px)' : 'none';

        return $variables;
    }

    /**
     * Get background gradient and settings based on theme.
     */
    public function getBackgroundVariables(): array
    {
        $themes = [
            'romantic' => [
                'gradient' => 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 25%, #ffecd2 50%, #a8edea 75%, #fed6e3 100%)',
                'description' => 'Soft romantic pastels with hearts',
            ],
            'elegant' => [
                'gradient' => 'linear-gradient(135deg, #f6f6f6 0%, #e9e9e9 25%, #f1f1f1 50%, #e0e0e0 75%, #f4f4f4 100%)',
                'description' => 'Sophisticated neutral tones',
            ],
            'modern' => [
                'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 25%, #6B73FF 50%, #9A9CE4 75%, #C9C9FF 100%)',
                'description' => 'Contemporary purple-blue gradients',
            ],
            'nature' => [
                'gradient' => 'linear-gradient(135deg, #a8e6cf 0%, #dcedc8 25%, #c8e6c9 50%, #81c784 75%, #a5d6a7 100%)',
                'description' => 'Fresh green nature inspired',
            ],
            'sunset' => [
                'gradient' => 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 25%, #fecfef 50%, #ffd1ff 75%, #ff9a9e 100%)',
                'description' => 'Warm sunset colors',
            ],
            'ocean' => [
                'gradient' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 25%, #a8edea 50%, #89cff0 75%, #b3e5fc 100%)',
                'description' => 'Cool ocean blues and teals',
            ],
            'royal' => [
                'gradient' => 'linear-gradient(135deg, #d299c2 0%, #fef9d7 25%, #d299c2 50%, #b19cd9 75%, #c2a5f5 100%)',
                'description' => 'Luxurious purple and gold',
            ],
            'minimal' => [
                'gradient' => 'linear-gradient(135deg, #ffffff 0%, #f8f9fa 25%, #ffffff 50%, #e9ecef 75%, #f8f9fa 100%)',
                'description' => 'Clean minimal white',
            ],
        ];

        $theme = $themes[$this->background_theme] ?? $themes['romantic'];

        // Apply opacity modifier
        $opacity = match ($this->background_opacity) {
            'light' => '0.7',
            'bold' => '1.0',
            default => '0.85'
        };

        return [
            'gradient' => $theme['gradient'],
            'description' => $theme['description'],
            'opacity' => $opacity,
        ];
    }

    /**
     * Get all available background themes.
     */
    public static function getBackgroundThemes(): array
    {
        return [
            'romantic' => 'Romantic Pastels',
            'elegant' => 'Elegant Neutrals',
            'modern' => 'Modern Purple-Blue',
            'nature' => 'Natural Greens',
            'sunset' => 'Sunset Warmth',
            'ocean' => 'Ocean Blues',
            'royal' => 'Royal Luxury',
            'minimal' => 'Minimal Clean',
        ];
    }
}
