@extends('layouts.admin.admin')

@section('title', 'Add New Client')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-user-plus"></i>
                Add New Client
            </h1>
            <p class="page-subtitle">Create a new client account for wedding card services</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Clients
            </a>
        </div>
    </div>

    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h3>Client Information</h3>
                <p>Enter the details for the new client account</p>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="user-form">
                @csrf
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i>
                        Full Name
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           class="form-input @error('name') error @enderror"
                           placeholder="Enter client's full name"
                           required>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           class="form-input @error('email') error @enderror"
                           placeholder="Enter client's email address"
                           required>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-input @error('password') error @enderror"
                           placeholder="Enter a secure password"
                           required>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <small class="form-help">Password must be at least 8 characters long</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock-open"></i>
                        Confirm Password
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-input"
                           placeholder="Confirm the password"
                           required>
                </div>

                <div class="info-box">
                    <div class="info-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="info-content">
                        <h4>Client Account Details</h4>
                        <ul>
                            <li>The account will be created with "user" role automatically</li>
                            <li>Client will be able to log in and create wedding cards</li>
                            <li>You can edit or delete this account later if needed</li>
                            <li>Client will have access to all active design templates</li>
                        </ul>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-cancel">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Create Client Account
                    </button>
                </div>
            </form>
        </div>
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

.btn-secondary {
    background: #718096;
    color: white;
}

.btn-secondary:hover {
    background: #4a5568;
}

.btn-cancel {
    background: #e2e8f0;
    color: #4a5568;
}

.btn-cancel:hover {
    background: #cbd5e0;
}

.form-container {
    max-width: 600px;
    margin: 0 auto;
}

.form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.form-header {
    padding: 30px 30px 20px 30px;
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    border-bottom: 1px solid #e2e8f0;
}

.form-header h3 {
    color: #2d3748;
    margin: 0 0 8px 0;
    font-size: 1.5rem;
}

.form-header p {
    color: #718096;
    margin: 0;
}

.user-form {
    padding: 30px;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.form-input.error {
    border-color: #e53e3e;
}

.error-message {
    color: #e53e3e;
    font-size: 12px;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.error-message:before {
    content: '⚠️';
    font-size: 12px;
}

.form-help {
    color: #718096;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

.info-box {
    background: #ebf8ff;
    border: 1px solid #bee3f8;
    border-radius: 8px;
    padding: 20px;
    margin: 25px 0;
    display: flex;
    gap: 15px;
}

.info-icon {
    color: #3182ce;
    font-size: 20px;
    flex-shrink: 0;
    margin-top: 2px;
}

.info-content h4 {
    color: #2c5282;
    margin: 0 0 10px 0;
    font-size: 14px;
}

.info-content ul {
    margin: 0;
    padding-left: 20px;
    color: #2c5282;
}

.info-content li {
    margin-bottom: 5px;
    font-size: 13px;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

/* Enhanced responsive adjustments for users create page */
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
        align-items: flex-start;
        margin-bottom: 25px;
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
    
    .form-container {
        max-width: 100%;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    
    .form-card {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        margin: 0 !important;
    }
    
    .form-header {
        padding: 20px 15px;
    }
    
    .form-header h3 {
        font-size: 1.3rem;
        text-align: center;
    }
    
    .form-header p {
        text-align: center;
        font-size: 14px;
    }
    
    .user-form {
        padding: 20px 15px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-input {
        padding: 10px 12px;
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    .info-box {
        padding: 15px;
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    
    .info-content h4 {
        text-align: center;
    }
    
    .info-content ul {
        text-align: left;
        padding-left: 15px;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
        padding: 12px 16px;
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
    
    .form-header {
        padding: 15px 10px;
    }
    
    .form-header h3 {
        font-size: 1.2rem;
    }
    
    .form-header p {
        font-size: 13px;
    }
    
    .user-form {
        padding: 15px 10px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-label {
        font-size: 13px;
        margin-bottom: 6px;
    }
    
    .form-input {
        padding: 8px 10px;
        font-size: 16px;
    }
    
    .info-box {
        padding: 12px;
        margin: 20px 0;
    }
    
    .info-content h4 {
        font-size: 13px;
    }
    
    .info-content li {
        font-size: 12px;
        margin-bottom: 3px;
    }
    
    .form-help {
        font-size: 11px;
    }
    
    .error-message {
        font-size: 11px;
    }
}
</style>
@endsection 