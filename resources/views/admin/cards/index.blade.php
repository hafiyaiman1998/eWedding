@extends('layouts.admin.admin')

@section('title', 'Wedding Cards')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-heart"></i>
                All Wedding Cards
            </h1>
            <p class="page-subtitle">View and manage all wedding cards created by clients</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.cards.published') }}" class="btn btn-success">
                <i class="fas fa-globe"></i>
                Published Cards
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.cards.index') }}" class="filter-form">
            <div class="filter-group">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search cards, clients, or templates..." class="form-input">
                
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                
                <select name="template" class="form-select">
                    <option value="">All Templates</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ request('template') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }}
                        </option>
                    @endforeach
                </select>
                
                <select name="user" class="form-select">
                    <option value="">All Clients</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="btn btn-search">
                    <i class="fas fa-search"></i>
                    Filter
                </button>
                
                @if(request()->hasAny(['search', 'status', 'template', 'user']))
                    <a href="{{ route('admin.cards.index') }}" class="btn btn-clear">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Cards Table -->
    <div class="table-container">
        @if($cards->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Card Details</th>
                        <th>Client</th>
                        <th>Template</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cards as $card)
                        <tr>
                            <td>
                                <div class="card-info">
                                    <div class="card-icon">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div class="card-details">
                                        <strong>{{ $card->title }}</strong>
                                        <small>
                                            {{ $card->card_details['bride_name'] ?? 'N/A' }} & 
                                            {{ $card->card_details['groom_name'] ?? 'N/A' }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="client-info">
                                    <div class="client-avatar">
                                        {{ strtoupper(substr($card->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $card->user->name }}</strong>
                                        <small>{{ $card->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $card->designTemplate->name }}</strong>
                            </td>
                            <td>{{ $card->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="status-badge {{ $card->is_published ? 'status-published' : 'status-draft' }}">
                                    {{ $card->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.cards.show', $card) }}" 
                                       class="btn btn-icon btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.cards.edit', $card) }}" 
                                       class="btn btn-icon btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $cards->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h3>No Wedding Cards Found</h3>
                <p>
                    @if(request()->hasAny(['search', 'status', 'template', 'user']))
                        No cards match your filter criteria.
                    @else
                        No wedding cards have been created yet by clients.
                    @endif
                </p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Add Clients First
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

.btn-success { background: #38a169; color: white; }
.btn-primary { background: #3182ce; color: white; }
.btn-info { background: #3182ce; color: white; }
.btn-warning { background: #d69e2e; color: white; }
.btn-danger { background: #e53e3e; color: white; }
.btn-secondary { background: #718096; color: white; }
.btn-search { background: #4299e1; color: white; }
.btn-clear { background: #718096; color: white; }

.btn-icon {
    padding: 8px;
    border-radius: 6px;
}

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
    min-width: 250px;
}

.table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #f7fafc;
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #4a5568;
    border-bottom: 1px solid #e2e8f0;
}

.data-table td {
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
}

.data-table tr:hover {
    background: #f7fafc;
}

.card-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.card-details strong {
    display: block;
    color: #2d3748;
    margin-bottom: 4px;
}

.card-details small {
    color: #718096;
    font-size: 12px;
    display: block;
    margin-bottom: 4px;
}

.client-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.client-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 12px;
}

.client-info strong {
    display: block;
    color: #2d3748;
    margin-bottom: 2px;
}

.client-info small {
    color: #718096;
    font-size: 12px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
    margin-bottom: 4px;
}

.status-published {
    background: #c6f6d5;
    color: #2f855a;
}

.status-draft {
    background: #fed7d7;
    color: #c53030;
}

.action-buttons {
    display: flex;
    gap: 6px;
}

.pagination-wrapper {
    padding: 20px;
    display: flex;
    justify-content: center;
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

/* Responsive adjustments for cards page */
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
    
    .btn-search, .btn-clear {
        width: 100%;
        justify-content: center;
    }
    
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .data-table {
        min-width: 700px;
    }
    
    .data-table th,
    .data-table td {
        padding: 12px 8px;
        font-size: 14px;
    }
    
    .card-info {
        gap: 8px;
    }
    
    .card-icon {
        width: 35px;
        height: 35px;
    }
    
    .client-info {
        gap: 8px;
    }
    
    .client-avatar {
        width: 30px;
        height: 30px;
        font-size: 11px;
    }
    
    .action-buttons {
        gap: 4px;
    }
    
    .btn-icon {
        padding: 6px;
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
    
    .data-table {
        min-width: 600px;
        font-size: 13px;
    }
    
    .data-table th,
    .data-table td {
        padding: 10px 6px;
    }
    
    .card-icon {
        width: 30px;
        height: 30px;
        font-size: 14px;
    }
    
    .card-details strong {
        font-size: 14px;
    }
    
    .card-details small {
        font-size: 11px;
    }
    
    .client-avatar {
        width: 28px;
        height: 28px;
        font-size: 10px;
    }
    
    .client-info strong {
        font-size: 13px;
    }
    
    .client-info small {
        font-size: 11px;
    }
    
    .status-badge {
        padding: 3px 8px;
        font-size: 11px;
    }
    
    .btn-icon {
        padding: 4px;
    }
    
    .btn-icon i {
        font-size: 12px;
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