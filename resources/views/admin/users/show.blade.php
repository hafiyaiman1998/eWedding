@extends('layouts.admin.admin')

@section('title', 'View Client Details')

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>👤 Client Details</h1>
            <p class="page-subtitle">View and manage client information</p>
        </div>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> > 
            <a href="{{ route('admin.users.index') }}">Clients</a> > 
            <span>{{ $user->name }}</span>
        </div>
    </div>

    <!-- User Details Card -->
    <div class="content-area">
        <div class="content-header">
            <div class="content-title">
                <h2>{{ $user->name }}</h2>
                <span class="status-badge status-{{ $user->email_verified_at ? 'active' : 'pending' }}">
                    {{ $user->email_verified_at ? 'Verified' : 'Pending Verification' }}
                </span>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Client
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <!-- User Information Grid -->
        <div class="user-details-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
            <!-- Personal Information -->
            <div class="detail-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(20px); border-radius: 20px; padding: 30px; border: 1px solid rgba(255,255,255,0.2);">
                <h3 style="color: var(--text-primary); margin-bottom: 20px; display: flex; align-items: center;">
                    <i class="fas fa-user" style="margin-right: 10px; color: #ff6b9d;"></i>
                    Personal Information
                </h3>
                <div class="detail-item" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 5px;">Full Name</label>
                    <p style="color: var(--text-primary); font-size: 16px;">{{ $user->name }}</p>
                </div>
                <div class="detail-item" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 5px;">Email Address</label>
                    <p style="color: var(--text-primary); font-size: 16px;">{{ $user->email }}</p>
                </div>
                <div class="detail-item" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 5px;">Account Type</label>
                    <p style="color: var(--text-primary); font-size: 16px; text-transform: capitalize;">{{ $user->type }}</p>
                </div>
                <div class="detail-item" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 5px;">Member Since</label>
                    <p style="color: var(--text-primary); font-size: 16px;">{{ $user->created_at->format('F j, Y') }}</p>
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="detail-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(20px); border-radius: 20px; padding: 30px; border: 1px solid rgba(255,255,255,0.2);">
                <h3 style="color: var(--text-primary); margin-bottom: 20px; display: flex; align-items: center;">
                    <i class="fas fa-chart-bar" style="margin-right: 10px; color: #ff6b9d;"></i>
                    Account Statistics
                </h3>
                <div class="stats-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div class="mini-stat" style="text-align: center; padding: 15px; background: rgba(255,107,157,0.1); border-radius: 15px;">
                        <div style="font-size: 24px; font-weight: 700; color: var(--text-primary);">{{ $user->weddingCards->count() }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase;">Wedding Cards</div>
                    </div>
                    <div class="mini-stat" style="text-align: center; padding: 15px; background: rgba(196,69,105,0.1); border-radius: 15px;">
                        <div style="font-size: 24px; font-weight: 700; color: var(--text-primary);">{{ $user->weddingCards->where('is_published', true)->count() }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase;">Published</div>
                    </div>
                </div>
                <div class="detail-item" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 5px;">Email Verified</label>
                    <p style="color: var(--text-primary); font-size: 16px;">
                        @if($user->email_verified_at)
                            <span style="color: #27ae60;"><i class="fas fa-check-circle"></i> Verified on {{ $user->email_verified_at->format('M j, Y') }}</span>
                        @else
                            <span style="color: #e74c3c;"><i class="fas fa-times-circle"></i> Not Verified</span>
                        @endif
                    </p>
                </div>
                <div class="detail-item">
                    <label style="font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 5px;">Last Activity</label>
                    <p style="color: var(--text-primary); font-size: 16px;">{{ $user->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <!-- Wedding Cards Section -->
        @if($user->weddingCards && $user->weddingCards->count() > 0)
        <div class="wedding-cards-section" style="margin-top: 40px;">
            <h3 style="color: var(--text-primary); margin-bottom: 20px; display: flex; align-items: center;">
                <i class="fas fa-heart" style="margin-right: 10px; color: #ff6b9d;"></i>
                Wedding Cards ({{ $user->weddingCards->count() }})
            </h3>
            <div class="cards-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                @foreach($user->weddingCards as $card)
                <div class="card-item" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(20px); border-radius: 15px; padding: 20px; border: 1px solid rgba(255,255,255,0.2);">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="color: var(--text-primary); margin: 0;">{{ $card->title ?: 'Untitled Card' }}</h4>
                        <span class="status-badge status-{{ $card->is_published ? 'active' : 'pending' }}" style="font-size: 11px;">
                            {{ $card->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    <div class="card-details" style="margin-bottom: 15px;">
                        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 5px;">
                            <i class="fas fa-palette"></i> Template: {{ $card->designTemplate->name ?? 'No Template' }}
                        </p>
                        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 5px;">
                            <i class="fas fa-calendar"></i> Created: {{ $card->created_at->format('M j, Y') }}
                        </p>
                        @if($card->is_published && $card->unique_url)
                        <p style="color: var(--text-secondary); font-size: 14px;">
                            <i class="fas fa-globe"></i> URL: 
                            <a href="{{ url('/wedding/' . $card->unique_url) }}" target="_blank" style="color: #ff6b9d;">
                                View Card
                            </a>
                        </p>
                        @endif
                    </div>
                    <div class="card-actions">
                        <a href="{{ route('admin.cards.show', $card) }}" class="btn btn-secondary" style="font-size: 12px; padding: 8px 15px;">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="empty-state" style="text-align: center; padding: 60px 20px; margin-top: 40px;">
            <i class="fas fa-heart" style="font-size: 64px; color: rgba(255,107,157,0.3); margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-primary); margin-bottom: 10px;">No Wedding Cards Yet</h3>
            <p style="color: var(--text-secondary);">This client hasn't created any wedding cards yet.</p>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="action-section" style="margin-top: 40px; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.2); text-align: center;">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary" style="margin-right: 15px;">
                <i class="fas fa-edit"></i> Edit Client Details
            </a>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                  class="delete-form" 
                  data-delete-type="client" 
                  data-delete-name="{{ $user->name }}"
                  data-delete-warning="This will also delete all their {{ $user->weddingCards->count() }} wedding cards."
                  style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn delete-btn" style="background: var(--primary-gradient); color: white; border: none;">
                    <i class="fas fa-trash"></i> Delete Client
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.user-details-grid .detail-card:hover {
    transform: translateY(-5px);
    transition: all 0.3s ease;
    box-shadow: 0 15px 35px rgba(255,107,157,0.2);
}

.mini-stat:hover {
    transform: scale(1.05);
    transition: all 0.3s ease;
}

.card-item:hover {
    transform: translateY(-3px);
    transition: all 0.3s ease;
    box-shadow: 0 10px 25px rgba(255,107,157,0.2);
}

.empty-state i {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}
</style>
@endsection 