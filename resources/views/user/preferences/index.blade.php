@extends('layouts.user.user')

@section('title', 'My Preferences')

@section('additional_css')
<style>
    .preferences-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .preference-card {
        background: var(--card-bg, white);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border: 1px solid var(--border-color, #e5e7eb);
        transition: all 0.3s ease;
    }

    .preference-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .preference-header {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--border-color, #e5e7eb);
    }

    .preference-icon {
        font-size: 24px;
        margin-right: 15px;
        color: var(--accent-color, #e91e63);
    }

    .preference-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--text-primary, #1f2937);
        margin: 0;
    }

    .preference-subtitle {
        font-size: 14px;
        color: var(--text-secondary, #6b7280);
        margin: 5px 0 0 0;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: var(--text-primary, #374151);
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--border-color, #e5e7eb);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: var(--input-bg, white);
        color: var(--text-primary, #1f2937);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent-color, #e91e63);
        box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
    }

    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 12px center;
        background-repeat: no-repeat;
        background-size: 16px 12px;
        padding-right: 40px;
    }

    .switch-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 0;
        border-bottom: 1px solid var(--border-light, #f3f4f6);
    }

    .switch-container:last-child {
        border-bottom: none;
    }

    .switch-info {
        flex: 1;
    }

    .switch-title {
        font-weight: 500;
        color: var(--text-primary, #374151);
        margin-bottom: 5px;
    }

    .switch-description {
        font-size: 13px;
        color: var(--text-secondary, #6b7280);
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 28px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .3s;
        border-radius: 28px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: var(--accent-color, #e91e63);
    }

    input:checked + .slider:before {
        transform: translateX(22px);
    }

    .color-scheme-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
        margin-top: 10px;
    }

    .color-option {
        position: relative;
        padding: 15px;
        border: 2px solid var(--border-color, #e5e7eb);
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--card-bg, white);
    }

    .color-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .color-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .color-option.selected {
        border-color: var(--accent-color, #e91e63);
        background: var(--accent-light, #fce4ec);
    }

    .color-preview {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin: 0 auto 10px;
        border: 2px solid rgba(255,255,255,0.8);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Background Theme Grid */
    .background-theme-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-top: 15px;
    }

    .background-option {
        position: relative;
        padding: 20px;
        border: 3px solid var(--border-color, #e5e7eb);
        border-radius: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: var(--card-bg, white);
        overflow: hidden;
    }

    .background-option:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .background-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .background-option.selected {
        border-color: var(--accent-color, #e91e63);
        background: var(--accent-light, #fce4ec);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(233, 30, 99, 0.3);
    }

    .background-preview {
        width: 100%;
        height: 80px;
        border-radius: 10px;
        margin: 0 auto 15px;
        border: 2px solid rgba(255,255,255,0.8);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }

    .background-preview::before {
        content: '✨';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 24px;
        opacity: 0.7;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .background-label {
        font-weight: 600;
        color: var(--text-primary, #374151);
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
    }

    .background-description {
        font-size: 12px;
        color: var(--text-secondary, #6b7280);
        line-height: 1.4;
        font-style: italic;
    }

    .background-option.selected .background-label {
        color: var(--accent-color, #e91e63);
        font-weight: 700;
    }

    .background-option.selected .background-description {
        color: var(--accent-dark, #c2185b);
    }

    .btn-group {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid var(--border-color, #e5e7eb);
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary {
        background: var(--accent-color, #e91e63);
        color: white;
    }

    .btn-primary:hover {
        background: var(--accent-dark, #c2185b);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: var(--bg-secondary, #f8f9fa);
        color: var(--text-secondary, #6b7280);
        border: 1px solid var(--border-color, #e5e7eb);
    }

    .btn-secondary:hover {
        background: var(--bg-hover, #e9ecef);
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-1px);
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid;
    }

    .alert-success {
        background: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-error {
        background: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .preview-box {
        background: var(--bg-secondary, #f8f9fa);
        border: 2px dashed var(--border-color, #e5e7eb);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        margin-top: 15px;
    }

    .preview-text {
        font-size: var(--base-font-size, 16px);
        color: var(--text-primary, #1f2937);
        font-weight: 500;
    }

    .live-preview {
        background: linear-gradient(135deg, var(--accent-color, #e91e63) 0%, var(--accent-light, #f8bbd9) 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 20px;
    }

    .heart-demo {
        position: relative;
        height: 60px;
        overflow: hidden;
        border-radius: 8px;
        background: linear-gradient(45deg, #fce4ec, #f8bbd9);
        margin: 15px 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .floating-heart-demo {
        position: absolute;
        font-size: 20px;
        animation: floatUpDemo 3s linear infinite;
        opacity: 0.7;
    }

    @keyframes floatUpDemo {
        0% {
            bottom: -20px;
            opacity: 0;
        }
        10% {
            opacity: 0.7;
        }
        90% {
            opacity: 0.7;
        }
        100% {
            bottom: 80px;
            opacity: 0;
        }
    }

    @media (max-width: 768px) {
        .preference-card {
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .color-scheme-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .btn-group {
            flex-direction: column-reverse;
        }
    }
</style>
@endsection

@section('content')
<div class="preferences-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-palette"></i>
            My Preferences
        </h1>
        <p class="page-subtitle">Personalize your eWeddingCard experience</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('error') ?: 'Please fix the errors below and try again.' }}
        </div>
    @endif

    <form method="POST" action="{{ route('user.preferences.update') }}" id="preferencesForm">
        @csrf

        <!-- Theme & Appearance -->
        <div class="preference-card">
            <div class="preference-header">
                <i class="fas fa-paint-brush preference-icon"></i>
                <div>
                    <h3 class="preference-title">Theme & Appearance</h3>
                    <p class="preference-subtitle">Make your wedding studio uniquely yours</p>
                </div>
            </div>

            <div class="live-preview">
                <h4>💕 Live Preview</h4>
                <p>This is how your personalized studio will look</p>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Theme Mode</label>
                    <select name="theme" class="form-control form-select" required>
                        <option value="light" {{ $preferences->theme === 'light' ? 'selected' : '' }}>Light Theme</option>
                        <option value="dark" {{ $preferences->theme === 'dark' ? 'selected' : '' }}>Dark Theme</option>
                        <option value="auto" {{ $preferences->theme === 'auto' ? 'selected' : '' }}>Auto (System)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Font Size</label>
                    <select name="font_size" class="form-control form-select" required>
                        <option value="small" {{ $preferences->font_size === 'small' ? 'selected' : '' }}>Small</option>
                        <option value="medium" {{ $preferences->font_size === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="large" {{ $preferences->font_size === 'large' ? 'selected' : '' }}>Large</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Color Scheme - Pick Your Wedding Colors!</label>
                <div class="color-scheme-grid">
                    <div class="color-option {{ $preferences->color_scheme === 'default' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="default" {{ $preferences->color_scheme === 'default' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #e91e63, #f8bbd9);"></div>
                        <span>Classic Pink</span>
                    </div>
                    <div class="color-option {{ $preferences->color_scheme === 'pink' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="pink" {{ $preferences->color_scheme === 'pink' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #e91e63, #f8bbd9);"></div>
                        <span>Rose Pink</span>
                    </div>
                    <div class="color-option {{ $preferences->color_scheme === 'purple' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="purple" {{ $preferences->color_scheme === 'purple' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #9c27b0, #e1bee7);"></div>
                        <span>Royal Purple</span>
                    </div>
                    <div class="color-option {{ $preferences->color_scheme === 'blue' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="blue" {{ $preferences->color_scheme === 'blue' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #2196f3, #bbdefb);"></div>
                        <span>Ocean Blue</span>
                    </div>
                    <div class="color-option {{ $preferences->color_scheme === 'green' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="green" {{ $preferences->color_scheme === 'green' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #4caf50, #c8e6c9);"></div>
                        <span>Garden Green</span>
                    </div>
                    <div class="color-option {{ $preferences->color_scheme === 'orange' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="orange" {{ $preferences->color_scheme === 'orange' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #ff9800, #ffe0b2);"></div>
                        <span>Sunset Orange</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layout Preferences -->
        <div class="preference-card">
            <div class="preference-header">
                <i class="fas fa-layout preference-icon"></i>
                <div>
                    <h3 class="preference-title">Layout & Navigation</h3>
                    <p class="preference-subtitle">Organize your workspace for maximum creativity</p>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Layout Density</label>
                    <select name="layout_density" class="form-control form-select" required>
                        <option value="compact" {{ $preferences->layout_density === 'compact' ? 'selected' : '' }}>Compact - More on screen</option>
                        <option value="comfortable" {{ $preferences->layout_density === 'comfortable' ? 'selected' : '' }}>Comfortable - Balanced</option>
                        <option value="spacious" {{ $preferences->layout_density === 'spacious' ? 'selected' : '' }}>Spacious - Relaxed feel</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Card View Mode</label>
                    <select name="card_view_mode" class="form-control form-select" required>
                        <option value="grid" {{ $preferences->card_view_mode === 'grid' ? 'selected' : '' }}>Grid View - Visual cards</option>
                        <option value="list" {{ $preferences->card_view_mode === 'list' ? 'selected' : '' }}>List View - Detailed rows</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Cards Per Page</label>
                    <select name="items_per_page" class="form-control form-select" required>
                        <option value="6" {{ $preferences->items_per_page == 6 ? 'selected' : '' }}>6 cards</option>
                        <option value="12" {{ $preferences->items_per_page == 12 ? 'selected' : '' }}>12 cards</option>
                        <option value="24" {{ $preferences->items_per_page == 24 ? 'selected' : '' }}>24 cards</option>
                        <option value="48" {{ $preferences->items_per_page == 48 ? 'selected' : '' }}>48 cards</option>
                    </select>
                </div>
            </div>

            <div class="switch-container">
                <div class="switch-info">
                    <div class="switch-title">Collapse Sidebar by Default</div>
                    <div class="switch-description">Start with a collapsed sidebar for more content space</div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="sidebar_collapsed" {{ $preferences->sidebar_collapsed ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <!-- Background Customization -->
        <div class="preference-card">
            <div class="preference-header">
                <i class="fas fa-image preference-icon"></i>
                <div>
                    <h3 class="preference-title">Background Themes</h3>
                    <p class="preference-subtitle">Transform your wedding studio with beautiful backgrounds</p>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Background Theme - Choose Your Perfect Setting!</label>
                <div class="background-theme-grid">
                    @php
                        $backgroundThemes = \App\Models\UserPreference::getBackgroundThemes();
                        $themeColors = [
                            'romantic' => 'linear-gradient(45deg, #ffecd2, #fcb69f)',
                            'elegant' => 'linear-gradient(45deg, #f6f6f6, #e9e9e9)',
                            'modern' => 'linear-gradient(45deg, #667eea, #764ba2)',
                            'nature' => 'linear-gradient(45deg, #a8e6cf, #81c784)',
                            'sunset' => 'linear-gradient(45deg, #ff9a9e, #fecfef)',
                            'ocean' => 'linear-gradient(45deg, #a8edea, #89cff0)',
                            'royal' => 'linear-gradient(45deg, #d299c2, #b19cd9)',
                            'minimal' => 'linear-gradient(45deg, #ffffff, #f8f9fa)'
                        ];
                    @endphp
                    
                    @foreach($backgroundThemes as $value => $label)
                    <div class="background-option {{ $preferences->background_theme === $value ? 'selected' : '' }}">
                        <input type="radio" name="background_theme" value="{{ $value }}" {{ $preferences->background_theme === $value ? 'checked' : '' }}>
                        <div class="background-preview" style="background: {{ $themeColors[$value] }};"></div>
                        <span class="background-label">{{ $label }}</span>
                        <div class="background-description">
                            @switch($value)
                                @case('romantic') Soft pastels perfect for love stories @break
                                @case('elegant') Sophisticated neutrals for timeless elegance @break
                                @case('modern') Contemporary colors for modern couples @break
                                @case('nature') Fresh greens inspired by natural beauty @break
                                @case('sunset') Warm sunset hues for romantic evenings @break
                                @case('ocean') Cool blues reminiscent of seaside weddings @break
                                @case('royal') Luxurious purples fit for royalty @break
                                @case('minimal') Clean whites for minimalist perfection @break
                            @endswitch
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Background Opacity</label>
                    <select name="background_opacity" class="form-control form-select" required>
                        <option value="light" {{ $preferences->background_opacity === 'light' ? 'selected' : '' }}>Light & Subtle</option>
                        <option value="medium" {{ $preferences->background_opacity === 'medium' ? 'selected' : '' }}>Medium Intensity</option>
                        <option value="bold" {{ $preferences->background_opacity === 'bold' ? 'selected' : '' }}>Bold & Vibrant</option>
                    </select>
                </div>
            </div>

            <div class="switch-container">
                <div class="switch-info">
                    <div class="switch-title">Enable Background Animation ✨</div>
                    <div class="switch-description">Animate the background with a subtle shifting effect</div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="background_animation_enabled" {{ $preferences->background_animation_enabled ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="switch-container">
                <div class="switch-info">
                    <div class="switch-title">Background Blur Effect</div>
                    <div class="switch-description">Add a subtle blur effect for a dreamy look</div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="background_blur_enabled" {{ $preferences->background_blur_enabled ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <!-- Animation Preferences -->
        <div class="preference-card">
            <div class="preference-header">
                <i class="fas fa-heart preference-icon"></i>
                <div>
                    <h3 class="preference-title">Romance & Animations</h3>
                    <p class="preference-subtitle">Add magical touches to your wedding experience</p>
                </div>
            </div>

            <div class="switch-container">
                <div class="switch-info">
                    <div class="switch-title">Enable Floating Hearts 💕</div>
                    <div class="switch-description">Show romantic floating hearts animation in the background</div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="floating_hearts_enabled" {{ $preferences->floating_hearts_enabled ? 'checked' : '' }} onchange="toggleHeartDemo()">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="heart-demo" id="heartDemo" style="display: {{ $preferences->floating_hearts_enabled ? 'flex' : 'none' }};">
                <span>💕 Preview of floating hearts</span>
                <div class="floating-heart-demo" style="left: 20%; animation-delay: 0s;">💕</div>
                <div class="floating-heart-demo" style="left: 40%; animation-delay: 1s;">💖</div>
                <div class="floating-heart-demo" style="left: 60%; animation-delay: 2s;">💗</div>
                <div class="floating-heart-demo" style="left: 80%; animation-delay: 0.5s;">💓</div>
            </div>

            <div class="switch-container">
                <div class="switch-info">
                    <div class="switch-title">Enable Smooth Animations</div>
                    <div class="switch-description">Show smooth transitions and hover effects</div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="animations_enabled" {{ $preferences->animations_enabled ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label">Animation Speed</label>
                <select name="animation_speed" class="form-control form-select" required>
                    <option value="slow" {{ $preferences->animation_speed === 'slow' ? 'selected' : '' }}>Slow & Graceful</option>
                    <option value="normal" {{ $preferences->animation_speed === 'normal' ? 'selected' : '' }}>Normal Pace</option>
                    <option value="fast" {{ $preferences->animation_speed === 'fast' ? 'selected' : '' }}>Quick & Snappy</option>
                </select>
            </div>
        </div>

        <!-- Notification Preferences -->
        <div class="preference-card">
            <div class="preference-header">
                <i class="fas fa-bell preference-icon"></i>
                <div>
                    <h3 class="preference-title">Notifications</h3>
                    <p class="preference-subtitle">Stay updated on your wedding planning journey</p>
                </div>
            </div>

            <div class="switch-container">
                <div class="switch-info">
                    <div class="switch-title">Email Notifications</div>
                    <div class="switch-description">Get updates about your wedding cards and guest responses</div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="email_notifications" {{ $preferences->email_notifications ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="switch-container">
                <div class="switch-info">
                    <div class="switch-title">Browser Notifications</div>
                    <div class="switch-description">Instant alerts when guests RSVP to your wedding</div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="browser_notifications" {{ $preferences->browser_notifications ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="switch-container">
                <div class="switch-info">
                    <div class="switch-title">Wedding Tips & Ideas</div>
                    <div class="switch-description">Receive inspiration and new template notifications</div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="marketing_emails" {{ $preferences->marketing_emails ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <!-- Localization Preferences -->
        <div class="preference-card">
            <div class="preference-header">
                <i class="fas fa-globe preference-icon"></i>
                <div>
                    <h3 class="preference-title">Language & Region</h3>
                    <p class="preference-subtitle">Set your preferred language and date formats</p>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Language</label>
                    <select name="language" class="form-control form-select" required>
                        <option value="en" {{ $preferences->language === 'en' ? 'selected' : '' }}>English</option>
                        <option value="ms" {{ $preferences->language === 'ms' ? 'selected' : '' }}>Bahasa Malaysia</option>
                        <option value="zh" {{ $preferences->language === 'zh' ? 'selected' : '' }}>中文 (Chinese)</option>
                        <option value="ta" {{ $preferences->language === 'ta' ? 'selected' : '' }}>தமிழ் (Tamil)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Date Format</label>
                    <select name="date_format" class="form-control form-select" required>
                        <option value="Y-m-d" {{ $preferences->date_format === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD ({{ date('Y-m-d') }})</option>
                        <option value="d/m/Y" {{ $preferences->date_format === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY ({{ date('d/m/Y') }})</option>
                        <option value="m/d/Y" {{ $preferences->date_format === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY ({{ date('m/d/Y') }})</option>
                        <option value="F j Y" {{ $preferences->date_format === 'F j Y' ? 'selected' : '' }}>Month DD YYYY ({{ date('F j Y') }})</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Timezone</label>
                <select name="timezone" class="form-control form-select" required>
                    <option value="UTC" {{ $preferences->timezone === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                    <option value="Asia/Kuala_Lumpur" {{ $preferences->timezone === 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>Malaysia (GMT+8)</option>
                    <option value="Asia/Singapore" {{ $preferences->timezone === 'Asia/Singapore' ? 'selected' : '' }}>Singapore (GMT+8)</option>
                    <option value="Asia/Bangkok" {{ $preferences->timezone === 'Asia/Bangkok' ? 'selected' : '' }}>Bangkok (GMT+7)</option>
                    <option value="Asia/Jakarta" {{ $preferences->timezone === 'Asia/Jakarta' ? 'selected' : '' }}>Jakarta (GMT+7)</option>
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="btn-group">
            <button type="button" class="btn btn-danger" onclick="resetPreferences()">
                <i class="fas fa-undo"></i>
                Reset to Default
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save My Preferences
            </button>
        </div>
    </form>
</div>
@endsection

@section('additional_js')
<script>
    // Color scheme selection
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
            updateLivePreview();
        });
    });

    // Background theme selection
    document.querySelectorAll('.background-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.background-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
            updateBackgroundPreview();
        });
    });

    // Live preview updates
    function updateLivePreview() {
        const theme = document.querySelector('[name="theme"]').value;
        const colorScheme = document.querySelector('[name="color_scheme"]:checked').value;
        const fontSize = document.querySelector('[name="font_size"]').value;
        
        const preview = document.querySelector('.live-preview');
        
        // Update preview based on selections
        let colors = {
            'default': { primary: '#e91e63', secondary: '#f8bbd9' },
            'pink': { primary: '#e91e63', secondary: '#f8bbd9' },
            'purple': { primary: '#9c27b0', secondary: '#e1bee7' },
            'blue': { primary: '#2196f3', secondary: '#bbdefb' },
            'green': { primary: '#4caf50', secondary: '#c8e6c9' },
            'orange': { primary: '#ff9800', secondary: '#ffe0b2' }
        };
        
        if (preview) {
            preview.style.background = `linear-gradient(135deg, ${colors[colorScheme].primary} 0%, ${colors[colorScheme].secondary} 100%)`;
            preview.style.fontSize = fontSize === 'small' ? '14px' : fontSize === 'large' ? '18px' : '16px';
        }
    }

    // Background preview updates
    function updateBackgroundPreview() {
        const backgroundTheme = document.querySelector('[name="background_theme"]:checked').value;
        const backgroundOpacity = document.querySelector('[name="background_opacity"]').value;
        const backgroundAnimation = document.querySelector('[name="background_animation_enabled"]').checked;
        const backgroundBlur = document.querySelector('[name="background_blur_enabled"]').checked;
        
        // Background themes map
        const themes = {
            'romantic': 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 25%, #ffecd2 50%, #a8edea 75%, #fed6e3 100%)',
            'elegant': 'linear-gradient(135deg, #f6f6f6 0%, #e9e9e9 25%, #f1f1f1 50%, #e0e0e0 75%, #f4f4f4 100%)',
            'modern': 'linear-gradient(135deg, #667eea 0%, #764ba2 25%, #6B73FF 50%, #9A9CE4 75%, #C9C9FF 100%)',
            'nature': 'linear-gradient(135deg, #a8e6cf 0%, #dcedc8 25%, #c8e6c9 50%, #81c784 75%, #a5d6a7 100%)',
            'sunset': 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 25%, #fecfef 50%, #ffd1ff 75%, #ff9a9e 100%)',
            'ocean': 'linear-gradient(135deg, #a8edea 0%, #fed6e3 25%, #a8edea 50%, #89cff0 75%, #b3e5fc 100%)',
            'royal': 'linear-gradient(135deg, #d299c2 0%, #fef9d7 25%, #d299c2 50%, #b19cd9 75%, #c2a5f5 100%)',
            'minimal': 'linear-gradient(135deg, #ffffff 0%, #f8f9fa 25%, #ffffff 50%, #e9ecef 75%, #f8f9fa 100%)'
        };
        
        // Apply opacity
        let opacity = backgroundOpacity === 'light' ? '0.7' : backgroundOpacity === 'bold' ? '1.0' : '0.85';
        
        // Apply to body immediately for preview
        document.body.style.background = themes[backgroundTheme];
        document.body.style.opacity = opacity;
        document.body.style.animation = backgroundAnimation ? 'gradientShift 15s ease infinite' : 'none';
        document.body.style.backdropFilter = backgroundBlur ? 'blur(5px)' : 'none';
        
        // Show preview message
        const livePreview = document.querySelector('.live-preview');
        if (livePreview) {
            livePreview.innerHTML = `
                <h4>💕 Background Preview Active!</h4>
                <p>You're seeing the <strong>${backgroundTheme}</strong> theme in action</p>
                <small>Save to make this permanent</small>
            `;
        }
    }

    // Toggle heart demo
    function toggleHeartDemo() {
        const checkbox = document.querySelector('[name="floating_hearts_enabled"]');
        const demo = document.getElementById('heartDemo');
        demo.style.display = checkbox.checked ? 'flex' : 'none';
    }

    // Initialize live preview
    document.addEventListener('DOMContentLoaded', function() {
        updateLivePreview();
        updateBackgroundPreview();
        
        // Update on change
        document.querySelector('[name="theme"]').addEventListener('change', updateLivePreview);
        document.querySelector('[name="font_size"]').addEventListener('change', updateLivePreview);
        
        // Background change listeners
        document.querySelector('[name="background_opacity"]').addEventListener('change', updateBackgroundPreview);
        document.querySelector('[name="background_animation_enabled"]').addEventListener('change', updateBackgroundPreview);
        document.querySelector('[name="background_blur_enabled"]').addEventListener('change', updateBackgroundPreview);
    });

    // Reset preferences function
    function resetPreferences() {
        Swal.fire({
            title: '💕 Reset Your Preferences?',
            text: 'This will reset all your personalized settings to default values. Your wedding cards will not be affected.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, reset my preferences',
            cancelButtonText: 'Keep my settings'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a form to submit reset request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("user.preferences.reset") }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Form validation
    document.getElementById('preferencesForm').addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = '#dc3545';
            } else {
                field.style.borderColor = '';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                title: 'Please Complete All Fields',
                text: 'Make sure all required preferences are selected.',
                icon: 'error',
                confirmButtonColor: '#e91e63'
            });
        }
    });
</script>
@endsection 