@extends('layouts.admin.admin')

@section('title', 'Design Templates')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-th-large"></i>
                Design Templates
            </h1>
            <p class="page-subtitle">Manage wedding card design templates</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i>
                Create New Template
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.templates.index') }}" class="filter-form">
            <div class="filter-group">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search templates..." class="form-input">
                
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="malaysian" value="1" {{ request('malaysian') ? 'checked' : '' }}>
                    Malaysian Designs Only
                </label>
                
                <button type="submit" class="btn btn-search">
                    <i class="fas fa-search"></i>
                    Filter
                </button>
                
                @if(request()->hasAny(['search', 'category', 'malaysian']))
                    <a href="{{ route('admin.templates.index') }}" class="btn btn-clear">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Templates Grid -->
    <div class="templates-container">
        @if($templates->count() > 0)
            <div class="templates-grid">
                @foreach($templates as $template)
                    <div class="template-card">
                        <div class="template-header">
                            <div class="template-info">
                                <h3 class="template-name">{{ $template->name }}</h3>
                                <p class="template-description">{{ Str::limit($template->description, 80) }}</p>
                            </div>
                            <div class="template-badges">
                                @if($template->is_malaysian_design)
                                    <span class="badge badge-malaysian">
                                        <i class="fas fa-star-and-crescent"></i>
                                        Malaysian
                                    </span>
                                @endif
                                <span class="badge badge-category">{{ ucfirst($template->category) }}</span>
                            </div>
                        </div>
                        
                        <div class="template-preview">
                            @if($template->preview_image)
                                <img src="{{ Storage::url($template->preview_image) }}" alt="{{ $template->name }}" class="preview-image">
                            @else
                                <div class="preview-placeholder">
                                    <i class="fas fa-image"></i>
                                    <span>No Preview</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="template-stats">
                            <div class="stat">
                                <i class="fas fa-heart"></i>
                                <span>{{ $template->wedding_cards_count }} cards</span>
                            </div>
                            <div class="stat">
                                <i class="fas fa-{{ $template->is_active ? 'check-circle' : 'pause-circle' }}"></i>
                                <span>{{ $template->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>
                        
                        <div class="template-actions">
                            <a href="{{ route('admin.templates.preview', $template) }}" 
                               class="btn btn-sm btn-info" title="Preview">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.templates.show', $template) }}" 
                               class="btn btn-sm btn-primary" title="View Details">
                                <i class="fas fa-info-circle"></i>
                            </a>
                            <a href="{{ route('admin.templates.edit', $template) }}" 
                               class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.templates.destroy', $template) }}" 
                                  class="delete-form" 
                                  data-delete-type="template" 
                                  data-delete-name="{{ $template->name }}"
                                  data-delete-warning="This will affect {{ $template->wedding_cards_count }} wedding cards."
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger delete-btn" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $templates->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-th-large"></i>
                </div>
                <h3>No Templates Found</h3>
                <p>
                    @if(request()->hasAny(['search', 'category', 'malaysian']))
                        No templates match your filter criteria.
                    @else
                        You haven't created any design templates yet.
                    @endif
                </p>
                <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Create First Template
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.page-content {
    padding: 20px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow-x: hidden;
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

.btn-sm {
    padding: 6px 10px;
    font-size: 12px;
}

.btn-info { background: #3182ce; color: white; }
.btn-warning { background: #d69e2e; color: white; }
.btn-danger { background: #e53e3e; color: white; }
.btn-search { background: #4299e1; color: white; }
.btn-clear { background: #718096; color: white; }

.filters-section {
    margin-bottom: 25px;
}

.filter-form {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.form-input, .form-select {
    padding: 8px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 14px;
}

.form-input {
    min-width: 200px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #4a5568;
}

.templates-container {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.template-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    background: white;
    transition: transform 0.2s, box-shadow 0.2s;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.template-header {
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.template-name {
    color: #2d3748;
    margin: 0 0 8px 0;
    font-size: 1.2rem;
}

.template-description {
    color: #718096;
    margin: 0 0 15px 0;
    font-size: 14px;
    line-height: 1.4;
}

.template-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.badge-malaysian {
    background: #fef5e7;
    color: #d69e2e;
}

.badge-category {
    background: #e6fffa;
    color: #319795;
}

.template-preview {
    height: 200px;
    background: #f7fafc;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-placeholder {
    text-align: center;
    color: #a0aec0;
}

.preview-placeholder i {
    font-size: 3rem;
    margin-bottom: 10px;
}

.template-stats {
    padding: 15px 20px;
    background: #f7fafc;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #e2e8f0;
}

.stat {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #718096;
    font-size: 13px;
}

.template-actions {
    padding: 15px 20px;
    display: flex;
    gap: 8px;
    justify-content: center;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #718096;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-state h3 {
    color: #2d3748;
    margin-bottom: 10px;
}

.empty-state p {
    margin-bottom: 25px;
}

/* Responsive adjustments for templates page */
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
    
    .page-content {
        padding: 15px 10px !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    
    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: center;
        margin-bottom: 25px;
        text-align: center;
    }
    
    .page-title {
        font-size: 1.5rem;
        flex-direction: column;
        gap: 8px;
        text-align: center;
    }
    
    .page-subtitle {
        font-size: 1rem;
        text-align: center;
    }
    
    .page-header-right {
        width: 100%;
        display: flex;
        justify-content: center;
    }
    
    .filter-form {
        padding: 15px;
    }
    
    .filter-group {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    
    .form-input {
        min-width: 0;
        width: 100%;
    }
    
    .form-select {
        width: 100%;
    }
    
    .checkbox-label {
        justify-content: center;
        padding: 8px;
        background: #f7fafc;
        border-radius: 6px;
    }
    
    .btn-search, .btn-clear {
        width: 100%;
        justify-content: center;
    }
    
    .templates-container {
        padding: 15px;
    }
    
    .templates-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .template-card {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    
    .template-header {
        padding: 15px;
    }
    
    .template-name {
        font-size: 1.1rem;
    }
    
    .template-preview {
        height: 150px;
    }
    
    .template-stats {
        padding: 12px 15px;
        flex-direction: column;
        gap: 8px;
        align-items: center;
    }
    
    .template-actions {
        padding: 12px 15px;
        flex-wrap: wrap;
        gap: 6px;
    }
    
    .empty-state {
        padding: 40px 15px;
    }
    
    .empty-icon {
        font-size: 3rem;
    }
}

@media (max-width: 480px) {
    .page-content {
        padding: 10px 5px !important;
        width: 100% !important;
        max-width: 100vw !important;
        overflow-x: hidden !important;
    }
    
    .page-title {
        font-size: 1.3rem;
    }
    
    .page-subtitle {
        font-size: 0.9rem;
    }
    
    .filter-form {
        padding: 12px;
    }
    
    .templates-container {
        padding: 12px;
    }
    
    .template-header {
        padding: 12px;
    }
    
    .template-name {
        font-size: 1rem;
    }
    
    .template-description {
        font-size: 13px;
    }
    
    .template-preview {
        height: 120px;
    }
    
    .preview-placeholder i {
        font-size: 2rem;
    }
    
    .template-stats {
        padding: 10px 12px;
    }
    
    .stat {
        font-size: 12px;
    }
    
    .template-actions {
        padding: 10px 12px;
    }
    
    .btn-sm {
        padding: 4px 8px;
        font-size: 11px;
    }
    
    .badge {
        padding: 3px 6px;
        font-size: 10px;
    }
    
    .form-input, .form-select {
        padding: 6px 10px;
        font-size: 13px;
    }
    
    .empty-state h3 {
        font-size: 1.2rem;
    }
    
    .empty-state p {
        font-size: 0.9rem;
    }
}
</style>
@endsection 