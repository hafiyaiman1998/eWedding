@extends('layouts.admin.admin')

@section('title', 'Edit Client')

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>✏️ Edit Client</h1>
            <p class="page-subtitle">Update client information and settings</p>
        </div>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> > 
            <a href="{{ route('admin.users.index') }}">Clients</a> > 
            <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a> > 
            <span>Edit</span>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="content-area">
        <div class="content-header">
            <div class="content-title">
                <h2>Edit Client Details</h2>
                <p style="color: var(--text-secondary); margin: 0;">Update {{ $user->name }}'s information</p>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Details
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="{{ route('admin.users.update', $user) }}" style="margin-top: 30px;">
            @csrf
            @method('PUT')
            
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-error" style="background: rgba(231,76,60,0.1); border: 1px solid rgba(231,76,60,0.3); border-radius: 15px; padding: 20px; margin-bottom: 30px;">
                    <h4 style="color: #e74c3c; margin-bottom: 15px;"><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h4>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li style="color: #e74c3c; margin-bottom: 5px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Grid -->
            <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <!-- Personal Information -->
                <div class="form-section" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(20px); border-radius: 20px; padding: 30px; border: 1px solid rgba(255,255,255,0.2);">
                    <h3 style="color: var(--text-primary); margin-bottom: 25px; display: flex; align-items: center;">
                        <i class="fas fa-user" style="margin-right: 10px; color: #ff6b9d;"></i>
                        Personal Information
                    </h3>

                    <!-- Full Name -->
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="name" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                            <i class="fas fa-user" style="margin-right: 8px; color: #ff6b9d;"></i>
                            Full Name *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}"
                               required
                               style="width: 100%; padding: 15px 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 15px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 16px; transition: all 0.3s ease;"
                               placeholder="Enter client's full name">
                    </div>

                    <!-- Email Address -->
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="email" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                            <i class="fas fa-envelope" style="margin-right: 8px; color: #ff6b9d;"></i>
                            Email Address *
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               required
                               style="width: 100%; padding: 15px 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 15px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 16px; transition: all 0.3s ease;"
                               placeholder="Enter email address">
                    </div>

                    <!-- Account Type -->
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="type" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                            <i class="fas fa-user-tag" style="margin-right: 8px; color: #ff6b9d;"></i>
                            Account Type *
                        </label>
                        <select id="type" 
                                name="type" 
                                required
                                style="width: 100%; padding: 15px 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 15px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 16px; transition: all 0.3s ease;">
                            <option value="user" {{ old('type', $user->type) == 'user' ? 'selected' : '' }}>User (Client)</option>
                            <option value="admin" {{ old('type', $user->type) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="form-section" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(20px); border-radius: 20px; padding: 30px; border: 1px solid rgba(255,255,255,0.2);">
                    <h3 style="color: var(--text-primary); margin-bottom: 25px; display: flex; align-items: center;">
                        <i class="fas fa-cog" style="margin-right: 10px; color: #ff6b9d;"></i>
                        Account Settings
                    </h3>

                    <!-- Password (Optional) -->
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="password" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                            <i class="fas fa-lock" style="margin-right: 8px; color: #ff6b9d;"></i>
                            New Password
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password"
                               style="width: 100%; padding: 15px 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 15px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 16px; transition: all 0.3s ease;"
                               placeholder="Leave blank to keep current password">
                        <small style="color: var(--text-secondary); font-size: 13px; margin-top: 5px; display: block;">
                            <i class="fas fa-info-circle"></i> Leave blank to keep current password
                        </small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="password_confirmation" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                            <i class="fas fa-lock" style="margin-right: 8px; color: #ff6b9d;"></i>
                            Confirm New Password
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation"
                               style="width: 100%; padding: 15px 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 15px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 16px; transition: all 0.3s ease;"
                               placeholder="Confirm new password">
                    </div>

                    <!-- Email Verification -->
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                            <i class="fas fa-envelope-check" style="margin-right: 8px; color: #ff6b9d;"></i>
                            Email Verification Status
                        </label>
                        <div style="padding: 15px 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 15px; background: rgba(255,255,255,0.1);">
                            @if($user->email_verified_at)
                                <span style="color: #27ae60; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> 
                                    Verified on {{ $user->email_verified_at->format('M j, Y \a\t g:i A') }}
                                </span>
                            @else
                                <span style="color: #e74c3c; font-weight: 600;">
                                    <i class="fas fa-times-circle"></i> 
                                    Email not verified
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Account Statistics -->
                    <div class="account-stats" style="margin-top: 30px;">
                        <h4 style="color: var(--text-primary); margin-bottom: 15px; font-size: 16px;">Account Summary</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div style="text-align: center; padding: 15px; background: rgba(255,107,157,0.1); border-radius: 10px;">
                                <div style="font-size: 20px; font-weight: 700; color: var(--text-primary);">{{ $user->weddingCards->count() }}</div>
                                <div style="font-size: 11px; color: var(--text-secondary); text-transform: uppercase;">Wedding Cards</div>
                            </div>
                            <div style="text-align: center; padding: 15px; background: rgba(196,69,105,0.1); border-radius: 10px;">
                                <div style="font-size: 20px; font-weight: 700; color: var(--text-primary);">{{ $user->weddingCards->where('is_published', true)->count() }}</div>
                                <div style="font-size: 11px; color: var(--text-secondary); text-transform: uppercase;">Published</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions" style="margin-top: 40px; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.2); text-align: center;">
                <button type="submit" class="btn btn-primary" style="margin-right: 15px; padding: 15px 40px; font-size: 16px;">
                    <i class="fas fa-save"></i> Update Client Details
                </button>
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary" style="padding: 15px 40px; font-size: 16px;">
                    <i class="fas fa-times"></i> Cancel Changes
                </a>
            </div>
        </form>

        <!-- Danger Zone -->
        <div class="danger-zone" style="margin-top: 50px; padding: 30px; background: rgba(231,76,60,0.1); border: 1px solid rgba(231,76,60,0.3); border-radius: 20px;">
            <h3 style="color: #e74c3c; margin-bottom: 15px; display: flex; align-items: center;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>
                Danger Zone
            </h3>
            <p style="color: var(--text-secondary); margin-bottom: 20px;">
                Once you delete this client, there is no going back. Please be certain.
            </p>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                  class="delete-form" 
                  data-delete-type="client" 
                  data-delete-name="{{ $user->name }}"
                  data-delete-warning="This will also delete all their {{ $user->weddingCards->count() }} wedding cards and cannot be undone!"
                  style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn delete-btn" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; border: none; padding: 12px 25px;">
                    <i class="fas fa-trash"></i> Delete Client Account
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.form-section:hover {
    transform: translateY(-5px);
    transition: all 0.3s ease;
    box-shadow: 0 15px 35px rgba(255,107,157,0.2);
}

.form-group input:focus,
.form-group select:focus {
    border-color: #ff6b9d !important;
    box-shadow: 0 0 0 3px rgba(255,107,157,0.1) !important;
    outline: none !important;
}

.form-group input:hover,
.form-group select:hover {
    border-color: rgba(255,107,157,0.5);
}

.btn:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.danger-zone:hover {
    border-color: rgba(231,76,60,0.5);
    transition: all 0.3s ease;
}

.account-stats > div:hover {
    transform: scale(1.05);
    transition: all 0.3s ease;
}

.alert-error {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
@endsection 