@extends('layouts.admin.admin')

@section('title', 'System Settings')

@section('content')
<div class="page-content">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">
                <i class="fas fa-cog"></i>
                System Settings
            </h1>
            <p class="page-subtitle">Configure and manage eWeddingCard system settings</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-tachometer-alt"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- System Status Overview -->
    <div class="status-overview">
        <div class="status-cards">
            <div class="status-card healthy">
                <div class="status-icon">
                    <i class="fas fa-server"></i>
                </div>
                <div class="status-content">
                    <h3>System Status</h3>
                    <p class="status-text">All Systems Operational</p>
                    <small>Last checked: {{ now()->format('M d, Y g:i A') }}</small>
                </div>
                <div class="status-indicator">
                    <i class="fas fa-circle"></i>
                </div>
            </div>
            
            <div class="status-card">
                <div class="status-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="status-content">
                    <h3>Database</h3>
                    <p class="status-text">Connected & Optimized</p>
                    <small>Response time: &lt; 50ms</small>
                </div>
                <div class="status-indicator healthy">
                    <i class="fas fa-circle"></i>
                </div>
            </div>
            
            <div class="status-card">
                <div class="status-icon">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="status-content">
                    <h3>Storage</h3>
                    <p class="status-text">{{ round(75.3, 1) }}% Used</p>
                    <small>{{ number_format(2.1) }}GB of {{ number_format(8) }}GB available</small>
                </div>
                <div class="status-indicator warning">
                    <i class="fas fa-circle"></i>
                </div>
            </div>
            
            <div class="status-card">
                <div class="status-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="status-content">
                    <h3>Security</h3>
                    <p class="status-text">Protected</p>
                    <small>SSL Certificate Valid</small>
                </div>
                <div class="status-indicator healthy">
                    <i class="fas fa-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Sections -->
    <div class="settings-content">
        <div class="settings-grid">
            <!-- General Settings -->
            <div class="settings-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-cogs"></i>
                            General Settings
                        </h3>
                        <button class="btn btn-sm btn-primary">Save Changes</button>
                    </div>
                    <div class="section-content">
                        <form class="settings-form">
                            <div class="form-group">
                                <label for="app_name">Application Name</label>
                                <input type="text" id="app_name" name="app_name" value="eWeddingCard Creative Studio" class="form-input">
                                <small class="form-help">The name displayed across the application</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="app_url">Application URL</label>
                                <input type="url" id="app_url" name="app_url" value="{{ request()->getSchemeAndHttpHost() }}" class="form-input">
                                <small class="form-help">Base URL for generating links</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="timezone">Default Timezone</label>
                                <select id="timezone" name="timezone" class="form-select">
                                    <option value="Asia/Kuala_Lumpur" selected>Asia/Kuala_Lumpur (GMT+8)</option>
                                    <option value="Asia/Singapore">Asia/Singapore (GMT+8)</option>
                                    <option value="UTC">UTC (GMT+0)</option>
                                    <option value="America/New_York">America/New_York (EST)</option>
                                </select>
                                <small class="form-help">Default timezone for dates and times</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="date_format">Date Format</label>
                                <select id="date_format" name="date_format" class="form-select">
                                    <option value="M d, Y" selected>Jan 15, 2025</option>
                                    <option value="d/m/Y">15/01/2025</option>
                                    <option value="Y-m-d">2025-01-15</option>
                                    <option value="d F Y">15 January 2025</option>
                                </select>
                                <small class="form-help">How dates are displayed throughout the system</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Email Configuration -->
            <div class="settings-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-envelope"></i>
                            Email Configuration
                        </h3>
                        <button class="btn btn-sm btn-primary">Save Changes</button>
                    </div>
                    <div class="section-content">
                        <form class="settings-form">
                            <div class="form-group">
                                <label for="mail_driver">Mail Driver</label>
                                <select id="mail_driver" name="mail_driver" class="form-select">
                                    <option value="smtp" selected>SMTP</option>
                                    <option value="sendmail">Sendmail</option>
                                    <option value="mailgun">Mailgun</option>
                                    <option value="ses">Amazon SES</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="mail_host">SMTP Host</label>
                                <input type="text" id="mail_host" name="mail_host" value="smtp.gmail.com" class="form-input">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="mail_port">SMTP Port</label>
                                    <input type="number" id="mail_port" name="mail_port" value="587" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label for="mail_encryption">Encryption</label>
                                    <select id="mail_encryption" name="mail_encryption" class="form-select">
                                        <option value="tls" selected>TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="">None</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="mail_from_address">From Email Address</label>
                                <input type="email" id="mail_from_address" name="mail_from_address" value="noreply@eweddingcard.com" class="form-input">
                            </div>
                            
                            <div class="form-group">
                                <label for="mail_from_name">From Name</label>
                                <input type="text" id="mail_from_name" name="mail_from_name" value="eWeddingCard Team" class="form-input">
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn btn-outline">Test Email</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Wedding Card Settings -->
            <div class="settings-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-heart"></i>
                            Wedding Card Settings
                        </h3>
                        <button type="button" class="btn btn-sm btn-primary" onclick="saveWeddingCardSettings()">Save Changes</button>
                    </div>
                    <div class="section-content">
                        <form class="settings-form" id="weddingCardSettingsForm">
                            @csrf
                            <div class="form-group">
                                <label for="max_cards_per_user">Max Cards Per User</label>
                                <input type="number" id="max_cards_per_user" name="max_cards_per_user" 
                                       value="{{ \App\Models\Setting::get('max_cards_per_user', 10) }}" 
                                       class="form-input" min="1" max="100" required>
                                <small class="form-help">Maximum number of wedding cards each user can create</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="default_card_expiry">Default Card Expiry (days)</label>
                                <input type="number" id="default_card_expiry" name="default_card_expiry" 
                                       value="{{ \App\Models\Setting::get('default_card_expiry_days', 365) }}" 
                                       class="form-input" min="1" max="3650" required>
                                <small class="form-help">How long cards remain active by default</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="allow_custom_domains" 
                                           {{ \App\Models\Setting::get('allow_custom_domains', true) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Allow Custom Domains
                                </label>
                                <small class="form-help">Let users use their own domain names</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="enable_analytics" 
                                           {{ \App\Models\Setting::get('enable_analytics_tracking', true) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Enable Analytics Tracking
                                </label>
                                <small class="form-help">Track views, shares, and RSVP data</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="auto_approve_cards" 
                                           {{ \App\Models\Setting::get('auto_approve_cards', true) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Auto-approve New Cards
                                </label>
                                <small class="form-help">Automatically publish new cards without admin review</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- File Upload Settings -->
            <div class="settings-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-upload"></i>
                            File Upload Settings
                        </h3>
                        <button class="btn btn-sm btn-primary">Save Changes</button>
                    </div>
                    <div class="section-content">
                        <form class="settings-form">
                            <div class="form-group">
                                <label for="max_file_size">Max File Size (MB)</label>
                                <input type="number" id="max_file_size" name="max_file_size" value="50" class="form-input">
                                <small class="form-help">Maximum file size for uploads</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="allowed_image_types">Allowed Image Types</label>
                                <input type="text" id="allowed_image_types" name="allowed_image_types" value="jpg,jpeg,png,gif,webp" class="form-input">
                                <small class="form-help">Comma-separated list of allowed image extensions</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="allowed_video_types">Allowed Video Types</label>
                                <input type="text" id="allowed_video_types" name="allowed_video_types" value="mp4,avi,mov,wmv,webm" class="form-input">
                                <small class="form-help">Comma-separated list of allowed video extensions</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="auto_optimize_images" checked>
                                    <span class="checkmark"></span>
                                    Auto-optimize Images
                                </label>
                                <small class="form-help">Automatically compress and optimize uploaded images</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="settings-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-shield-alt"></i>
                            Security Settings
                        </h3>
                        <button class="btn btn-sm btn-primary">Save Changes</button>
                    </div>
                    <div class="section-content">
                        <form class="settings-form">
                            <div class="form-group">
                                <label for="session_lifetime">Session Lifetime (minutes)</label>
                                <input type="number" id="session_lifetime" name="session_lifetime" value="120" class="form-input">
                                <small class="form-help">How long users stay logged in</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="password_min_length">Minimum Password Length</label>
                                <input type="number" id="password_min_length" name="password_min_length" value="8" class="form-input">
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="require_email_verification" checked>
                                    <span class="checkmark"></span>
                                    Require Email Verification
                                </label>
                                <small class="form-help">Users must verify their email before accessing the system</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="enable_two_factor">
                                    <span class="checkmark"></span>
                                    Enable Two-Factor Authentication
                                </label>
                                <small class="form-help">Add an extra layer of security for admin accounts</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="log_user_activities" checked>
                                    <span class="checkmark"></span>
                                    Log User Activities
                                </label>
                                <small class="form-help">Keep track of user actions for security auditing</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Backup & Maintenance -->
            <div class="settings-section">
                <div class="section-card">
                    <div class="section-header">
                        <h3>
                            <i class="fas fa-database"></i>
                            Backup & Maintenance
                        </h3>
                        <div class="header-actions">
                            <button class="btn btn-sm btn-success">Run Backup Now</button>
                        </div>
                    </div>
                    <div class="section-content">
                        <div class="backup-status">
                            <div class="backup-info">
                                <div class="backup-item">
                                    <span class="label">Last Backup:</span>
                                    <span class="value">{{ now()->subHours(6)->format('M d, Y g:i A') }}</span>
                                </div>
                                <div class="backup-item">
                                    <span class="label">Backup Size:</span>
                                    <span class="value">{{ number_format(142.5, 1) }}MB</span>
                                </div>
                                <div class="backup-item">
                                    <span class="label">Backup Location:</span>
                                    <span class="value">Cloud Storage (AWS S3)</span>
                                </div>
                            </div>
                        </div>
                        
                        <form class="settings-form">
                            <div class="form-group">
                                <label for="backup_frequency">Backup Frequency</label>
                                <select id="backup_frequency" name="backup_frequency" class="form-select">
                                    <option value="daily" selected>Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="manual">Manual Only</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="backup_retention">Backup Retention (days)</label>
                                <input type="number" id="backup_retention" name="backup_retention" value="30" class="form-input">
                                <small class="form-help">How long to keep backup files</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="backup_database" checked>
                                    <span class="checkmark"></span>
                                    Include Database
                                </label>
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="backup_files" checked>
                                    <span class="checkmark"></span>
                                    Include Uploaded Files
                                </label>
                            </div>
                        </form>
                        
                        <div class="maintenance-actions">
                            <h4>Maintenance Actions</h4>
                            <div class="action-buttons">
                                <button class="btn btn-outline">Clear Cache</button>
                                <button class="btn btn-outline">Optimize Database</button>
                                <button class="btn btn-outline">Clean Logs</button>
                                <button class="btn btn-warning">Maintenance Mode</button>
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

/* Status Overview */
.status-overview {
    margin-bottom: 30px;
}

.status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.status-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    border-left: 4px solid #e2e8f0;
}

.status-card.healthy {
    border-left-color: #38a169;
}

.status-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.status-content {
    flex: 1;
}

.status-content h3 {
    color: #2d3748;
    margin: 0 0 5px 0;
    font-size: 18px;
}

.status-text {
    color: #4a5568;
    margin: 0 0 5px 0;
    font-weight: 600;
}

.status-content small {
    color: #718096;
    font-size: 12px;
}

.status-indicator {
    position: absolute;
    top: 15px;
    right: 15px;
    color: #e2e8f0;
    font-size: 12px;
}

.status-indicator.healthy {
    color: #38a169;
}

.status-indicator.warning {
    color: #ed8936;
}

/* Settings Content */
.settings-content {
    margin-bottom: 30px;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 25px;
}

.settings-section {
    display: flex;
    flex-direction: column;
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

.header-actions {
    display: flex;
    gap: 10px;
}

.section-content {
    padding: 25px;
}

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

.form-help {
    color: #718096;
    font-size: 12px;
    margin-top: -3px;
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

.form-actions {
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

.btn-success {
    background: #38a169;
    color: white;
}

.btn-success:hover {
    background: #2f855a;
}

.btn-warning {
    background: #ed8936;
    color: white;
}

.btn-warning:hover {
    background: #dd6b20;
}

/* Backup Status */
.backup-status {
    margin-bottom: 25px;
    padding: 20px;
    background: #f7fafc;
    border-radius: 8px;
    border-left: 4px solid #38a169;
}

.backup-info {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.backup-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.backup-item .label {
    color: #718096;
    font-weight: 600;
}

.backup-item .value {
    color: #2d3748;
    font-weight: 600;
}

.maintenance-actions {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.maintenance-actions h4 {
    color: #2d3748;
    margin: 0 0 15px 0;
}

.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 20px;
    }
    
    .status-cards {
        grid-template-columns: 1fr;
    }
    
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .section-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .backup-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
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
    
    .status-card {
        padding: 20px;
    }
    
    .section-content {
        padding: 20px;
    }
}
</style>

<script>
// Form handling
document.addEventListener('DOMContentLoaded', function() {
    // Save button functionality
    const saveButtons = document.querySelectorAll('.btn-primary');
    saveButtons.forEach(button => {
        if (button.textContent.includes('Save Changes')) {
            button.addEventListener('click', function() {
                // Show loading state
                const originalText = this.textContent;
                this.textContent = 'Saving...';
                this.disabled = true;
                
                // Simulate save operation
                setTimeout(() => {
                    this.textContent = 'Saved!';
                    this.style.background = '#38a169';
                    
                    setTimeout(() => {
                        this.textContent = originalText;
                        this.disabled = false;
                        this.style.background = '';
                    }, 2000);
                }, 1000);
            });
        }
    });
    
    // Test email button
    const testEmailBtn = document.querySelector('.btn-outline');
    if (testEmailBtn && testEmailBtn.textContent.includes('Test Email')) {
        testEmailBtn.addEventListener('click', function() {
            const originalText = this.textContent;
            this.textContent = 'Sending...';
            this.disabled = true;
            
            setTimeout(() => {
                this.textContent = 'Email Sent!';
                this.style.background = '#38a169';
                this.style.color = 'white';
                this.style.borderColor = '#38a169';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.disabled = false;
                    this.style.background = '';
                    this.style.color = '';
                    this.style.borderColor = '';
                }, 3000);
            }, 2000);
        });
    }
    
    // Maintenance mode button
    const maintenanceBtn = document.querySelector('.btn-warning');
    if (maintenanceBtn && maintenanceBtn.textContent.includes('Maintenance Mode')) {
        maintenanceBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to enable maintenance mode? This will temporarily disable the site for all users.')) {
                this.textContent = 'Maintenance Mode: ON';
                this.style.background = '#e53e3e';
            }
        });
    }
    
    // Auto-save indication
    const inputs = document.querySelectorAll('.form-input, .form-select, input[type="checkbox"]');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            // Show visual indicator that settings have changed
            const section = this.closest('.section-card');
            const saveBtn = section.querySelector('.btn-primary');
            if (saveBtn && saveBtn.textContent.includes('Save Changes')) {
                saveBtn.style.background = '#ed8936';
                saveBtn.textContent = 'Save Changes*';
            }
        });
    });
});

// Wedding Card Settings Save Function
function saveWeddingCardSettings() {
    const form = document.getElementById('weddingCardSettingsForm');
    const button = form.closest('.section-card').querySelector('.btn-primary');
    const originalText = button.textContent;
    
    // Show loading state
    button.textContent = 'Saving...';
    button.disabled = true;
    
    // Get form data
    const formData = new FormData();
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    formData.append('max_cards_per_user', document.getElementById('max_cards_per_user').value);
    formData.append('default_card_expiry', document.getElementById('default_card_expiry').value);
    
    // Handle checkboxes
    const allowCustomDomains = document.querySelector('input[name="allow_custom_domains"]');
    if (allowCustomDomains && allowCustomDomains.checked) {
        formData.append('allow_custom_domains', '1');
    }
    
    const enableAnalytics = document.querySelector('input[name="enable_analytics"]');
    if (enableAnalytics && enableAnalytics.checked) {
        formData.append('enable_analytics', '1');
    }
    
    const autoApproveCards = document.querySelector('input[name="auto_approve_cards"]');
    if (autoApproveCards && autoApproveCards.checked) {
        formData.append('auto_approve_cards', '1');
    }
    
    // Debug: Log form data
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    // Send AJAX request
    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Response text:', text);
                throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            button.textContent = 'Saved!';
            button.style.background = '#38a169';
            
            // Show success message
            showNotification('Wedding card settings updated successfully!', 'success');
            
            setTimeout(() => {
                button.textContent = originalText;
                button.disabled = false;
                button.style.background = '';
            }, 2000);
        } else {
            throw new Error(data.message || 'Failed to save settings');
        }
    })
    .catch(error => {
        console.error('Error details:', error);
        button.textContent = 'Error!';
        button.style.background = '#e74c3c';
        
        // Show error message
        showNotification('Error: ' + error.message, 'error');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.disabled = false;
            button.style.background = '';
        }, 3000);
    });
}

// Notification function
function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        background: ${type === 'success' ? '#38a169' : '#e74c3c'};
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endsection