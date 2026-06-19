@extends('layouts.admin.admin')

@section('title', 'Admin Preferences')

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

    .color-scheme-grid,
    .background-theme-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
        margin-top: 10px;
    }

    .color-option,
    .background-option {
        position: relative;
        padding: 15px;
        border: 2px solid var(--border-color, #e5e7eb);
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--card-bg, white);
    }

    .color-option:hover,
    .background-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .color-option input,
    .background-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .color-option.selected,
    .background-option.selected {
        border-color: var(--accent-color, #e91e63);
        background: var(--accent-light, #fce4ec);
    }

    .color-preview,
    .background-preview {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin: 0 auto 10px;
        border: 2px solid rgba(255,255,255,0.8);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .background-preview {
        height: 50px;
        border-radius: 8px;
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

    .live-preview {
        background: linear-gradient(135deg, var(--accent-color, #e91e63) 0%, var(--accent-light, #f8bbd9) 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="preferences-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-cog"></i>
            Admin Preferences
        </h1>
        <p class="page-subtitle">Customize your admin dashboard experience</p>
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

    <form method="POST" action="{{ route('admin.preferences.update') }}" id="preferencesForm">
        @csrf

        <!-- Background Customization -->
        <div class="preference-card">
            <div class="preference-header">
                <i class="fas fa-image preference-icon"></i>
                <div>
                    <h3 class="preference-title">Background Themes</h3>
                    <p class="preference-subtitle">Customize your admin dashboard background</p>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Background Theme</label>
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
                        <span>{{ $label }}</span>
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
                    <div class="switch-title">Enable Background Animation</div>
                    <div class="switch-description">Animate the background with a shifting gradient effect</div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="background_animation_enabled" {{ $preferences->background_animation_enabled ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="switch-container">
                <div class="switch-info">
                    <div class="switch-title">Background Blur Effect</div>
                    <div class="switch-description">Add a subtle blur effect for enhanced focus</div>
                </div>
                <label class="switch">
                    <input type="checkbox" name="background_blur_enabled" {{ $preferences->background_blur_enabled ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <!-- Theme & Appearance -->
        <div class="preference-card">
            <div class="preference-header">
                <i class="fas fa-paint-brush preference-icon"></i>
                <div>
                    <h3 class="preference-title">Theme & Colors</h3>
                    <p class="preference-subtitle">Customize the visual appearance</p>
                </div>
            </div>

            <div class="live-preview">
                <h4>Live Preview</h4>
                <p>This shows your current theme settings</p>
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
                <label class="form-label">Color Scheme</label>
                <div class="color-scheme-grid">
                    <div class="color-option {{ $preferences->color_scheme === 'default' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="default" {{ $preferences->color_scheme === 'default' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #e91e63, #f8bbd9);"></div>
                        <span>Default</span>
                    </div>
                    <div class="color-option {{ $preferences->color_scheme === 'pink' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="pink" {{ $preferences->color_scheme === 'pink' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #e91e63, #f8bbd9);"></div>
                        <span>Pink</span>
                    </div>
                    <div class="color-option {{ $preferences->color_scheme === 'purple' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="purple" {{ $preferences->color_scheme === 'purple' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #9c27b0, #e1bee7);"></div>
                        <span>Purple</span>
                    </div>
                    <div class="color-option {{ $preferences->color_scheme === 'blue' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="blue" {{ $preferences->color_scheme === 'blue' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #2196f3, #bbdefb);"></div>
                        <span>Blue</span>
                    </div>
                    <div class="color-option {{ $preferences->color_scheme === 'green' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="green" {{ $preferences->color_scheme === 'green' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #4caf50, #c8e6c9);"></div>
                        <span>Green</span>
                    </div>
                    <div class="color-option {{ $preferences->color_scheme === 'orange' ? 'selected' : '' }}">
                        <input type="radio" name="color_scheme" value="orange" {{ $preferences->color_scheme === 'orange' ? 'checked' : '' }}>
                        <div class="color-preview" style="background: linear-gradient(45deg, #ff9800, #ffe0b2);"></div>
                        <span>Orange</span>
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
                    <p class="preference-subtitle">Adjust interface layout and spacing</p>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Layout Density</label>
                    <select name="layout_density" class="form-control form-select" required>
                        <option value="compact" {{ $preferences->layout_density === 'compact' ? 'selected' : '' }}>Compact</option>
                        <option value="comfortable" {{ $preferences->layout_density === 'comfortable' ? 'selected' : '' }}>Comfortable</option>
                        <option value="spacious" {{ $preferences->layout_density === 'spacious' ? 'selected' : '' }}>Spacious</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Items Per Page</label>
                    <select name="items_per_page" class="form-control form-select" required>
                        <option value="12" {{ $preferences->items_per_page == 12 ? 'selected' : '' }}>12 items</option>
                        <option value="24" {{ $preferences->items_per_page == 24 ? 'selected' : '' }}>24 items</option>
                        <option value="48" {{ $preferences->items_per_page == 48 ? 'selected' : '' }}>48 items</option>
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

        <!-- Action Buttons -->
        <div class="btn-group">
            <button type="button" class="btn btn-danger" onclick="resetPreferences()">
                <i class="fas fa-undo"></i>
                Reset to Default
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Preferences
            </button>
        </div>
    </form>
</div>
@endsection

@section('additional_js')
<script>
    // Background theme selection
    document.querySelectorAll('.background-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.background-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
            updateBackgroundPreview();
        });
    });

    // Color scheme selection
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });

    // Background preview updates
    function updateBackgroundPreview() {
        const backgroundTheme = document.querySelector('[name="background_theme"]:checked').value;
        const backgroundOpacity = document.querySelector('[name="background_opacity"]').value;
        const backgroundAnimation = document.querySelector('[name="background_animation_enabled"]').checked;
        
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
        
        // Apply to body immediately for preview
        document.body.style.background = themes[backgroundTheme];
        document.body.style.animation = backgroundAnimation ? 'gradientShift 15s ease infinite' : 'none';
        
        // Update live preview
        const livePreview = document.querySelector('.live-preview');
        if (livePreview) {
            livePreview.innerHTML = `
                <h4>Background Preview Active!</h4>
                <p>Showing <strong>${backgroundTheme}</strong> theme</p>
                <small>Save to make this permanent</small>
            `;
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateBackgroundPreview();
        
        // Background change listeners
        document.querySelector('[name="background_opacity"]').addEventListener('change', updateBackgroundPreview);
        document.querySelector('[name="background_animation_enabled"]').addEventListener('change', updateBackgroundPreview);
        document.querySelector('[name="background_blur_enabled"]').addEventListener('change', updateBackgroundPreview);
    });

    // Reset preferences function
    function resetPreferences() {
        Swal.fire({
            title: 'Reset Preferences?',
            text: 'This will reset all your preferences to default values.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, reset preferences',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.preferences.reset") }}';
                
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
</script>
@endsection 