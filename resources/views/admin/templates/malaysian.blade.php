@extends('layouts.admin.admin')

@section('title', 'Malaysian Design Templates')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-star-and-crescent"></i>
                Malaysian Design Templates
            </h1>
            <p class="page-subtitle">Traditional and cultural wedding card designs</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i>
                Create Malaysian Template
            </a>
            <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-th-large"></i>
                All Templates
            </a>
        </div>
    </div>

    <!-- Malaysian Templates Info -->
    <div class="info-banner">
        <div class="banner-content">
            <div class="banner-icon">
                <i class="fas fa-mosque"></i>
            </div>
            <div class="banner-text">
                <h3>Malaysian Cultural Heritage</h3>
                <p>Showcase beautiful Malaysian wedding traditions with authentic designs featuring Songket patterns, batik motifs, and traditional color schemes.</p>
            </div>
            <div class="banner-stats">
                <div class="stat">
                    <span class="stat-number">{{ $templates->total() }}</span>
                    <span class="stat-label">Malaysian Templates</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="templates-container">
        @if($templates->count() > 0)
            <div class="templates-grid">
                @foreach($templates as $template)
                    <div class="template-card malaysian-card">
                        <div class="template-header">
                            <div class="template-info">
                                <h3 class="template-name">{{ $template->name }}</h3>
                                <p class="template-description">{{ Str::limit($template->description, 80) }}</p>
                            </div>
                            <div class="template-badges">
                                <span class="badge badge-malaysian">
                                    <i class="fas fa-star-and-crescent"></i>
                                    Malaysian
                                </span>
                                <span class="badge badge-category">{{ ucfirst($template->category) }}</span>
                            </div>
                        </div>
                        
                        <div class="template-preview">
                            @if($template->preview_image)
                                <img src="{{ Storage::url($template->preview_image) }}" alt="{{ $template->name }}" class="preview-image">
                            @else
                                <div class="preview-placeholder malaysian-placeholder">
                                    <i class="fas fa-mosque"></i>
                                    <span>Malaysian Design</span>
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
                    <i class="fas fa-star-and-crescent"></i>
                </div>
                <h3>No Malaysian Templates Found</h3>
                <p>You haven't created any Malaysian design templates yet. Start by creating beautiful traditional designs.</p>
                <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Create First Malaysian Template
                </a>
            </div>
        @endif
    </div>

    <!-- Cultural Design Ideas -->
    <div class="ideas-section">
        <h3 class="section-title">
            <i class="fas fa-lightbulb"></i>
            Malaysian Design Inspirations
        </h3>
        <div class="ideas-grid">
            <div class="idea-card">
                <div class="idea-icon">
                    <i class="fas fa-gem"></i>
                </div>
                <h4>Songket Patterns</h4>
                <p>Traditional woven fabric designs with gold and silver threads</p>
            </div>
            <div class="idea-card">
                <div class="idea-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h4>Batik Motifs</h4>
                <p>Traditional Malaysian batik patterns and botanical designs</p>
            </div>
            <div class="idea-card">
                <div class="idea-icon">
                    <i class="fas fa-mosque"></i>
                </div>
                <h4>Islamic Art</h4>
                <p>Beautiful geometric patterns and Arabic calligraphy elements</p>
            </div>
            <div class="idea-card">
                <div class="idea-icon">
                    <i class="fas fa-palette"></i>
                </div>
                <h4>Traditional Colors</h4>
                <p>Gold, maroon, royal blue, and emerald green color schemes</p>
            </div>
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

.btn-primary {
    background: linear-gradient(135deg, #d69e2e 0%, #b7791f 100%);
    color: white;
}

.btn-secondary {
    background: #718096;
    color: white;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 12px;
}

.btn-info { background: #3182ce; color: white; }
.btn-warning { background: #d69e2e; color: white; }
.btn-danger { background: #e53e3e; color: white; }

.info-banner {
    background: linear-gradient(135deg, #fef5e7 0%, #f7fafc 100%);
    border: 2px solid #d69e2e;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 30px;
}

.banner-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.banner-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #d69e2e 0%, #b7791f 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.banner-text {
    flex: 1;
}

.banner-text h3 {
    color: #744210;
    margin: 0 0 8px 0;
    font-size: 1.3rem;
}

.banner-text p {
    color: #8d5524;
    margin: 0;
    line-height: 1.5;
}

.banner-stats {
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
    color: #744210;
}

.stat-label {
    color: #8d5524;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.templates-container {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
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

.malaysian-card {
    border: 2px solid #fed7aa;
    background: linear-gradient(135deg, #fffaf0 0%, #fefefe 100%);
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(214, 158, 46, 0.15);
}

.template-header {
    padding: 20px;
    border-bottom: 1px solid #fed7aa;
}

.template-name {
    color: #744210;
    margin: 0 0 8px 0;
    font-size: 1.2rem;
}

.template-description {
    color: #8d5524;
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
    border: 1px solid #d69e2e;
}

.badge-category {
    background: #e6fffa;
    color: #319795;
}

.template-preview {
    height: 200px;
    background: #fffaf0;
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

.malaysian-placeholder {
    color: #d69e2e;
}

.preview-placeholder i {
    font-size: 3rem;
    margin-bottom: 10px;
}

.template-stats {
    padding: 15px 20px;
    background: #fffaf0;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #fed7aa;
}

.template-stats .stat {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #8d5524;
    font-size: 13px;
}

.template-actions {
    padding: 15px 20px;
    display: flex;
    gap: 8px;
    justify-content: center;
    background: #fffaf0;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #8d5524;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    color: #d69e2e;
}

.empty-state h3 {
    color: #744210;
    margin-bottom: 10px;
}

.empty-state p {
    margin-bottom: 25px;
}

.ideas-section {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.section-title {
    color: #744210;
    margin: 0 0 25px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.2rem;
}

.ideas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.idea-card {
    padding: 20px;
    border: 2px solid #fed7aa;
    border-radius: 12px;
    text-align: center;
    background: linear-gradient(135deg, #fffaf0 0%, #fefefe 100%);
    transition: transform 0.2s;
}

.idea-card:hover {
    transform: translateY(-2px);
}

.idea-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #d69e2e 0%, #b7791f 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    margin: 0 auto 15px;
}

.idea-card h4 {
    color: #744210;
    margin: 0 0 10px 0;
    font-size: 1.1rem;
}

.idea-card p {
    color: #8d5524;
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
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
    
    .banner-content {
        flex-direction: column;
        text-align: center;
    }
    
    .templates-grid {
        grid-template-columns: 1fr;
    }
    
    .ideas-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection 