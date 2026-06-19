@extends('layouts.admin.admin')

@section('title', 'Admin Profile')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-user-shield"></i>
                Admin Profile
            </h1>
            <p class="page-subtitle">Manage your administrator account and preferences</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-tachometer-alt"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Profile Overview -->
    <div class="profile-overview">
        <div class="profile-card">
            <div class="profile-banner">
                <div class="profile-avatar-large">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                </div>
            </div>
            <div class="profile-info">
                <h2 class="profile-name">{{ Auth::user()->name ?? 'Admin User' }}</h2>
                <p class="profile-role">{{ Auth::user()->type ?? 'System Administrator' }}</p>
                <p class="profile-email">{{ Auth::user()->email ?? 'admin@example.com' }}</p>
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-value">{{ date('M d, Y', strtotime(Auth::user()->created_at ?? now())) }}</span>
                        <span class="stat-label">Member Since</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ Auth::user()->last_login_at ? date('M d, Y', strtotime(Auth::user()->last_login_at)) : 'Today' }}</span>
                        <span class="stat-label">Last Login</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">Active</span>
                        <span class="stat-label">Status</span>
                    </div>
                </div>
                <div class="profile-actions">
                    <button class="btn btn-primary" onclick="editProfile()">
                        <i class="fas fa-edit"></i>
                        Edit Profile
                    </button>
                    <button class="btn btn-outline" onclick="changePassword()">
                        <i class="fas fa-key"></i>
                        Change Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
        <div class="profile-grid">
            <!-- Personal Information -->
            <div class="profile-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-user"></i>
                            Personal Information
                        </h3>
                        <button class="btn btn-sm btn-primary" onclick="editPersonalInfo()">Edit</button>
                    </div>
                    <div class="section-content">
                        <div class="info-grid" id="personalInfoView">
                            <div class="info-item">
                                <label>Full Name</label>
                                <span>{{ Auth::user()->name ?? 'Admin User' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Email Address</label>
                                <span>{{ Auth::user()->email ?? 'admin@example.com' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Phone Number</label>
                                <span>{{ Auth::user()->phone ?? '+60 12-345 6789' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Department</label>
                                <span>{{ Auth::user()->department ?? 'System Administration' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Employee ID</label>
                                <span>{{ Auth::user()->employee_id ?? 'EWC-ADM-001' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Location</label>
                                <span>{{ Auth::user()->location ?? 'Kuala Lumpur, Malaysia' }}</span>
                            </div>
                        </div>
                        
                        <form class="settings-form" id="personalInfoForm" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" id="full_name" name="full_name" value="{{ Auth::user()->name ?? 'Admin User' }}" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" value="{{ Auth::user()->email ?? 'admin@example.com' }}" class="form-input">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" value="{{ Auth::user()->phone ?? '+60 12-345 6789' }}" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <input type="text" id="department" name="department" value="{{ Auth::user()->department ?? 'System Administration' }}" class="form-input">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="employee_id">Employee ID</label>
                                    <input type="text" id="employee_id" name="employee_id" value="{{ Auth::user()->employee_id ?? 'EWC-ADM-001' }}" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input type="text" id="location" name="location" value="{{ Auth::user()->location ?? 'Kuala Lumpur, Malaysia' }}" class="form-input">
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn btn-primary" onclick="savePersonalInfo()">Save Changes</button>
                                <button type="button" class="btn btn-outline" onclick="cancelPersonalInfo()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div class="profile-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-shield-alt"></i>
                            Account Security
                        </h3>
                        <span class="security-status secure">
                            <i class="fas fa-check-circle"></i>
                            Secure
                        </span>
                    </div>
                    <div class="section-content">
                        <div class="security-items">
                            <div class="security-item">
                                <div class="security-info">
                                    <h4>Password</h4>
                                    <p>Last changed {{ now()->subDays(45)->format('M d, Y') }}</p>
                                </div>
                                <button class="btn btn-outline btn-sm" onclick="changePassword()">Change</button>
                            </div>
                            
                            <div class="security-item">
                                <div class="security-info">
                                    <h4>Two-Factor Authentication</h4>
                                    <p>Not enabled - Add extra security to your account</p>
                                </div>
                                <button class="btn btn-primary btn-sm" onclick="enable2FA()">Enable</button>
                            </div>
                            
                            <div class="security-item">
                                <div class="security-info">
                                    <h4>Login Sessions</h4>
                                    <p>2 active sessions across 2 devices</p>
                                </div>
                                <button class="btn btn-outline btn-sm" onclick="manageSessions()">Manage</button>
                            </div>
                            
                            <div class="security-item">
                                <div class="security-info">
                                    <h4>Recovery Email</h4>
                                    <p>{{ Auth::user()->recovery_email ?? 'Not set' }}</p>
                                </div>
                                <button class="btn btn-outline btn-sm" onclick="setRecoveryEmail()">{{ Auth::user()->recovery_email ? 'Update' : 'Set' }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="profile-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-cog"></i>
                            Preferences
                        </h3>
                        <button class="btn btn-sm btn-primary" onclick="savePreferences()">Save</button>
                    </div>
                    <div class="section-content">
                        <form class="settings-form">
                            <div class="form-group">
                                <label for="language">Language</label>
                                <select id="language" name="language" class="form-select">
                                    <option value="en" selected>English</option>
                                    <option value="ms">Bahasa Malaysia</option>
                                    <option value="zh">中文</option>
                                    <option value="ta">தமிழ்</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="timezone">Timezone</label>
                                <select id="timezone" name="timezone" class="form-select">
                                    <option value="Asia/Kuala_Lumpur" selected>Asia/Kuala_Lumpur (GMT+8)</option>
                                    <option value="Asia/Singapore">Asia/Singapore (GMT+8)</option>
                                    <option value="UTC">UTC (GMT+0)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="date_format">Date Format</label>
                                <select id="date_format" name="date_format" class="form-select">
                                    <option value="M d, Y" selected>Jan 15, 2025</option>
                                    <option value="d/m/Y">15/01/2025</option>
                                    <option value="Y-m-d">2025-01-15</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="items_per_page">Items Per Page</label>
                                <select id="items_per_page" name="items_per_page" class="form-select">
                                    <option value="10">10 items</option>
                                    <option value="25" selected>25 items</option>
                                    <option value="50">50 items</option>
                                    <option value="100">100 items</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="profile-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-bell"></i>
                            Notification Settings
                        </h3>
                        <button class="btn btn-sm btn-primary" onclick="saveNotifications()">Save</button>
                    </div>
                    <div class="section-content">
                        <form class="settings-form">
                            <div class="notification-group">
                                <h4>Email Notifications</h4>
                                <div class="notification-items">
                                    <div class="notification-item">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="new_user_registrations" checked>
                                            <span class="checkmark"></span>
                                            New user registrations
                                        </label>
                                    </div>
                                    <div class="notification-item">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="new_wedding_cards" checked>
                                            <span class="checkmark"></span>
                                            New wedding cards created
                                        </label>
                                    </div>
                                    <div class="notification-item">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="system_alerts" checked>
                                            <span class="checkmark"></span>
                                            System alerts and warnings
                                        </label>
                                    </div>
                                    <div class="notification-item">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="backup_reports">
                                            <span class="checkmark"></span>
                                            Daily backup reports
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="notification-group">
                                <h4>Dashboard Notifications</h4>
                                <div class="notification-items">
                                    <div class="notification-item">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="real_time_activity" checked>
                                            <span class="checkmark"></span>
                                            Real-time activity updates
                                        </label>
                                    </div>
                                    <div class="notification-item">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="performance_alerts" checked>
                                            <span class="checkmark"></span>
                                            Performance alerts
                                        </label>
                                    </div>
                                    <div class="notification-item">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="maintenance_reminders" checked>
                                            <span class="checkmark"></span>
                                            Maintenance reminders
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="profile-section full-width">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-history"></i>
                            Recent Activity
                        </h3>
                        <a href="#" class="btn btn-sm btn-outline">View All</a>
                    </div>
                    <div class="section-content">
                        <div class="activity-timeline">
                            <div class="activity-item">
                                <div class="activity-icon login">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Logged in to admin panel</h4>
                                    <p>{{ now()->format('M d, Y \a\t g:i A') }} - IP: 192.168.1.100</p>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon update">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Updated template "Romantic Garden"</h4>
                                    <p>{{ now()->subHours(2)->format('M d, Y \a\t g:i A') }} - Modified layout and styling</p>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon create">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Created new user account</h4>
                                    <p>{{ now()->subHours(5)->format('M d, Y \a\t g:i A') }} - User: sarah.johnson@email.com</p>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon settings">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Modified system settings</h4>
                                    <p>{{ now()->subDay()->format('M d, Y \a\t g:i A') }} - Updated email configuration</p>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon security">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Security scan completed</h4>
                                    <p>{{ now()->subDays(2)->format('M d, Y \a\t g:i A') }} - No threats detected</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

.btn-sm {
    padding: 8px 16px;
    font-size: 12px;
}

/* Profile Overview */
.profile-overview {
    margin-bottom: 30px;
}

.profile-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    position: relative;
}

.profile-banner {
    height: 150px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.profile-avatar-large {
    width: 120px;
    height: 120px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 700;
    color: white;
    border: 4px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    margin-top: 60px;
}

.profile-info {
    padding: 80px 40px 40px;
    text-align: center;
}

.profile-name {
    color: #2d3748;
    margin: 0 0 5px 0;
    font-size: 28px;
    font-weight: 700;
}

.profile-role {
    color: #667eea;
    margin: 0 0 5px 0;
    font-size: 16px;
    font-weight: 600;
}

.profile-email {
    color: #718096;
    margin: 0 0 30px 0;
    font-size: 14px;
}

.profile-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-bottom: 30px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    color: #2d3748;
    font-size: 16px;
    font-weight: 700;
}

.stat-label {
    color: #718096;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.profile-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
}

/* Profile Content */
.profile-content {
    margin-bottom: 30px;
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
}

.profile-section.full-width {
    grid-column: 1 / -1;
}

.section-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    height: fit-content;
}

.section-header {
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
    background: #f7fafc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-header h3 {
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.security-status {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 12px;
}

.security-status.secure {
    color: #38a169;
    background: rgba(56, 161, 105, 0.1);
}

.section-content {
    padding: 25px;
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-item label {
    color: #718096;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.info-item span {
    color: #2d3748;
    font-weight: 600;
}

/* Forms */
.settings-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group label {
    color: #2d3748;
    font-weight: 600;
    font-size: 14px;
}

.form-input, .form-select {
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.btn-outline {
    background: transparent;
    border: 2px solid #e2e8f0;
    color: #4a5568;
}

.btn-outline:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
}

/* Security Items */
.security-items {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.security-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}

.security-info h4 {
    color: #2d3748;
    margin: 0 0 5px 0;
    font-size: 16px;
}

.security-info p {
    color: #718096;
    margin: 0;
    font-size: 14px;
}

/* Notifications */
.notification-group {
    margin-bottom: 25px;
}

.notification-group h4 {
    color: #2d3748;
    margin: 0 0 15px 0;
    font-size: 16px;
}

.notification-items {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.notification-item {
    padding: 12px 0;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    color: #2d3748;
    font-weight: 600;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #667eea;
}

/* Activity Timeline */
.activity-timeline {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #f1f5f9;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.activity-icon.login {
    background: #38a169;
}

.activity-icon.update {
    background: #3182ce;
}

.activity-icon.create {
    background: #667eea;
}

.activity-icon.settings {
    background: #ed8936;
}

.activity-icon.security {
    background: #9f7aea;
}

.activity-content h4 {
    color: #2d3748;
    margin: 0 0 5px 0;
    font-size: 16px;
}

.activity-content p {
    color: #718096;
    margin: 0;
    font-size: 14px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 20px;
    }
    
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .profile-stats {
        flex-direction: column;
        gap: 20px;
    }
    
    .profile-actions {
        flex-direction: column;
    }
    
    .security-item {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    /* CRITICAL: Ensure header and burger button work properly */
    .header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    
    .menu-toggle {
        display: block !important;
        background: none !important;
        border: none !important;
        color: #2d3748 !important;
        font-size: 18px !important;
        cursor: pointer !important;
        padding: 8px !important;
        border-radius: 6px !important;
        transition: background-color 0.3s ease !important;
    }
    
    .menu-toggle:hover {
        background-color: rgba(0,0,0,0.05) !important;
    }
}

@media (max-width: 480px) {
    .page-content {
        padding: 15px;
    }
    
    .profile-info {
        padding: 80px 25px 25px;
    }
    
    .section-content {
        padding: 20px;
    }
}
</style>

<script>
// Profile management functions
function editProfile() {
    document.getElementById('personalInfoView').style.display = 'none';
    document.getElementById('personalInfoForm').style.display = 'block';
}

function editPersonalInfo() {
    editProfile();
}

function savePersonalInfo() {
    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;
    
    // Simulate save operation
    setTimeout(() => {
        saveBtn.textContent = 'Saved!';
        saveBtn.style.background = '#38a169';
        
        setTimeout(() => {
            cancelPersonalInfo();
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
            saveBtn.style.background = '';
        }, 1500);
    }, 1000);
}

function cancelPersonalInfo() {
    document.getElementById('personalInfoView').style.display = 'block';
    document.getElementById('personalInfoForm').style.display = 'none';
}

function changePassword() {
    alert('Password change functionality would open a secure modal or redirect to a password change page.');
}

function enable2FA() {
    if (confirm('Enable Two-Factor Authentication? This will require you to use an authenticator app.')) {
        alert('2FA setup would begin here - QR code display, app setup, etc.');
    }
}

function manageSessions() {
    alert('Session management would show active sessions with options to terminate individual sessions.');
}

function setRecoveryEmail() {
    const email = prompt('Enter recovery email address:');
    if (email) {
        alert('Recovery email would be set/updated: ' + email);
    }
}

function savePreferences() {
    const saveBtn = event.target;
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;
    
    setTimeout(() => {
        saveBtn.textContent = 'Saved!';
        saveBtn.style.background = '#38a169';
        
        setTimeout(() => {
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
            saveBtn.style.background = '';
        }, 2000);
    }, 1000);
}

function saveNotifications() {
    const saveBtn = event.target;
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;
    
    setTimeout(() => {
        saveBtn.textContent = 'Saved!';
        saveBtn.style.background = '#38a169';
        
        setTimeout(() => {
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
            saveBtn.style.background = '';
        }, 2000);
    }, 1000);
}

// Auto-save indication for preferences
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-input, .form-select, input[type="checkbox"]');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            // Show visual indicator that settings have changed
            const section = this.closest('.section-card');
            const saveBtn = section.querySelector('.btn-primary');
            if (saveBtn && (saveBtn.textContent.includes('Save') || saveBtn.onclick === savePreferences || saveBtn.onclick === saveNotifications)) {
                saveBtn.style.background = '#ed8936';
                if (saveBtn.textContent === 'Save') {
                    saveBtn.textContent = 'Save*';
                }
            }
        });
    });
});
</script>
@endsection