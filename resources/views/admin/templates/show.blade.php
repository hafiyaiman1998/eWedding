@extends('layouts.admin.admin')

@section('title', 'Template Details')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-info-circle"></i>
                {{ $template->name }}
            </h1>
            <p class="page-subtitle">Template details and usage statistics</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.templates.preview', $template) }}" class="btn btn-info">
                <i class="fas fa-eye"></i>
                Preview Template
            </a>
            <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i>
                Edit Template
            </a>
            <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Templates
            </a>
        </div>
    </div>

    <div class="details-container">
        <!-- Template Information -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info"></i>
                    Template Information
                </h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label class="info-label">Template Name:</label>
                        <span class="info-value">{{ $template->name }}</span>
                    </div>
                    
                    <div class="info-item">
                        <label class="info-label">Category:</label>
                        <span class="info-value">
                            <span class="badge badge-category">{{ ucfirst($template->category) }}</span>
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <label class="info-label">Status:</label>
                        <span class="info-value">
                            <span class="badge {{ $template->is_active ? 'badge-active' : 'badge-inactive' }}">
                                <i class="fas fa-{{ $template->is_active ? 'check-circle' : 'pause-circle' }}"></i>
                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <label class="info-label">Malaysian Design:</label>
                        <span class="info-value">
                            @if($template->is_malaysian_design)
                                <span class="badge badge-malaysian">
                                    <i class="fas fa-star-and-crescent"></i>
                                    Yes
                                </span>
                            @else
                                <span class="badge badge-general">No</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <label class="info-label">Created:</label>
                        <span class="info-value">{{ $template->created_at->format('M d, Y \a\t H:i') }}</span>
                    </div>
                    
                    <div class="info-item">
                        <label class="info-label">Last Updated:</label>
                        <span class="info-value">{{ $template->updated_at->format('M d, Y \a\t H:i') }}</span>
                    </div>
                </div>
                
                @if($template->description)
                <div class="description-section">
                    <label class="info-label">Description:</label>
                    <p class="description-text">{{ $template->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Preview Image -->
        @if($template->preview_image)
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-image"></i>
                    Preview Image
                </h3>
            </div>
            <div class="card-body">
                <div class="preview-container">
                    <img src="{{ Storage::url($template->preview_image) }}" 
                         alt="{{ $template->name }}" 
                         class="template-preview-image">
                </div>
            </div>
        </div>
        @endif

        <!-- Usage Statistics -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    Usage Statistics
                </h3>
            </div>
            <div class="card-body">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $template->weddingCards->count() }}</div>
                            <div class="stat-label">Total Wedding Cards</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $template->weddingCards->where('is_published', true)->count() }}</div>
                            <div class="stat-label">Published Cards</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $template->weddingCards->where('is_published', false)->count() }}</div>
                            <div class="stat-label">Draft Cards</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $template->weddingCards->unique('user_id')->count() }}</div>
                            <div class="stat-label">Unique Users</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Wedding Cards -->
        @if($template->weddingCards->count() > 0)
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-heart"></i>
                    Recent Wedding Cards
                </h3>
            </div>
            <div class="card-body">
                <div class="cards-list">
                    @foreach($template->weddingCards->take(5) as $card)
                        <div class="card-item">
                            <div class="card-info">
                                <h4 class="card-title">{{ $card->title }}</h4>
                                <p class="card-details">
                                    {{ $card->card_details['bride_name'] ?? 'N/A' }} & {{ $card->card_details['groom_name'] ?? 'N/A' }}
                                </p>
                                <small class="card-date">
                                    Created by {{ $card->user->name }} on {{ $card->created_at->format('M d, Y') }}
                                </small>
                            </div>
                            <div class="card-status">
                                <span class="badge {{ $card->is_published ? 'badge-published' : 'badge-draft' }}">
                                    {{ $card->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($template->weddingCards->count() > 5)
                    <div class="view-all">
                        <a href="{{ route('admin.cards.index', ['template' => $template->id]) }}" class="btn btn-outline">
                            View All {{ $template->weddingCards->count() }} Cards
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Default Variables -->
        @if($template->default_variables && count($template->default_variables) > 0)
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-code"></i>
                    Default Variables
                </h3>
            </div>
            <div class="card-body">
                <div class="variables-container">
                    <pre class="variables-code">{{ json_encode($template->default_variables, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>
        @endif

        <!-- Template Code -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-code"></i>
                    Template Code
                </h3>
            </div>
            <div class="card-body">
                <div class="code-container">
                    <pre class="template-code">{{ $template->blade_template }}</pre>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions-section">
            <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Edit Template
            </a>
            <a href="{{ route('admin.templates.preview', $template) }}" class="btn btn-info">
                <i class="fas fa-eye"></i>
                Preview Template
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
.btn-outline { background: transparent; color: #667eea; border: 2px solid #667eea; }

.details-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.details-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    background: #f7fafc;
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.card-title {
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.1rem;
}

.card-body {
    padding: 20px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-label {
    color: #718096;
    font-size: 14px;
    font-weight: 500;
}

.info-value {
    color: #2d3748;
    font-size: 16px;
}

.description-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.description-text {
    color: #4a5568;
    line-height: 1.6;
    margin: 8px 0 0 0;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.badge-category { background: #e6fffa; color: #319795; }
.badge-active { background: #f0fff4; color: #38a169; }
.badge-inactive { background: #fed7d7; color: #e53e3e; }
.badge-malaysian { background: #fef5e7; color: #d69e2e; }
.badge-general { background: #edf2f7; color: #718096; }
.badge-published { background: #f0fff4; color: #38a169; }
.badge-draft { background: #fef5e7; color: #d69e2e; }

.preview-container {
    text-align: center;
}

.template-preview-image {
    max-width: 100%;
    max-height: 400px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #f7fafc;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    line-height: 1;
}

.stat-label {
    color: #718096;
    font-size: 14px;
    margin-top: 5px;
}

.cards-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.card-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #f7fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.card-info h4 {
    color: #2d3748;
    margin: 0 0 5px 0;
    font-size: 16px;
}

.card-details {
    color: #4a5568;
    margin: 0 0 5px 0;
    font-size: 14px;
}

.card-date {
    color: #718096;
    font-size: 12px;
}

.view-all {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.variables-container, .code-container {
    background: #1a202c;
    border-radius: 8px;
    padding: 20px;
    overflow-x: auto;
}

.variables-code, .template-code {
    color: #e2e8f0;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.5;
    margin: 0;
    white-space: pre-wrap;
    word-wrap: break-word;
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
    
    .page-header-right {
        align-self: stretch;
    }
    
    .page-header-right .btn {
        flex: 1;
        justify-content: center;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-section {
        flex-direction: column;
    }
}
</style>
@endsection 