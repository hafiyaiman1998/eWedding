@extends('layouts.admin.admin')

@section('title', 'Pending Approvals')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-clock"></i>
                Pending Approvals
            </h1>
            <p class="page-subtitle">Review and manage wedding cards awaiting approval</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.cards.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to All Cards
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Search and Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.cards.pending') }}" class="filters-form">
            <div class="search-box">
                <input type="text" name="search" placeholder="Search by title, user name, or email..." 
                       value="{{ request('search') }}" class="search-input">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Pending Cards Grid -->
    @if($pendingCards->count() > 0)
        <div class="cards-grid">
            @foreach($pendingCards as $card)
                <div class="card-item pending" data-card-id="{{ $card->id }}">
                    <div class="card-header">
                        <div class="card-status pending">
                            <i class="fas fa-clock"></i>
                            Pending Approval
                        </div>
                        <div class="card-date">
                            {{ $card->created_at->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="card-content">
                        <h3 class="card-title">{{ $card->title }}</h3>
                        
                        <div class="card-details">
                            <div class="detail-item">
                                <i class="fas fa-user"></i>
                                <span>{{ $card->user->name }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>{{ $card->user->email }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-palette"></i>
                                <span>{{ $card->designTemplate->name }}</span>
                            </div>
                            @if($card->card_details && isset($card->card_details['wedding_date']))
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ $card->card_details['wedding_date'] }}</span>
                                </div>
                            @endif
                        </div>

                        @if($card->custom_message)
                            <div class="card-message">
                                <strong>Custom Message:</strong>
                                <p>{{ Str::limit($card->custom_message, 100) }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="card-actions">
                        <button class="btn btn-sm btn-primary" onclick="previewCard({{ $card->id }})">
                            <i class="fas fa-eye"></i>
                            Preview
                        </button>
                        <button class="btn btn-sm btn-success" onclick="showApprovalModal({{ $card->id }}, '{{ addslashes($card->title) }}')">
                            <i class="fas fa-check"></i>
                            Approve
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="showRejectModal({{ $card->id }}, '{{ addslashes($card->title) }}')">
                            <i class="fas fa-times"></i>
                            Reject
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $pendingCards->appends(request()->query())->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>No Pending Approvals</h3>
            <p>All wedding cards have been reviewed! Check back later for new submissions.</p>
        </div>
    @endif
</div>

<!-- Old reject modal removed - now using SweetAlert -->

<style>
.page-content {
    padding: 20px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
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
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 12px;
}

.btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #cbd5e0;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-success {
    background: #38a169;
    color: white;
}

.btn-success:hover {
    background: #2f855a;
}

.btn-danger {
    background: #e53e3e;
    color: white;
}

.btn-danger:hover {
    background: #c53030;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #c6f6d5;
    color: #22543d;
    border: 1px solid #9ae6b4;
}

.alert-error {
    background: #fed7d7;
    color: #742a2a;
    border: 1px solid #fc8181;
}

.filters-section {
    margin-bottom: 30px;
}

.filters-form {
    display: flex;
    gap: 20px;
    align-items: center;
}

.search-box {
    display: flex;
    flex: 1;
    max-width: 400px;
}

.search-input {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-right: none;
    border-radius: 8px 0 0 8px;
    font-size: 14px;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
}

.search-btn {
    padding: 12px 16px;
    background: #667eea;
    color: white;
    border: 2px solid #667eea;
    border-radius: 0 8px 8px 0;
    cursor: pointer;
}

.search-btn:hover {
    background: #5a67d8;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.card-item {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.card-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.card-item.pending {
    border-left: 4px solid #ed8936;
}

.card-header {
    padding: 20px;
    background: #f7fafc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.card-status.pending {
    background: rgba(237, 137, 54, 0.1);
    color: #c05621;
}

.card-date {
    color: #718096;
    font-size: 12px;
}

.card-content {
    padding: 20px;
}

.card-title {
    color: #2d3748;
    margin: 0 0 15px 0;
    font-size: 18px;
}

.card-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 15px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #4a5568;
    font-size: 14px;
}

.detail-item i {
    width: 16px;
    color: #718096;
}

.card-message {
    background: #f7fafc;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.card-message strong {
    color: #2d3748;
    display: block;
    margin-bottom: 5px;
}

.card-message p {
    color: #4a5568;
    margin: 0;
    font-size: 14px;
}

.card-actions {
    padding: 20px;
    background: #f7fafc;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 48px;
    color: #38a169;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #2d3748;
    margin: 0 0 10px 0;
}

.empty-state p {
    color: #718096;
    margin: 0;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

/* Custom Approval & Rejection Popup Styles */
.approval-content, .reject-content {
    text-align: center;
    padding: 20px;
}

.approval-icon-wrapper, .reject-icon-wrapper {
    margin-bottom: 20px;
}

.approval-icon {
    font-size: 80px;
    background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: approvalIconBounce 2s ease-in-out infinite;
    filter: drop-shadow(0 4px 15px rgba(56, 161, 105, 0.3));
}

.reject-icon {
    font-size: 80px;
    background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: rejectIconBounce 2s ease-in-out infinite;
    filter: drop-shadow(0 4px 15px rgba(229, 62, 62, 0.3));
}

.approval-text, .reject-text {
    font-size: 16px;
    line-height: 1.6;
    margin: 15px 0;
    color: #2c3e50;
}

.approval-warning, .reject-info {
    background: linear-gradient(135deg, rgba(56, 161, 105, 0.1) 0%, rgba(47, 133, 90, 0.1) 100%);
    border: 2px solid rgba(56, 161, 105, 0.3);
    border-radius: 20px;
    padding: 20px;
    margin-top: 20px;
    color: #2f855a;
    font-weight: 600;
    box-shadow: 0 8px 25px rgba(56, 161, 105, 0.1);
    display: flex;
    align-items: center;
    gap: 10px;
}

.reject-form-group {
    margin-top: 20px;
    text-align: left;
}

.reject-label {
    display: block;
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 8px;
}

.reject-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    box-sizing: border-box;
    resize: vertical;
}

.reject-textarea:focus {
    outline: none;
    border-color: #e53e3e;
    box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
}

.approval-spinner, .reject-spinner {
    width: 60px;
    height: 60px;
    border: 4px solid rgba(56, 161, 105, 0.2);
    border-left: 4px solid #38a169;
    border-radius: 50%;
    animation: loadingSpin 1s linear infinite;
    margin: 0 auto 20px;
}

.reject-spinner {
    border: 4px solid rgba(229, 62, 62, 0.2);
    border-left: 4px solid #e53e3e;
}

.approval-title {
    background: linear-gradient(135deg, #38a169 0%, #2f855a 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

.reject-title {
    background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

.approval-confirm-btn {
    background: linear-gradient(135deg, #38a169 0%, #2f855a 100%) !important;
    color: white !important;
    border: none !important;
    border-radius: 15px !important;
    padding: 15px 30px !important;
    font-weight: 600 !important;
    font-family: 'Poppins', sans-serif !important;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    box-shadow: 0 8px 25px rgba(56, 161, 105, 0.3) !important;
    margin: 0 10px !important;
}

.approval-confirm-btn:hover {
    transform: translateY(-2px) scale(1.05) !important;
    box-shadow: 0 12px 35px rgba(56, 161, 105, 0.4) !important;
}

.reject-confirm-btn {
    background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%) !important;
    color: white !important;
    border: none !important;
    border-radius: 15px !important;
    padding: 15px 30px !important;
    font-weight: 600 !important;
    font-family: 'Poppins', sans-serif !important;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    box-shadow: 0 8px 25px rgba(229, 62, 62, 0.3) !important;
    margin: 0 10px !important;
}

.reject-confirm-btn:hover {
    transform: translateY(-2px) scale(1.05) !important;
    box-shadow: 0 12px 35px rgba(229, 62, 62, 0.4) !important;
}

@keyframes approvalIconBounce {
    0%, 20%, 50%, 80%, 100% { 
        transform: translateY(0) scale(1); 
        opacity: 0.9; 
    }
    40% { 
        transform: translateY(-15px) scale(1.15); 
        opacity: 1; 
    }
    60% { 
        transform: translateY(-8px) scale(1.08); 
        opacity: 0.95; 
    }
}

@keyframes rejectIconBounce {
    0%, 20%, 50%, 80%, 100% { 
        transform: translateY(0) scale(1); 
        opacity: 0.9; 
    }
    40% { 
        transform: translateY(-15px) scale(1.15); 
        opacity: 1; 
    }
    60% { 
        transform: translateY(-8px) scale(1.08); 
        opacity: 0.95; 
    }
}

@keyframes loadingSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .cards-grid {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        flex-direction: column;
        gap: 20px;
    }
    
    .card-actions {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
let currentCardId = null;

function previewCard(cardId) {
    window.open(`/admin/cards/${cardId}/preview`, '_blank');
}

// Custom approval modal
function showApprovalModal(cardId, cardTitle) {
    Swal.fire({
        title: 'Approve Wedding Card?',
        html: `<div class="approval-content">
            <div class="approval-icon-wrapper">
                <i class="fas fa-check-circle approval-icon"></i>
            </div>
            <p class="approval-text">Are you sure you want to approve <strong>"${cardTitle}"</strong>?</p>
            <div class="approval-warning">
                <i class="fas fa-info-circle warning-icon"></i>
                This will publish the wedding card immediately and make it visible to the public.
            </div>
        </div>`,
        icon: false,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Yes, Approve It!',
        cancelButtonText: '<i class="fas fa-times"></i> Cancel',
        customClass: {
            popup: 'glassmorphism-popup approval-popup',
            title: 'swal-title approval-title',
            htmlContainer: 'swal-content',
            confirmButton: 'swal-confirm-btn approval-confirm-btn',
            cancelButton: 'swal-cancel-btn'
        },
        buttonsStyling: false,
        reverseButtons: true,
        allowOutsideClick: true,
        allowEscapeKey: true,
        showClass: {
            popup: 'animate__animated animate__bounceIn animate__fast'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOut animate__faster'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            performApproval(cardId, cardTitle);
        }
    });
}

// Perform approval action
function performApproval(cardId, cardTitle) {
    // Show loading state
    Swal.fire({
        title: 'Approving Wedding Card...',
        html: `<div class="loading-content">
            <div class="loading-spinner approval-spinner"></div>
            <p>Please wait while we approve "${cardTitle}"...</p>
        </div>`,
        icon: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        customClass: {
            popup: 'glassmorphism-popup',
            title: 'swal-title',
            htmlContainer: 'swal-content'
        },
        showClass: {
            popup: 'animate__animated animate__fadeIn animate__faster'
        }
    });

    // Perform AJAX request
    fetch(`/admin/cards/${cardId}/approve`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Wedding Card Approved!',
                html: `<div class="success-content">
                    <div class="success-icon-wrapper">
                        <i class="fas fa-check-circle success-icon"></i>
                    </div>
                    <p class="success-text">${data.message}</p>
                    <div class="success-animation">
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                    </div>
                </div>`,
                icon: false,
                confirmButtonText: '<i class="fas fa-arrow-right"></i> Continue',
                customClass: {
                    popup: 'glassmorphism-popup',
                    title: 'swal-title success-title',
                    htmlContainer: 'swal-content',
                    confirmButton: 'swal-confirm-btn'
                },
                buttonsStyling: false,
                allowOutsideClick: true,
                showClass: {
                    popup: 'animate__animated animate__bounceIn animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOut animate__faster'
                }
            }).then(() => {
                // Remove the card from the grid
                document.querySelector(`[data-card-id="${cardId}"]`).remove();
                
                // Check if no more cards left
                if (document.querySelectorAll('.card-item').length === 0) {
                    location.reload();
                }
            });
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('An error occurred while approving the card.', 'error');
    });
}

function showRejectModal(cardId, cardTitle) {
    Swal.fire({
        title: 'Reject Wedding Card?',
        html: `<div class="reject-content">
            <div class="reject-icon-wrapper">
                <i class="fas fa-times-circle reject-icon"></i>
            </div>
            <p class="reject-text">Are you sure you want to reject <strong>"${cardTitle}"</strong>?</p>
            <div class="reject-form-group">
                <label for="swal-rejection-reason" class="reject-label">Reason for rejection:</label>
                <textarea id="swal-rejection-reason" class="reject-textarea" rows="4" 
                          placeholder="Please provide a clear reason for rejecting this wedding card..." required></textarea>
            </div>
        </div>`,
        icon: false,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-times"></i> Reject Card',
        cancelButtonText: '<i class="fas fa-arrow-left"></i> Cancel',
        customClass: {
            popup: 'glassmorphism-popup reject-popup',
            title: 'swal-title reject-title',
            htmlContainer: 'swal-content',
            confirmButton: 'swal-confirm-btn reject-confirm-btn',
            cancelButton: 'swal-cancel-btn'
        },
        buttonsStyling: false,
        reverseButtons: true,
        allowOutsideClick: true,
        allowEscapeKey: true,
        showClass: {
            popup: 'animate__animated animate__bounceIn animate__fast'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOut animate__faster'
        },
        preConfirm: () => {
            const reason = document.getElementById('swal-rejection-reason').value;
            if (!reason.trim()) {
                Swal.showValidationMessage('Please provide a reason for rejection');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            performRejection(cardId, cardTitle, result.value);
        }
    });
}

// Perform rejection action
function performRejection(cardId, cardTitle, reason) {
    // Show loading state
    Swal.fire({
        title: 'Rejecting Wedding Card...',
        html: `<div class="loading-content">
            <div class="loading-spinner reject-spinner"></div>
            <p>Please wait while we reject "${cardTitle}"...</p>
        </div>`,
        icon: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        customClass: {
            popup: 'glassmorphism-popup',
            title: 'swal-title',
            htmlContainer: 'swal-content'
        },
        showClass: {
            popup: 'animate__animated animate__fadeIn animate__faster'
        }
    });

    // Perform AJAX request
    fetch(`/admin/cards/${cardId}/reject`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Wedding Card Rejected',
                html: `<div class="reject-success-content">
                    <div class="reject-success-icon-wrapper">
                        <i class="fas fa-check-circle success-icon"></i>
                    </div>
                    <p class="reject-success-text">${data.message}</p>
                    <div class="reject-info">
                        <i class="fas fa-info-circle"></i>
                        The user will be notified about the rejection and can resubmit after making changes.
                    </div>
                </div>`,
                icon: false,
                confirmButtonText: '<i class="fas fa-arrow-right"></i> Continue',
                customClass: {
                    popup: 'glassmorphism-popup',
                    title: 'swal-title reject-success-title',
                    htmlContainer: 'swal-content',
                    confirmButton: 'swal-confirm-btn'
                },
                buttonsStyling: false,
                allowOutsideClick: true,
                showClass: {
                    popup: 'animate__animated animate__bounceIn animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOut animate__faster'
                }
            }).then(() => {
                // Remove the card from the grid
                document.querySelector(`[data-card-id="${cardId}"]`).remove();
                
                // Check if no more cards left
                if (document.querySelectorAll('.card-item').length === 0) {
                    location.reload();
                }
            });
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('An error occurred while rejecting the card.', 'error');
    });
}

// Removed old reject form handler as it's now handled by SweetAlert

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'error'}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    
    document.querySelector('.page-content').insertBefore(notification, document.querySelector('.page-content').firstChild);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Old modal event listeners removed - now using SweetAlert
</script>
@endsection 