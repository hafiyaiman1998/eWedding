@extends('layouts.admin.admin')

@section('title', 'Preview Template')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-eye"></i>
                Preview: {{ $template->name }}
            </h1>
            <p class="page-subtitle">See how the template looks with sample data</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i>
                Edit Template
            </a>
            <a href="{{ route('admin.templates.show', $template) }}" class="btn btn-info">
                <i class="fas fa-info-circle"></i>
                View Details
            </a>
            <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Templates
            </a>
        </div>
    </div>

    <div class="preview-container">
        <!-- Template Information -->
        <div class="template-info-card">
            <div class="info-header">
                <div class="info-left">
                    <h3 class="template-title">{{ $template->name }}</h3>
                    <div class="template-meta">
                        <span class="badge badge-{{ $template->category }}">{{ ucfirst($template->category) }}</span>
                        @if($template->is_malaysian_design)
                            <span class="badge badge-malaysian">
                                <i class="fas fa-star-and-crescent"></i>
                                Malaysian Design
                            </span>
                        @endif
                        <span class="badge badge-{{ $template->is_active ? 'active' : 'inactive' }}">
                            {{ $template->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    @if($template->description)
                        <p class="template-description">{{ $template->description }}</p>
                    @endif
                </div>
                <div class="info-right">
                    <div class="usage-stats">
                        <div class="stat">
                            <span class="stat-number">{{ $template->weddingCards->count() }}</span>
                            <span class="stat-label">Cards Created</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Preview -->
        <div class="preview-card">
            <div class="preview-header">
                <h4>
                    <i class="fas fa-desktop"></i>
                    Template Preview
                </h4>
                <div class="preview-actions">
                    @if($template->full_html_template)
                        <a href="{{ route('admin.templates.full-preview', $template) }}" 
                           target="_blank" 
                           class="btn btn-sm btn-success">
                            <i class="fas fa-play"></i>
                            View with Animations
                        </a>
                    @endif
                    <button class="btn btn-sm btn-outline" onclick="togglePreviewMode()">
                        <i class="fas fa-mobile-alt"></i>
                        Toggle Mobile View
                    </button>
                    <button class="btn btn-sm btn-outline" onclick="printPreview()">
                        <i class="fas fa-print"></i>
                        Print Preview
                    </button>
                </div>
            </div>
            
            <div class="preview-viewport" id="previewViewport">
                <div class="preview-content" id="previewContent">
                    @php
                        $details = $previewData;
                        
                        // Create a safe rendering environment
                        $templateContent = $template->blade_template;
                        
                        // Handle PHP functions like strtoupper() with Blade variables
                        $templateContent = preg_replace_callback(
                            '/\{\{\s*strtoupper\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\)\s*\}\}/',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $fallback = $matches[2];
                                return strtoupper($previewData[$key] ?? $fallback);
                            },
                            $templateContent
                        );
                        
                        // Handle date() function
                        $templateContent = preg_replace_callback(
                            '/\{\{\s*date\("([^"]+)"\)\s*\}\}/',
                            function($matches) {
                                $format = $matches[1];
                                return date($format);
                            },
                            $templateContent
                        );
                        
                        // Handle complex Blade variables with multiple fallbacks (mixed quotes)
                        $templateContent = preg_replace_callback(
                            '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\'([^\']*)\'\s*\}\}/',
                            function($matches) use ($previewData) {
                                $key1 = $matches[1];
                                $key2 = $matches[2];
                                $fallback = $matches[3];
                                return $previewData[$key1] ?? $previewData[$key2] ?? $fallback;
                            },
                            $templateContent
                        );
                        
                        // Handle complex Blade variables with multiple fallbacks (double quotes)
                        $templateContent = preg_replace_callback(
                            '/\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
                            function($matches) use ($previewData) {
                                $key1 = $matches[1];
                                $key2 = $matches[2];
                                $fallback = $matches[3];
                                return $previewData[$key1] ?? $previewData[$key2] ?? $fallback;
                            },
                            $templateContent
                        );
                        
                        // Handle Blade variables with null coalescing operator (double quotes)
                        $templateContent = preg_replace_callback(
                            '/\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $fallback = $matches[2];
                                return $previewData[$key] ?? $fallback;
                            },
                            $templateContent
                        );
                        
                        // Handle Blade variables with null coalescing operator (single quotes)
                        $templateContent = preg_replace_callback(
                            '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\'([^\']*)\'\s*\}\}/',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $fallback = $matches[2];
                                return $previewData[$key] ?? $fallback;
                            },
                            $templateContent
                        );
                        
                        // Handle simple Blade variables without fallback (double quotes)
                        $templateContent = preg_replace_callback(
                            '/\{\{\s*\$details\["([^"]+)"\]\s*\}\}/',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                return $previewData[$key] ?? '';
                            },
                            $templateContent
                        );
                        
                        // Handle simple Blade variables without fallback (single quotes)
                        $templateContent = preg_replace_callback(
                            '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\}\}/',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                return $previewData[$key] ?? '';
                            },
                            $templateContent
                        );
                        
                        // Process @if/@else/@endif blocks FIRST before simple @if blocks
                        // This prevents the simple @if processing from breaking the @if/@else structure
                        
                        // Handle @if/@else/@endif statements (double quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(\$details\["([^"]+)"\]\s*\?\?\s*false\)(.*?)@else(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $ifContent = $matches[2];
                                $elseContent = $matches[3];
                                return !empty($previewData[$key]) ? $ifContent : $elseContent;
                            },
                            $templateContent
                        );
                        
                        // Handle @if/@else/@endif statements (single quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(\$details\[\'([^\']+)\'\]\s*\?\?\s*false\)(.*?)@else(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $ifContent = $matches[2];
                                $elseContent = $matches[3];
                                return !empty($previewData[$key]) ? $ifContent : $elseContent;
                            },
                            $templateContent
                        );
                        
                        // Handle @if/@else/@endif statements with isset() (double quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(isset\(\$details\["([^"]+)"\]\)\)(.*?)@else(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $ifContent = $matches[2];
                                $elseContent = $matches[3];
                                return (isset($previewData[$key]) && !empty($previewData[$key])) ? $ifContent : $elseContent;
                            },
                            $templateContent
                        );
                        
                        // Handle @if/@else/@endif statements with isset() (single quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(isset\(\$details\[\'([^\']+)\'\]\)\)(.*?)@else(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $ifContent = $matches[2];
                                $elseContent = $matches[3];
                                return (isset($previewData[$key]) && !empty($previewData[$key])) ? $ifContent : $elseContent;
                            },
                            $templateContent
                        );
                        
                        // Handle simple @if/@else/@endif statements (double quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(\$details\["([^"]+)"\]\)(.*?)@else(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $ifContent = $matches[2];
                                $elseContent = $matches[3];
                                return !empty($previewData[$key]) ? $ifContent : $elseContent;
                            },
                            $templateContent
                        );
                        
                        // Handle simple @if/@else/@endif statements (single quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(\$details\[\'([^\']+)\'\]\)(.*?)@else(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $ifContent = $matches[2];
                                $elseContent = $matches[3];
                                return !empty($previewData[$key]) ? $ifContent : $elseContent;
                            },
                            $templateContent
                        );
                        
                        // After processing @if/@else/@endif blocks, handle remaining simple @if blocks
                        
                        // Handle @if statements with OR conditions (double quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(\$details\["([^"]+)"\]\s*\|\|\s*\$details\["([^"]+)"\]\)(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key1 = $matches[1];
                                $key2 = $matches[2];
                                $content = $matches[3];
                                return (!empty($previewData[$key1]) || !empty($previewData[$key2])) ? $content : '';
                            },
                            $templateContent
                        );
                        
                        // Handle conditional @if statements with ?? false (double quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(\$details\["([^"]+)"\]\s*\?\?\s*false\)(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $content = $matches[2];
                                return !empty($previewData[$key]) ? $content : '';
                            },
                            $templateContent
                        );
                        
                        // Handle conditional @if statements with ?? false (single quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(\$details\[\'([^\']+)\'\]\s*\?\?\s*false\)(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $content = $matches[2];
                                return !empty($previewData[$key]) ? $content : '';
                            },
                            $templateContent
                        );
                        
                        // Handle @if(isset()) statements (double quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(isset\(\$details\["([^"]+)"\]\)\)(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $content = $matches[2];
                                return isset($previewData[$key]) && !empty($previewData[$key]) ? $content : '';
                            },
                            $templateContent
                        );
                        
                        // Handle @if(isset()) statements (single quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(isset\(\$details\[\'([^\']+)\'\]\)\)(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $content = $matches[2];
                                return isset($previewData[$key]) && !empty($previewData[$key]) ? $content : '';
                            },
                            $templateContent
                        );
                        
                        // Handle simple @if statements (double quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(\$details\["([^"]+)"\]\)(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $content = $matches[2];
                                return !empty($previewData[$key]) ? $content : '';
                            },
                            $templateContent
                        );
                        
                        // Handle simple @if statements (single quotes)
                        $templateContent = preg_replace_callback(
                            '/@if\(\$details\[\'([^\']+)\'\]\)(.*?)@endif/s',
                            function($matches) use ($previewData) {
                                $key = $matches[1];
                                $content = $matches[2];
                                return !empty($previewData[$key]) ? $content : '';
                            },
                            $templateContent
                        );
                        
                        // Clean up any remaining orphaned @endif statements
                        $templateContent = str_replace('@endif', '', $templateContent);
                        
                        // Clean up any remaining orphaned @if statements
                        $templateContent = preg_replace('/@if\([^)]+\)/', '', $templateContent);
                        
                        // Clean up any remaining orphaned @else statements
                        $templateContent = str_replace('@else', '', $templateContent);
                        
                        // Handle @for loops for gallery items
                        $templateContent = preg_replace_callback(
                            '/@for\(\$i\s*=\s*(\d+);\s*\$i\s*<=\s*(\d+);\s*\$i\+\+\)(.*?)@endfor/s',
                            function($matches) use ($previewData) {
                                $start = (int)$matches[1];
                                $end = (int)$matches[2];
                                $content = $matches[3];
                                $result = '';
                                
                                for ($i = $start; $i <= $end; $i++) {
                                    $iterationContent = $content;
                                    $iterationContent = str_replace('$i', $i, $iterationContent);
                                    
                                    // Handle gallery photo variables
                                    $iterationContent = preg_replace_callback(
                                        '/\{\{\s*\$details\["gallery_photo_"\s*\.\s*\$i\]\s*\?\?\s*false\s*\}\}/',
                                        function($m) use ($previewData, $i) {
                                            return $previewData["gallery_photo_$i"] ?? false;
                                        },
                                        $iterationContent
                                    );
                                    
                                    $result .= $iterationContent;
                                }
                                
                                return $result;
                            },
                            $templateContent
                        );
                        
                        // Handle any remaining {{ date('Y') }} expressions without quotes  
                        $templateContent = preg_replace_callback(
                            '/\{\{\s*date\(\'([^\']+)\'\)\s*\}\}/',
                            function($matches) {
                                $format = $matches[1];
                                return date($format);
                            },
                            $templateContent
                        );
                        
                        // Handle complex expressions like {{ date('Y') }} {{ $details["groom_name"] }}
                        $templateContent = preg_replace_callback(
                            '/\{\{\s*date\(\'([^\']+)\'\)\s*\}\}\s*\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
                            function($matches) use ($previewData) {
                                $dateFormat = $matches[1];
                                $key = $matches[2];
                                $fallback = $matches[3];
                                return date($dateFormat) . ' ' . ($previewData[$key] ?? $fallback);
                            },
                            $templateContent
                        );
                        
                        // Clean up any remaining Blade syntax that might cause issues
                        $templateContent = str_replace('<?php', '', $templateContent);
                        $templateContent = str_replace('?>', '', $templateContent);
                        
                        // Remove any leftover curly braces that might be malformed
                        $templateContent = preg_replace('/\{\{\s*\}\}/', '', $templateContent);
                        
                        // Debug what's in previewData for images
                        $hasGroomPhoto = !empty($previewData['groom_photo']);
                        $hasBridePhoto = !empty($previewData['bride_photo']);
                        
                        // Add CSS to ensure proper image display and hide any leftover placeholders
                        $templateContent = '<style>
                            .photo-frame { position: relative; overflow: hidden; }
                            .photo-frame img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 2; }
                            .photo-frame .photo-placeholder, 
                            .photo-frame div[style*="background: #f0f0f0"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; }
                            .photo-frame img + div { display: none !important; }
                            .photo-frame img + .photo-placeholder { display: none !important; }
                        </style>
                        <!-- Debug: Groom Photo: ' . ($hasGroomPhoto ? 'YES' : 'NO') . ', Bride Photo: ' . ($hasBridePhoto ? 'YES' : 'NO') . ' -->' . $templateContent;
                        
                        echo $templateContent;
                    @endphp
                </div>
            </div>
        </div>

        <!-- Template Code -->
        <div class="code-card">
            <div class="code-header">
                <h4>
                    <i class="fas fa-code"></i>
                    Template Code
                </h4>
                <button class="btn btn-sm btn-outline" onclick="copyCode()">
                    <i class="fas fa-copy"></i>
                    Copy Code
                </button>
            </div>
            <div class="code-content">
                <pre class="template-code" id="templateCode">{{ $template->blade_template }}</pre>
            </div>
        </div>

        <!-- Sample Data Display -->
        <div class="data-card">
            <div class="data-header">
                <h4>
                    <i class="fas fa-database"></i>
                    Preview Data Used
                </h4>
            </div>
            <div class="data-content">
                <div class="data-grid">
                    @foreach($previewData as $key => $value)
                        <div class="data-item">
                            <span class="data-key">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                            <span class="data-value">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions-section">
            <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Edit This Template
            </a>
            
            @if($template->weddingCards->count() == 0)
                <form method="POST" action="{{ route('admin.templates.destroy', $template) }}" 
                      class="delete-form" 
                      data-delete-type="template" 
                      data-delete-name="{{ $template->name }}"
                      data-delete-warning="This will affect {{ $template->wedding_cards_count }} wedding cards."
                      style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger delete-btn">
                        <i class="fas fa-trash"></i>
                        Delete Template
                    </button>
                </form>
            @else
                <button class="btn btn-danger" disabled title="Cannot delete template with existing wedding cards">
                    <i class="fas fa-lock"></i>
                    Template In Use ({{ $template->weddingCards->count() }} cards)
                </button>
            @endif
        </div>
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

.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.btn-secondary { background: #718096; color: white; }
.btn-info { background: #3182ce; color: white; }
.btn-warning { background: #d69e2e; color: white; }
.btn-danger { background: #e53e3e; color: white; }
.btn-success { background: #38a169; color: white; }
.btn-outline { background: transparent; color: #667eea; border: 2px solid #667eea; }
.btn-sm { padding: 6px 10px; font-size: 12px; }

.preview-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.template-info-card, .preview-card, .code-card, .data-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.info-header, .preview-header, .code-header, .data-header {
    background: #f7fafc;
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-header h3, .preview-header h4, .code-header h4, .data-header h4 {
    margin: 0;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 10px;
}

.template-title {
    font-size: 1.3rem;
    margin: 0 0 10px 0;
}

.template-meta {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.template-description {
    color: #4a5568;
    margin: 0;
    line-height: 1.5;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.badge-traditional { background: #fef5e7; color: #d69e2e; }
.badge-modern { background: #e6fffa; color: #319795; }
.badge-malaysian { background: #fef5e7; color: #d69e2e; }
.badge-elegant { background: #faf5ff; color: #805ad5; }
.badge-minimalist { background: #f0fff4; color: #38a169; }
.badge-floral { background: #fed7d7; color: #e53e3e; }
.badge-active { background: #f0fff4; color: #38a169; }
.badge-inactive { background: #fed7d7; color: #e53e3e; }

.usage-stats {
    text-align: center;
}

.stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
}

.stat-label {
    color: #718096;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.preview-viewport {
    background: #f7fafc;
    padding: 40px;
    min-height: 400px;
    transition: all 0.3s ease;
}

.preview-viewport.mobile {
    max-width: 400px;
    margin: 0 auto;
}

.preview-content {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.code-content {
    background: #1a202c;
    padding: 20px;
    overflow-x: auto;
}

.template-code {
    color: #e2e8f0;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.5;
    margin: 0;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.data-content {
    padding: 20px;
}

.data-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.data-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 15px;
    background: #f7fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.data-key {
    color: #4a5568;
    font-weight: 500;
    font-size: 14px;
}

.data-value {
    color: #2d3748;
    font-size: 16px;
}

.actions-section {
    display: flex;
    gap: 15px;
    justify-content: center;
    padding: 30px 0;
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
    
    .page-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .info-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .preview-header, .code-header, .data-header {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
    
    .data-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-section {
        flex-direction: column;
    }
}
</style>

<script>
let isMobileView = false;

function togglePreviewMode() {
    const viewport = document.getElementById('previewViewport');
    isMobileView = !isMobileView;
    
    if (isMobileView) {
        viewport.classList.add('mobile');
    } else {
        viewport.classList.remove('mobile');
    }
}

function printPreview() {
    const previewContent = document.getElementById('previewContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>{{ $template->name }} - Preview</title>
                <style>
                    body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                    .preview-content { max-width: 800px; margin: 0 auto; }
                </style>
            </head>
            <body>
                <div class="preview-content">
                    ${previewContent}
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function copyCode() {
    const code = document.getElementById('templateCode').textContent;
    navigator.clipboard.writeText(code).then(() => {
        // Visual feedback
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Copied!';
        button.style.background = '#38a169';
        button.style.color = 'white';
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = '';
            button.style.color = '';
        }, 2000);
    });
}
</script>
@endsection 