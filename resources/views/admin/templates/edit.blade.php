@extends('layouts.admin.admin')

@section('title', 'Edit Template')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-edit"></i>
                Edit Template: {{ $template->name }}
            </h1>
            <p class="page-subtitle">Modify template design and settings</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.templates.show', $template) }}" class="btn btn-info">
                <i class="fas fa-eye"></i>
                View Details
            </a>
            <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Templates
            </a>
        </div>
    </div>

    <div class="form-container">
        <form method="POST" action="{{ route('admin.templates.update', $template) }}" enctype="multipart/form-data" class="template-form">
            @csrf
            @method('PUT')
            
            <div class="form-section">
                <h3 class="section-title">Basic Information</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Template Name *</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $template->name) }}"
                               class="form-input @error('name') error @enderror" 
                               placeholder="e.g., Traditional Malaysian Songket" 
                               required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="category" class="form-label">Category *</label>
                        <select id="category" name="category" class="form-select @error('category') error @enderror" required>
                            <option value="">Choose Category</option>
                            <option value="traditional" {{ old('category', $template->category) == 'traditional' ? 'selected' : '' }}>Traditional</option>
                            <option value="modern" {{ old('category', $template->category) == 'modern' ? 'selected' : '' }}>Modern</option>
                            <option value="malaysian" {{ old('category', $template->category) == 'malaysian' ? 'selected' : '' }}>Malaysian</option>
                            <option value="elegant" {{ old('category', $template->category) == 'elegant' ? 'selected' : '' }}>Elegant</option>
                            <option value="minimalist" {{ old('category', $template->category) == 'minimalist' ? 'selected' : '' }}>Minimalist</option>
                            <option value="floral" {{ old('category', $template->category) == 'floral' ? 'selected' : '' }}>Floral</option>
                        </select>
                        @error('category')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-textarea @error('description') error @enderror" 
                              rows="3" 
                              placeholder="Brief description of the template design and style">{{ old('description', $template->description) }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="preview_image" class="form-label">Preview Image</label>
                        @if($template->preview_image)
                            <div class="current-image">
                                <img src="{{ Storage::url($template->preview_image) }}" alt="Current preview" class="preview-img">
                                <small class="form-help">Current preview image</small>
                            </div>
                        @endif
                        <input type="file" 
                               id="preview_image" 
                               name="preview_image" 
                               class="form-file @error('preview_image') error @enderror"
                               accept="image/*">
                        <small class="form-help">Upload a new preview image (JPG, PNG, GIF, max 2MB) - leave empty to keep current</small>
                        @error('preview_image')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" 
                                       name="is_malaysian_design" 
                                       value="1" 
                                       {{ old('is_malaysian_design', $template->is_malaysian_design) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                                Malaysian Design Template
                            </label>
                            <small class="form-help">Check if this template features Malaysian cultural elements</small>
                        </div>
                        
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                                Template is Active
                            </label>
                            <small class="form-help">Uncheck to temporarily disable this template</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Template Design</h3>
                
                <div class="form-group">
                    <label for="blade_template" class="form-label">Blade Template Code *</label>
                    <textarea id="blade_template" 
                              name="blade_template" 
                              class="form-code @error('blade_template') error @enderror" 
                              rows="15" 
                              placeholder="Enter your Blade template HTML and CSS code here..." 
                              required>{{ old('blade_template', $template->blade_template) }}</textarea>
                    @error('blade_template')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">
                        Use variables like: &#123;&#123; $details["bride_name"] &#125;&#125;, &#123;&#123; $details["groom_name"] &#125;&#125;, &#123;&#123; $details["wedding_date"] &#125;&#125;, etc.
                    </small>
                </div>
                

            </div>

            <div class="form-section">
                <h3 class="section-title">Full HTML Template (with Animations)</h3>
                
                <div class="form-group">
                    <label for="full_html_template" class="form-label">Complete HTML Document</label>
                    <textarea id="full_html_template" 
                              name="full_html_template" 
                              class="form-code @error('full_html_template') error @enderror" 
                              rows="20" 
                              placeholder="Enter the complete HTML document with CSS, JavaScript, and animations...">{{ old('full_html_template', $template->full_html_template) }}</textarea>
                    @error('full_html_template')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">
                        This should be a complete HTML document including &lt;!DOCTYPE html&gt;, &lt;html&gt;, &lt;head&gt;, &lt;body&gt; tags, all CSS, and JavaScript for animations. Use the same variables as in the Blade template above.
                    </small>
                </div>
                
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="copy_from_blade" onchange="copyFromBlade()">
                        <span class="checkmark"></span>
                        Copy from Blade template and wrap in HTML structure
                    </label>
                    <small class="form-help">Check this to automatically copy the Blade template content and wrap it in a basic HTML document structure</small>
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <button type="button" id="parseVariablesBtn" class="btn btn-info">
                        <i class="fas fa-magic"></i>
                        Parse Variables from Template
                    </button>
                    <button type="button" id="forceGalleryBtn" class="btn btn-secondary" style="margin-left: 10px;">
                        <i class="fas fa-images"></i>
                        Force Add Gallery Photos (1-6)
                    </button>
                    <small class="form-help">
                        Click to automatically detect variables from the <strong>Full HTML Template</strong> above and update default value fields below.
                        <br><strong>Date/Time Variables</strong> - Variables containing 'datetime' get datetime picker, 'date' get date picker, 'time' get time picker.
                        <br><strong>Loop Variables</strong> - The system will automatically detect loop patterns like 
                        <span style="font-family: monospace; background: #f1f5f9; padding: 2px 4px; border-radius: 3px;">gallery_photo_$i</span> 
                        in @@for loops and create numbered fields (e.g., gallery_photo_1, gallery_photo_2, etc.).
                        <br><strong>Image/Video Variables</strong> - Variables containing 'photo', 'image', 'story', 'video' etc. will automatically create file upload fields.
                        <br><strong>Audio/Song Variables</strong> - Variables containing 'song', 'audio', 'music' etc. will automatically create MP3 upload fields.
                        <br><strong>Force Gallery</strong> - If auto-detection fails, click "Force Add Gallery Photos" to manually add 6 gallery photo upload fields.
                    </small>
                </div>
            </div>

            <div class="form-section" id="defaultVariablesSection">
                <h3 class="section-title">
                    <i class="fas fa-cogs"></i>
                    Default Variable Values
                </h3>
                <p class="section-description">Set default values for the variables found in your template:</p>
                <div id="variableFields"></div>
                <input type="hidden" name="default_variables" id="default_variables_json" value="{{ json_encode($template->default_variables ?? []) }}">
                <input type="hidden" name="parse_variables_used" id="parse_variables_used" value="0">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Template
                </button>
                <a href="{{ route('admin.templates.preview', $template) }}" class="btn btn-info">
                    <i class="fas fa-eye"></i>
                    Preview Changes
                </a>
                <a href="{{ route('admin.templates.show', $template) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.page-content {
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.page-header-left {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.page-title {
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-subtitle {
    color: #718096;
    margin: 0;
}

.btn {
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    background: none;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-secondary {
    background: #718096;
    color: white;
}

.btn-info {
    background: #3182ce;
    color: white;
}

.form-container {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 1000px;
    margin: 0 auto;
}

.template-form {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.form-section {
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 25px;
}

.form-section:last-of-type {
    border-bottom: none;
    padding-bottom: 0;
}

.section-title {
    color: #2d3748;
    margin: 0 0 20px 0;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-description {
    color: #718096;
    margin: 0 0 20px 0;
    font-size: 14px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-label {
    color: #2d3748;
    font-weight: 500;
    font-size: 14px;
}

.form-input, .form-select, .form-textarea, .form-code, .form-file {
    padding: 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-input:focus, .form-select:focus, .form-textarea:focus, .form-code:focus, .form-file:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-code {
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.5;
}

.form-help {
    color: #718096;
    font-size: 12px;
    margin-top: -2px;
}

.error {
    border-color: #e53e3e;
}

.error-message {
    color: #e53e3e;
    font-size: 12px;
    margin-top: -2px;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 15px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #2d3748;
    cursor: pointer;
    font-size: 14px;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #667eea;
}

.current-image {
    margin-bottom: 10px;
}

.preview-img {
    max-width: 200px;
    max-height: 150px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    display: block;
    margin-bottom: 5px;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.variable-field {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 15px;
    align-items: center;
    margin-bottom: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.variable-label {
    font-weight: 500;
    color: #2d3748;
    font-size: 14px;
}

.variable-input {
    padding: 8px 12px;
    border: 1px solid #cbd5e0;
    border-radius: 6px;
    font-size: 14px;
}

.variable-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.variable-input-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.variable-file-input {
    padding: 8px 12px;
    border: 2px dashed #cbd5e0;
    border-radius: 6px;
    font-size: 14px;
    background: #f8fafc;
    cursor: pointer;
    transition: border-color 0.2s;
}

.variable-file-input:hover {
    border-color: #667eea;
    background: #edf2f7;
}

.image-preview {
    position: relative;
    display: inline-block;
    margin-top: 8px;
}

.btn-remove-image:hover {
    background: rgba(239, 68, 68, 1) !important;
}

.video-preview {
    position: relative;
    display: inline-block;
    margin-top: 8px;
}

.btn-remove-video {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    line-height: 1;
    transition: background 0.3s ease;
}

.btn-remove-video:hover {
    background: rgba(239, 68, 68, 1) !important;
}

.loop-variable-section {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 15px !important;
    margin-bottom: 20px !important;
}

.loop-variable-section h4 {
    color: #2d3748 !important;
    font-size: 16px !important;
    margin: 0 0 15px 0 !important;
    display: flex;
    align-items: center;
    gap: 8px;
}

.loop-variable-section .variable-field {
    background: white;
    border: 1px solid #cbd5e0;
    margin-bottom: 10px;
}

@media (max-width: 768px) {
    /* CRITICAL: Ensure header and burger button work properly */
    .header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 0 15px !important;
    }
    
    .header-left {
        display: flex !important;
        align-items: center !important;
        gap: 15px !important;
    }
    
    .menu-toggle {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .variable-field {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .form-actions {
        justify-content: stretch;
    }
    
    .form-actions .btn {
        flex: 1;
        justify-content: center;
    }
}
</style>

<script src="{{ asset('js/template-form.js') }}"></script>
<script>
// Load existing variables for edit form
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.loadExistingVariables === 'function') {
        window.loadExistingVariables();
    }
});

function copyFromBlade() {
    const checkbox = document.getElementById('copy_from_blade');
    const bladeContent = document.getElementById('blade_template').value.trim();
    const fullHtmlTextarea = document.getElementById('full_html_template');
    
    if (checkbox.checked && bladeContent) {
        const htmlTemplate = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Invitation - {{ $details["bride_name"] ?? "Bride" }} & {{ $details["groom_name"] ?? "Groom" }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add your animations and enhanced styles here */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            overflow-x: hidden;
        }
        
        /* Example animations - customize as needed */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        .fade-in.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Add more animations here */
    </style>
</head>
<body>
    ${bladeContent}
    
    <scr` + `ipt>
        // Add your JavaScript animations here
        document.addEventListener('DOMContentLoaded', function() {
            // Example scroll animation
            const fadeElements = document.querySelectorAll('.fade-in');
            
            const scrollAnimation = () => {
                fadeElements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const elementVisible = 150;
                    
                    if (elementTop < window.innerHeight - elementVisible) {
                        element.classList.add('active');
                    }
                });
            };
            
            window.addEventListener('scroll', scrollAnimation);
            scrollAnimation(); // Run once on load
        });
    </scr` + `ipt>
</body>
</html>`;
        
        fullHtmlTextarea.value = htmlTemplate;
    } else if (!checkbox.checked) {
        // Optionally clear the full HTML when unchecked
        // fullHtmlTextarea.value = '';
    }
}
</script>
@endsection 