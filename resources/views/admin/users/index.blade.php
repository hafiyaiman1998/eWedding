@extends('layouts.admin.admin')

@section('title', 'Manage Clients')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-users"></i>
                Manage Clients
            </h1>
            <p class="page-subtitle">View, create, edit, and delete client accounts</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i>
                Add New Client
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.users.index') }}" class="search-form">
            <div class="search-group">
                <div class="search-field">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name or email..." class="form-input">
                    <button type="submit" class="btn btn-search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-clear">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="table-container">
        @if($users->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Client Info</th>
                        <th>Email</th>
                        <th>Wedding Cards</th>
                        <th>Joined Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="user-details">
                                        <strong>{{ $user->name }}</strong>
                                        <small>ID: {{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <div class="card-stats">
                                    <span class="total-cards">{{ $user->weddingCards->count() }}</span>
                                    <span class="published-cards">
                                        ({{ $user->weddingCards->where('is_published', true)->count() }} published)
                                    </span>
                                </div>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="status-badge status-active">Active</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="btn btn-icon btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="btn btn-icon btn-warning" title="Edit Client">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                          class="delete-form" 
                                          data-delete-type="client" 
                                          data-delete-name="{{ $user->name }}"
                                          data-delete-warning="This will also delete all their {{ $user->weddingCards->count() }} wedding cards."
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-danger delete-btn" 
                                                title="Delete Client">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $users->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>No Clients Found</h3>
                <p>
                    @if(request('search'))
                        No clients match your search criteria.
                    @else
                        You haven't created any client accounts yet.
                    @endif
                </p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Create First Client
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
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-icon {
    padding: 8px;
    border-radius: 6px;
}

.btn-info {
    background: #3182ce;
    color: white;
}

.btn-warning {
    background: #d69e2e;
    color: white;
}

.btn-danger {
    background: #e53e3e;
    color: white;
}

.btn-search {
    background: #4299e1;
    color: white;
    padding: 10px 16px;
    border-radius: 0 8px 8px 0;
}

.btn-clear {
    background: #718096;
    color: white;
}

.filters-section {
    margin-bottom: 25px;
}

.search-form {
    display: flex;
    justify-content: flex-start;
}

.search-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-field {
    display: flex;
    align-items: center;
}

.form-input {
    padding: 10px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px 0 0 8px;
    border-right: none;
    outline: none;
    font-size: 14px;
    min-width: 300px;
}

.form-input:focus {
    border-color: #4299e1;
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

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.user-details strong {
    display: block;
    color: #2d3748;
    margin-bottom: 2px;
}

.user-details small {
    color: #718096;
    font-size: 12px;
}

.card-stats .total-cards {
    font-weight: 600;
    color: #2d3748;
}

.card-stats .published-cards {
    color: #718096;
    font-size: 12px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-active {
    background: #c6f6d5;
    color: #2f855a;
}

.action-buttons {
    display: flex;
    gap: 8px;
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

/* Responsive adjustments for users page */
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
        align-items: center;
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
    
    .search-group {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
        width: 100%;
    }
    
    .search-field {
        width: 100%;
    }
    
    .form-input {
        min-width: 0;
        width: 100%;
        border-radius: 8px;
        border-right: 2px solid #e2e8f0;
    }
    
    .btn-search {
        border-radius: 8px;
        width: 100%;
        justify-content: center;
    }
    
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .data-table {
        min-width: 600px;
    }
    
    .data-table th,
    .data-table td {
        padding: 12px 8px;
        font-size: 14px;
    }
    
    .user-info {
        gap: 8px;
    }
    
    .user-avatar {
        width: 35px;
        height: 35px;
        font-size: 12px;
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
    
    .data-table {
        min-width: 500px;
        font-size: 13px;
    }
    
    .data-table th,
    .data-table td {
        padding: 10px 6px;
    }
    
    .user-avatar {
        width: 30px;
        height: 30px;
        font-size: 11px;
    }
    
    .user-details strong {
        font-size: 14px;
    }
    
    .user-details small {
        font-size: 11px;
    }
    
    .card-stats .published-cards {
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
    
    .form-input {
        padding: 8px 12px;
        font-size: 13px;
    }
    
    .btn-search {
        padding: 8px 12px;
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