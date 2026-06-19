@extends('layouts.user.user')

@section('title', 'Edit Wedding Card')
@section('page_title', 'Edit Wedding Card')
@section('page_subtitle', 'Update your wedding invitation details')

@section('content')
<div class="edit-container">
    <div class="content-card">
        <!-- Header with Card Info -->
        <div class="card-header">
            <div class="card-info">
                <h2 class="card-title">{{ $card->title }}</h2>
                <p class="card-status">
                    Status: 
                    @if($card->is_published)
                        <span class="status published">
                            <i class="fas fa-globe"></i> Published
                        </span>
                    @else
                        <span class="status draft">
                            <i class="fas fa-edit"></i> Draft
                        </span>
                    @endif
                </p>
                <p class="card-template">
                    <i class="fas fa-palette"></i>
                    Template: {{ $card->designTemplate->name }}
                </p>
            </div>
            
            <div class="card-actions">
                <a href="{{ route('user.cards.preview', $card) }}" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-eye"></i>
                    Preview Card
                </a>
                @if($card->is_published)
                    <a href="{{ route('user.cards.share', $card) }}" class="btn btn-success">
                        <i class="fas fa-share-alt"></i>
                        Share Card
                    </a>
                @endif
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

        <!-- Edit Form -->
        <form method="POST" action="{{ route('user.cards.update', $card) }}" id="editForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-sections">
                <!-- Template Selection -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-palette"></i>
                        Template
                    </h3>
                    
                    <div class="current-template">
                        <div class="template-preview">
                            @if($card->designTemplate->preview_image)
                                <img src="{{ asset('storage/' . $card->designTemplate->preview_image) }}" alt="{{ $card->designTemplate->name }}">
                            @else
                                <div class="template-placeholder">
                                    <i class="fas fa-heart"></i>
                                    <span>{{ $card->designTemplate->name }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="template-info">
                            <h4>{{ $card->designTemplate->name }}</h4>
                            <p>{{ ucfirst($card->designTemplate->category) }}</p>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="changeTemplate()">
                                <i class="fas fa-exchange-alt"></i>
                                Change Template
                            </button>
                        </div>
                    </div>
                    
                    <input type="hidden" name="design_template_id" value="{{ $card->design_template_id }}" id="templateInput">
                </div>

                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Basic Information
                    </h3>
                    
                    <div class="form-group">
                        <label for="title" class="form-label">Card Title *</label>
                        <input type="text" id="title" name="title" class="form-input" 
                               value="{{ old('title', $card->title) }}" required>
                        @error('title')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Dynamic Template Variables Section -->
                <div class="form-section" id="templateVariablesSection">
                    <h3 class="section-title">
                        <i class="fas fa-cogs"></i>
                        Wedding Information
                    </h3>
                    <p class="section-description">Customize your wedding details based on the selected template:</p>
                    <div id="dynamicFields"></div>
                </div>

                <!-- Personal Message -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-message"></i>
                        Personal Message
                    </h3>
                    
                    <div class="form-group">
                        <label for="custom_message" class="form-label">Custom Message</label>
                        <textarea id="custom_message" name="custom_message" 
                                  class="form-textarea" rows="4">{{ old('custom_message', $card->custom_message) }}</textarea>
                        <small class="form-help">This message will appear on your wedding card</small>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('user.cards.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Cards
                </a>
                
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                    
                    @if(!$card->is_published && $autoApproveEnabled)
                        <button type="button" class="btn btn-success" onclick="publishCard()">
                            <i class="fas fa-globe"></i>
                            Save & Publish
                        </button>
                    @elseif(!$card->is_published && !$autoApproveEnabled)
                        <div class="approval-notice">
                            <i class="fas fa-info-circle"></i>
                            <span>Cards require admin approval before publishing. Save your changes and wait for admin review.</span>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Template Selection Modal -->
<div class="modal" id="templateModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Choose New Template</h3>
            <button type="button" class="modal-close" onclick="closeTemplateModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="warning-message" style="margin-bottom: 20px; padding: 15px; background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); border-radius: 8px; color: #856404;">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Important:</strong> Changing the template will replace your current form fields with new ones based on the selected template. Your existing data will be preserved where possible, but some fields may be lost if they don't exist in the new template.
            </div>
            <div class="templates-grid">
                @foreach($templates as $template)
                    <div class="template-card" data-template-id="{{ $template->id }}" onclick="selectNewTemplate({{ $template->id }})">
                        <div class="template-preview">
                            @if($template->preview_image)
                                <img src="{{ asset('storage/' . $template->preview_image) }}" alt="{{ $template->name }}">
                            @else
                                <div class="template-placeholder">
                                    <i class="fas fa-heart"></i>
                                    <span>{{ $template->name }}</span>
                                </div>
                            @endif
                            
                            @if($template->is_malaysian_design)
                                <div class="template-badge">
                                    <i class="fas fa-star-and-crescent"></i>
                                    Malaysian
                                </div>
                            @endif
                        </div>
                        <div class="template-info">
                            <h4>{{ $template->name }}</h4>
                            <p>{{ ucfirst($template->category) }}</p>
                            @if($template->description)
                                <p class="template-description">{{ $template->description }}</p>
                            @endif
                        </div>
                        <div class="template-actions">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="previewTemplateInModal({{ $template->id }})" style="pointer-events: auto;">
                                <i class="fas fa-eye"></i>
                                Preview
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeTemplateModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmTemplateChange()" disabled id="confirmBtn">
                    Change Template
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Template Preview Modal -->
<div class="modal" id="previewModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Template Preview</h3>
            <button type="button" class="modal-close" onclick="closePreviewModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <iframe id="previewFrame" src="" width="100%" height="600px" frameborder="0"></iframe>
        </div>
    </div>
</div>

<style>
.edit-container {
    max-width: 900px;
    margin: 0 auto;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 25px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.card-title {
    font-size: 24px;
    color: #2c3e50;
    margin-bottom: 5px;
}

.card-status, .card-template {
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 5px;
}

.status {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status.published {
    background: rgba(46, 204, 113, 0.2);
    color: #27ae60;
}

.status.draft {
    background: rgba(230, 126, 34, 0.2);
    color: #e67e22;
}

.card-actions {
    display: flex;
    gap: 15px;
}

.current-template {
    display: flex;
    gap: 20px;
    align-items: center;
    background: rgba(255, 255, 255, 0.1);
    padding: 20px;
    border-radius: 10px;
}

.template-preview {
    width: 120px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.template-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.template-placeholder {
    text-align: center;
    color: #7f8c8d;
    font-size: 12px;
}

.template-placeholder i {
    font-size: 24px;
    margin-bottom: 5px;
    display: block;
}

.template-info h4 {
    color: #2c3e50;
    margin-bottom: 5px;
}

.template-info p {
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 10px;
}

.form-sections {
    max-width: 800px;
    margin: 0 auto 40px;
}

.form-section {
    margin-bottom: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 30px;
}

.section-title {
    font-size: 20px;
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.form-input, .form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.2);
    color: #2c3e50;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: #ff6b9d;
    background: rgba(255, 255, 255, 0.3);
}

.form-help {
    color: #7f8c8d;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

.error-message {
    color: #e74c3c;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 40px;
}

.action-buttons {
    display: flex;
    gap: 15px;
    align-items: center;
}

.approval-notice {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: rgba(52, 152, 219, 0.1);
    border: 1px solid rgba(52, 152, 219, 0.3);
    border-radius: 8px;
    color: #2980b9;
    font-size: 14px;
    font-weight: 500;
}

.approval-notice i {
    color: #3498db;
    font-size: 16px;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: rgba(46, 204, 113, 0.1);
    border: 1px solid rgba(46, 204, 113, 0.3);
    color: #27ae60;
}

.alert-error {
    background: rgba(231, 76, 60, 0.1);
    border: 1px solid rgba(231, 76, 60, 0.3);
    color: #e74c3c;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1000;
    padding: 20px;
}

.modal-content {
    background: white;
    border-radius: 15px;
    max-width: 900px;
    margin: 0 auto;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
}

/* Dynamic Fields Styles */
.section-description {
    color: #7f8c8d;
    margin-bottom: 20px;
    font-size: 14px;
}

.variable-field {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 15px;
    align-items: start;
    margin-bottom: 15px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.variable-label {
    font-weight: 500;
    color: #2c3e50;
    font-size: 14px;
    padding-top: 8px;
}

.variable-input-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.variable-input {
    padding: 8px 12px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 6px;
    font-size: 14px;
    background: rgba(255, 255, 255, 0.2);
    color: #2c3e50;
}

.variable-input:focus {
    outline: none;
    border-color: #ff6b9d;
    background: rgba(255, 255, 255, 0.3);
}

.variable-file-input {
    padding: 8px 12px;
    border: 2px dashed rgba(255, 255, 255, 0.3);
    border-radius: 6px;
    font-size: 14px;
    background: rgba(255, 255, 255, 0.1);
    cursor: pointer;
    transition: border-color 0.2s;
    color: #2c3e50;
}

.variable-file-input:hover {
    border-color: #ff6b9d;
    background: rgba(255, 255, 255, 0.2);
}

.image-preview {
    position: relative;
    display: inline-block;
    margin-top: 8px;
}

.preview-image {
    max-width: 150px;
    max-height: 100px;
    border-radius: 6px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(231, 76, 60, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    line-height: 1;
    transition: background 0.3s ease;
}

.btn-remove-image:hover {
    background: rgba(231, 76, 60, 1);
}

.video-preview {
    position: relative;
    display: inline-block;
    margin-top: 8px;
}

.preview-video {
    max-width: 200px;
    height: 120px;
    border-radius: 6px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-remove-video {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(231, 76, 60, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    line-height: 1;
    transition: background 0.3s ease;
}

.btn-remove-video:hover {
    background: rgba(231, 76, 60, 1);
}

.loop-variable-section {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.loop-variable-section h4 {
    color: #2c3e50;
    font-size: 16px;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.loop-variable-section .variable-field {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 10px;
}
    align-items: center;
}

.modal-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #7f8c8d;
}

.modal-body {
    padding: 20px;
}

.templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.template-card {
    background: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.template-card.selected {
    border-color: #ff6b9d;
}

.template-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #667eea;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

.template-description {
    color: #4a5568;
    font-size: 0.85rem;
    line-height: 1.4;
    margin-top: 5px;
}

.template-actions {
    padding: 0 20px 20px;
    display: flex;
    gap: 10px;
}

.template-actions .btn {
    flex: 1;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    border-top: 1px solid #eee;
    padding-top: 20px;
}

@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        gap: 20px;
        align-items: stretch;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 20px;
        align-items: stretch;
    }
    
    .action-buttons {
        justify-content: center;
    }
    
    .templates-grid {
        grid-template-columns: 1fr;
    }
    
    .current-template {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
let selectedNewTemplateId = null;
let currentTemplateVariables = {};
let currentCardDetails = @json($card->card_details ?? []);

// Load dynamic fields on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDynamicFields();
});

function loadDynamicFields() {
    const templateId = document.getElementById('templateInput').value;
    if (templateId) {
        fetch(`/user/templates/${templateId}/data`)
            .then(response => response.json())
            .then(data => {
                console.log('Template data received:', data);
                currentTemplateVariables = data.default_variables || {};
                
                // If we have full_html_template, extract variables using the same logic as admin
                let extractedVariables = {};
                if (data.full_html_template) {
                    console.log('Extracting variables from full_html_template');
                    const variableNames = extractVariablesFromTemplate(data.full_html_template);
                    
                    // Create default values for extracted variables
                    variableNames.forEach(varName => {
                        if (currentTemplateVariables[varName]) {
                            extractedVariables[varName] = currentTemplateVariables[varName];
                        } else if (currentCardDetails[varName]) {
                            extractedVariables[varName] = currentCardDetails[varName];
                        } else {
                            // Set empty default for user to fill
                            extractedVariables[varName] = '';
                        }
                    });
                    
                    // Merge with existing template variables
                    currentTemplateVariables = { ...extractedVariables, ...currentTemplateVariables };
                }
                
                generateDynamicFields(currentTemplateVariables, currentCardDetails);
            })
            .catch(error => {
                console.error('Error fetching template data:', error);
            });
    }
}

function generateDynamicFields(variables, existingData = {}) {
    const container = document.getElementById('dynamicFields');
    const section = document.getElementById('templateVariablesSection');
    
    if (!variables || Object.keys(variables).length === 0) {
        section.style.display = 'none';
        return;
    }
    
    section.style.display = 'block';
    container.innerHTML = '';
    
    // Group variables by type
    const regularVariables = {};
    const loopVariables = {};
    
    Object.keys(variables).forEach(key => {
        const match = key.match(/^(.+)_(\d+)$/);
        if (match) {
            const baseName = match[1];
            const index = parseInt(match[2]);
            if (!loopVariables[baseName]) {
                loopVariables[baseName] = {};
            }
            loopVariables[baseName][index] = variables[key];
        } else {
            regularVariables[key] = variables[key];
        }
    });
    
    // Create regular fields
    Object.keys(regularVariables).forEach(key => {
        const existingValue = existingData[key] || regularVariables[key];
        createVariableField(key, existingValue, container);
    });
    
    // Create loop sections
    Object.keys(loopVariables).forEach(baseName => {
        createLoopSection(baseName, loopVariables[baseName], container, existingData);
    });
}

function createVariableField(key, defaultValue, container) {
    const fieldDiv = document.createElement('div');
    fieldDiv.className = 'variable-field';
    
    const label = document.createElement('div');
    label.className = 'variable-label';
    label.textContent = formatFieldLabel(key);
    
    const inputContainer = document.createElement('div');
    inputContainer.className = 'variable-input-container';
    
    let inputElement;
    
    // Determine input type based on variable name (following admin template logic)
    if (isDateTimeVariable(key)) {
        inputElement = createDateTimeInput(key, defaultValue);
    } else if (isDateVariable(key)) {
        inputElement = createDateInput(key, defaultValue);
    } else if (isTimeVariable(key)) {
        inputElement = createTimeInput(key, defaultValue);
    } else if (isImageVariable(key)) {
        inputElement = createImageInput(key, defaultValue);
    } else if (isVideoVariable(key)) {
        inputElement = createVideoInput(key, defaultValue);
    } else if (isAudioVariable(key)) {
        inputElement = createAudioInput(key, defaultValue);
    } else if (isTextareaVariable(key)) {
        inputElement = createTextareaInput(key, defaultValue);
    } else {
        inputElement = createTextInput(key, defaultValue);
    }
    
    inputContainer.appendChild(inputElement);
    fieldDiv.appendChild(label);
    fieldDiv.appendChild(inputContainer);
    container.appendChild(fieldDiv);
}

function createLoopSection(baseName, items, container, existingData = {}) {
    const sectionDiv = document.createElement('div');
    sectionDiv.className = 'loop-variable-section';
    
    const title = document.createElement('h4');
    title.innerHTML = `<i class="fas fa-images"></i> ${formatFieldLabel(baseName)} Collection`;
    sectionDiv.appendChild(title);
    
    const maxIndex = Math.max(...Object.keys(items).map(k => parseInt(k)));
    
    for (let i = 1; i <= maxIndex; i++) {
        const key = `${baseName}_${i}`;
        const existingValue = existingData[key] || items[i] || '';
        createVariableField(key, existingValue, sectionDiv);
    }
    
    container.appendChild(sectionDiv);
}

function createTextInput(key, defaultValue) {
    const input = document.createElement('input');
    input.type = 'text';
    input.name = `card_details[${key}]`;
    input.className = 'variable-input';
    input.value = defaultValue || '';
    input.placeholder = formatFieldLabel(key);
    return input;
}

function createTextareaInput(key, defaultValue) {
    const textarea = document.createElement('textarea');
    textarea.name = `card_details[${key}]`;
    textarea.className = 'variable-input';
    textarea.value = defaultValue || '';
    textarea.placeholder = formatFieldLabel(key);
    textarea.rows = 3;
    return textarea;
}

function createDateInput(key, defaultValue) {
    const input = document.createElement('input');
    input.type = 'date';
    input.name = `card_details[${key}]`;
    input.className = 'variable-input';
    input.value = defaultValue || '';
    return input;
}

function createTimeInput(key, defaultValue) {
    const input = document.createElement('input');
    input.type = 'time';
    input.name = `card_details[${key}]`;
    input.className = 'variable-input';
    input.value = defaultValue || '';
    return input;
}

function createDateTimeInput(key, defaultValue) {
    const input = document.createElement('input');
    input.type = 'datetime-local';
    input.name = `card_details[${key}]`;
    input.className = 'variable-input';
    
    // Handle existing value formatting for datetime-local
    let value = defaultValue || '';
    if (value) {
        try {
            const date = new Date(value);
            if (!isNaN(date.getTime())) {
                // Format to YYYY-MM-DDTHH:MM format required by datetime-local
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                value = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        } catch (e) {
            // If parsing fails, use original value
        }
    }
    
    input.value = value;
    input.placeholder = `Select date and time for ${formatFieldLabel(key)}`;
    return input;
}

function createAudioInput(key, defaultValue) {
    const container = document.createElement('div');
    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.gap = '8px';
    
    // Set existing value if available - show preview first
    if (defaultValue) {
        showAudioPreview(container, defaultValue.startsWith('http') ? defaultValue : `/storage/${defaultValue}`);
        
        // Add hidden input for existing audio
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `card_details[${key}]`;
        hiddenInput.value = defaultValue;
        container.appendChild(hiddenInput);
    }
    
    // Create file input for audio upload
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.name = `variable_files[${key}]`;
    fileInput.className = 'variable-file-input';
    fileInput.accept = 'audio/*,.mp3,.wav,.ogg';
    
    // Create URL display input (readonly)
    const urlInput = document.createElement('input');
    urlInput.type = 'text';
    urlInput.className = 'variable-input';
    urlInput.placeholder = `Upload audio for ${formatFieldLabel(key)}`;
    urlInput.readonly = true;
    
    if (defaultValue) {
        urlInput.value = defaultValue;
    }
    
    // Handle file upload with preview
    fileInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            
            // Show loading state
            urlInput.value = 'File selected: ' + file.name;
            urlInput.style.color = '#718096';
            
            // Create preview immediately
            const url = URL.createObjectURL(file);
            showAudioPreview(container, url);
            urlInput.value = 'Audio uploaded and ready';
            urlInput.style.color = '#2d3748';
        }
    });
    
    container.appendChild(fileInput);
    container.appendChild(urlInput);
    
    return container;
}

function createImageInput(key, defaultValue) {
    const container = document.createElement('div');
    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.gap = '8px';
    
    // Set existing value if available - show preview first
    if (defaultValue) {
        showImagePreview(container, defaultValue.startsWith('http') ? defaultValue : `/storage/${defaultValue}`);
        
        // Add hidden input for existing image
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `card_details[${key}]`;
        hiddenInput.value = defaultValue;
        container.appendChild(hiddenInput);
    }
    
    // Create file input for photo upload
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.name = `variable_files[${key}]`;
    fileInput.className = 'variable-file-input';
    fileInput.accept = 'image/*';
    
    // Create URL display input (readonly)
    const urlInput = document.createElement('input');
    urlInput.type = 'text';
    urlInput.className = 'variable-input';
    urlInput.placeholder = `Upload an image for ${formatFieldLabel(key)}`;
    urlInput.readonly = true;
    
    if (defaultValue) {
        urlInput.value = defaultValue;
    }
    
    // Handle file upload with preview
    fileInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            
            // Show loading state
            urlInput.value = 'File selected: ' + file.name;
            urlInput.style.color = '#718096';
            
            // Create preview immediately
            const reader = new FileReader();
            reader.onload = function(event) {
                showImagePreview(container, event.target.result);
                urlInput.value = 'Image uploaded and ready';
                urlInput.style.color = '#2d3748';
            };
            reader.readAsDataURL(file);
        }
    });
    
    container.appendChild(fileInput);
    container.appendChild(urlInput);
    
    return container;
}

function createVideoInput(key, defaultValue) {
    const container = document.createElement('div');
    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.gap = '8px';
    
    // Set existing value if available - show preview first
    if (defaultValue) {
        showVideoPreview(container, defaultValue.startsWith('http') ? defaultValue : `/storage/${defaultValue}`);
        
        // Add hidden input for existing video
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `card_details[${key}]`;
        hiddenInput.value = defaultValue;
        container.appendChild(hiddenInput);
    }
    
    // Create file input for video upload
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.name = `variable_files[${key}]`;
    fileInput.className = 'variable-file-input';
    fileInput.accept = 'video/*';
    
    // Create URL display input (readonly)
    const urlInput = document.createElement('input');
    urlInput.type = 'text';
    urlInput.className = 'variable-input';
    urlInput.placeholder = `Upload a video for ${formatFieldLabel(key)}`;
    urlInput.readonly = true;
    
    if (defaultValue) {
        urlInput.value = defaultValue;
    }
    
    // Handle file upload with preview
    fileInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            
            // Show loading state
            urlInput.value = 'File selected: ' + file.name;
            urlInput.style.color = '#718096';
            
            // Create preview immediately
            const url = URL.createObjectURL(file);
            showVideoPreview(container, url);
            urlInput.value = 'Video uploaded and ready';
            urlInput.style.color = '#2d3748';
        }
    });
    
    container.appendChild(fileInput);
    container.appendChild(urlInput);
    
    return container;
}

function showImagePreview(container, imageUrl) {
    // Remove existing preview
    const existingPreview = container.querySelector('.image-preview');
    if (existingPreview) {
        existingPreview.remove();
    }
    
    // Create new preview
    const preview = document.createElement('div');
    preview.className = 'image-preview';
    preview.style.marginBottom = '10px';
    preview.style.position = 'relative';
    preview.style.display = 'inline-block';
    
    const img = document.createElement('img');
    img.src = imageUrl;
    img.style.maxWidth = '150px';
    img.style.maxHeight = '100px';
    img.style.borderRadius = '8px';
    img.style.border = '1px solid #e2e8f0';
    img.style.objectFit = 'cover';
    img.className = 'preview-image';
    
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn-remove-image';
    removeBtn.innerHTML = '×';
    removeBtn.style.position = 'absolute';
    removeBtn.style.top = '5px';
    removeBtn.style.right = '5px';
    removeBtn.style.background = 'rgba(239, 68, 68, 0.9)';
    removeBtn.style.color = 'white';
    removeBtn.style.border = 'none';
    removeBtn.style.borderRadius = '50%';
    removeBtn.style.width = '24px';
    removeBtn.style.height = '24px';
    removeBtn.style.cursor = 'pointer';
    removeBtn.style.display = 'flex';
    removeBtn.style.alignItems = 'center';
    removeBtn.style.justifyContent = 'center';
    removeBtn.style.fontSize = '16px';
    removeBtn.style.lineHeight = '1';
    
    removeBtn.addEventListener('click', function() {
        const urlInput = container.querySelector('.variable-input');
        urlInput.value = '';
        preview.remove();
        
        // Clear the file input
        const fileInput = container.querySelector('.variable-file-input');
        if (fileInput) {
            fileInput.value = '';
        }
        
        // Clear hidden input for existing files
        const hiddenInput = container.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            hiddenInput.value = '';
        }
    });
    
    preview.appendChild(img);
    preview.appendChild(removeBtn);
    
    // Insert preview at the beginning of the container
    container.insertBefore(preview, container.firstChild);
}

function showVideoPreview(container, videoUrl) {
    // Remove existing preview
    const existingPreview = container.querySelector('.video-preview');
    if (existingPreview) {
        existingPreview.remove();
    }
    
    // Create new preview
    const preview = document.createElement('div');
    preview.className = 'video-preview';
    preview.style.marginBottom = '10px';
    preview.style.position = 'relative';
    preview.style.display = 'inline-block';
    
    const video = document.createElement('video');
    video.src = videoUrl;
    video.controls = true;
    video.style.maxWidth = '200px';
    video.style.height = '120px';
    video.style.borderRadius = '8px';
    video.style.border = '1px solid #e2e8f0';
    video.style.objectFit = 'cover';
    video.className = 'preview-video';
    
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn-remove-video';
    removeBtn.innerHTML = '×';
    removeBtn.style.position = 'absolute';
    removeBtn.style.top = '5px';
    removeBtn.style.right = '5px';
    removeBtn.style.background = 'rgba(239, 68, 68, 0.9)';
    removeBtn.style.color = 'white';
    removeBtn.style.border = 'none';
    removeBtn.style.borderRadius = '50%';
    removeBtn.style.width = '24px';
    removeBtn.style.height = '24px';
    removeBtn.style.cursor = 'pointer';
    removeBtn.style.display = 'flex';
    removeBtn.style.alignItems = 'center';
    removeBtn.style.justifyContent = 'center';
    removeBtn.style.fontSize = '16px';
    removeBtn.style.lineHeight = '1';
    
    removeBtn.addEventListener('click', function() {
        const urlInput = container.querySelector('.variable-input');
        urlInput.value = '';
        preview.remove();
        
        // Clear the file input
        const fileInput = container.querySelector('.variable-file-input');
        if (fileInput) {
            fileInput.value = '';
        }
        
        // Clear hidden input for existing files
        const hiddenInput = container.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            hiddenInput.value = '';
        }
    });
    
    preview.appendChild(video);
    preview.appendChild(removeBtn);
    
    // Insert preview at the beginning of the container
    container.insertBefore(preview, container.firstChild);
}

function showAudioPreview(container, audioUrl) {
    // Remove existing preview
    const existingPreview = container.querySelector('.audio-preview');
    if (existingPreview) {
        existingPreview.remove();
    }
    
    // Create new preview
    const preview = document.createElement('div');
    preview.className = 'audio-preview';
    preview.style.marginBottom = '10px';
    preview.style.position = 'relative';
    preview.style.display = 'inline-block';
    
    const audio = document.createElement('audio');
    audio.src = audioUrl;
    audio.controls = true;
    audio.style.width = '100%';
    audio.style.maxWidth = '300px';
    audio.style.height = '40px';
    audio.style.borderRadius = '8px';
    audio.style.border = '1px solid #e2e8f0';
    audio.style.background = '#f8f9fa';
    audio.className = 'preview-audio';
    
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn-remove-audio';
    removeBtn.innerHTML = '×';
    removeBtn.style.position = 'absolute';
    removeBtn.style.top = '5px';
    removeBtn.style.right = '5px';
    removeBtn.style.background = 'rgba(239, 68, 68, 0.9)';
    removeBtn.style.color = 'white';
    removeBtn.style.border = 'none';
    removeBtn.style.borderRadius = '50%';
    removeBtn.style.width = '24px';
    removeBtn.style.height = '24px';
    removeBtn.style.cursor = 'pointer';
    removeBtn.style.display = 'flex';
    removeBtn.style.alignItems = 'center';
    removeBtn.style.justifyContent = 'center';
    removeBtn.style.fontSize = '16px';
    removeBtn.style.lineHeight = '1';
    
    removeBtn.addEventListener('click', function() {
        const urlInput = container.querySelector('.variable-input');
        urlInput.value = '';
        preview.remove();
        
        // Clear the file input
        const fileInput = container.querySelector('.variable-file-input');
        if (fileInput) {
            fileInput.value = '';
        }
        
        // Clear hidden input for existing files
        const hiddenInput = container.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            hiddenInput.value = '';
        }
    });
    
    preview.appendChild(audio);
    preview.appendChild(removeBtn);
    
    // Insert preview at the beginning of the container
    container.insertBefore(preview, container.firstChild);
}

function removePreview(key) {
    const preview = document.getElementById(`preview_${key}`);
    if (preview) {
        preview.remove();
    }
    
    // Clear the file input
    const fileInput = document.querySelector(`[name="variable_files[${key}]"]`);
    if (fileInput) {
        fileInput.value = '';
    }
    
    // Clear hidden input for existing files
    const hiddenInput = document.querySelector(`[name="card_details[${key}]"]`);
    if (hiddenInput) {
        hiddenInput.value = '';
    }
}

// Variable extraction and helper functions now handled by external JS file

function changeTemplate() {
    document.getElementById('templateModal').style.display = 'block';
}

function closeTemplateModal() {
    document.getElementById('templateModal').style.display = 'none';
    // Reset selection
    document.querySelectorAll('.template-card').forEach(card => {
        card.classList.remove('selected');
    });
    selectedNewTemplateId = null;
    document.getElementById('confirmBtn').disabled = true;
}

function selectNewTemplate(templateId) {
    // Prevent event bubbling for preview button
    event.stopPropagation();
    
    // Skip if this is the current template
    const currentTemplateId = document.getElementById('templateInput').value;
    if (templateId == currentTemplateId) {
        alert('This template is already selected.');
        return;
    }
    
    // Remove previous selection
    document.querySelectorAll('.template-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to current card
    document.querySelector(`[data-template-id="${templateId}"]`).classList.add('selected');
    
    selectedNewTemplateId = templateId;
    document.getElementById('confirmBtn').disabled = false;
}

function confirmTemplateChange() {
    if (selectedNewTemplateId) {
        console.log('Changing template to ID:', selectedNewTemplateId);
        
        // Get the selected template info directly from the modal card
        const selectedTemplateCard = document.querySelector(`[data-template-id="${selectedNewTemplateId}"]`);
        let newTemplateInfo = null;
        
        if (selectedTemplateCard) {
            const nameElement = selectedTemplateCard.querySelector('.template-info h4');
            const categoryElement = selectedTemplateCard.querySelector('.template-info p');
            const previewImg = selectedTemplateCard.querySelector('.template-preview img');
            const placeholder = selectedTemplateCard.querySelector('.template-placeholder span');
            
            newTemplateInfo = {
                name: nameElement ? nameElement.textContent : 'Unknown Template',
                category: categoryElement ? categoryElement.textContent : 'Unknown Category',
                previewImage: previewImg ? previewImg.src : null,
                placeholderName: placeholder ? placeholder.textContent : null
            };
            
            console.log('Extracted template info from modal:', newTemplateInfo);
        }
        
        // Save current form data before changing
        const currentData = {};
        const formInputs = document.querySelectorAll('#dynamicFields input, #dynamicFields textarea, #dynamicFields select');
        formInputs.forEach(input => {
            if (input.name && input.name.startsWith('card_details[')) {
                const key = input.name.replace('card_details[', '').replace(']', '');
                currentData[key] = input.value;
            }
        });
        
        console.log('Saved current data:', currentData);
        
        // Get current template name for comparison
        const currentTitleElement = document.querySelector('.current-template .template-info h4');
        const oldTemplateName = currentTitleElement ? currentTitleElement.textContent : 'Unknown Template';
        
        // Update template ID
        const templateInput = document.getElementById('templateInput');
        templateInput.value = selectedNewTemplateId;
        console.log('Template ID changed from', templateInput.value, 'to', selectedNewTemplateId);
        
        // IMMEDIATELY update the display with modal data (don't wait for API)
        if (newTemplateInfo) {
            updateTemplateDisplayImmediately(oldTemplateName, newTemplateInfo);
        }
        
        closeTemplateModal();
        
        // Preserve existing data where possible when loading new fields
        currentCardDetails = { ...currentCardDetails, ...currentData };
        
        // Reload dynamic fields for the new template
        loadDynamicFields();
        
        // Add a visual indicator that template is being changed
        const templateSection = document.querySelector('.current-template');
        if (templateSection) {
            templateSection.style.border = '2px solid #28a745';
            templateSection.style.backgroundColor = 'rgba(40, 167, 69, 0.05)';
            
            // Remove the highlight after 3 seconds
            setTimeout(() => {
                templateSection.style.border = '';
                templateSection.style.backgroundColor = '';
            }, 3000);
        }
        
        // Show confirmation with actual template names
        const successMessage = document.createElement('div');
        successMessage.className = 'alert alert-success';
        successMessage.innerHTML = `
            <i class="fas fa-check-circle"></i>
            Template changed from "<strong>${oldTemplateName}</strong>" to "<strong>${newTemplateInfo ? newTemplateInfo.name : 'New Template'}</strong>"! Your existing data has been preserved where possible.
        `;
        successMessage.style.position = 'fixed';
        successMessage.style.top = '20px';
        successMessage.style.right = '20px';
        successMessage.style.zIndex = '9999';
        successMessage.style.maxWidth = '400px';
        document.body.appendChild(successMessage);
        
        // Remove the message after 5 seconds
        setTimeout(() => {
            if (successMessage.parentNode) {
                successMessage.parentNode.removeChild(successMessage);
            }
        }, 5000);
    }
}

function updateTemplateDisplayImmediately(oldTemplateName, newTemplateInfo) {
    console.log('IMMEDIATE UPDATE - From:', oldTemplateName, 'To:', newTemplateInfo.name);
    
    // Update template preview image IMMEDIATELY
    const previewContainer = document.querySelector('.current-template .template-preview');
    if (previewContainer && newTemplateInfo) {
        if (newTemplateInfo.previewImage) {
            previewContainer.innerHTML = `<img src="${newTemplateInfo.previewImage}" alt="${newTemplateInfo.name}">`;
            console.log('✅ Updated preview image immediately');
        } else if (newTemplateInfo.placeholderName) {
            previewContainer.innerHTML = `
                <div class="template-placeholder">
                    <i class="fas fa-heart"></i>
                    <span>${newTemplateInfo.placeholderName}</span>
                </div>
            `;
            console.log('✅ Updated to placeholder immediately');
        }
    }
    
    // Update template info IMMEDIATELY with transition display
    const templateInfoContainer = document.querySelector('.current-template .template-info');
    if (templateInfoContainer && newTemplateInfo) {
        const titleElement = templateInfoContainer.querySelector('h4');
        const categoryElement = templateInfoContainer.querySelector('p');
        
        if (titleElement && categoryElement) {
            // Show immediate transition with clear before/after
            titleElement.innerHTML = `
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <span style="color: #e74c3c; text-decoration: line-through; font-size: 13px; opacity: 0.8;">
                        Previous: ${oldTemplateName}
                    </span>
                    <span style="color: #27ae60; font-weight: bold; font-size: 16px;">
                        <i class="fas fa-check-circle" style="margin-right: 5px;"></i>
                        ${newTemplateInfo.name}
                    </span>
                </div>
            `;
            
            categoryElement.innerHTML = `
                <span style="color: #27ae60; font-weight: bold;">
                    <i class="fas fa-tag" style="margin-right: 5px;"></i>
                    ${newTemplateInfo.category}
                </span>
            `;
            
            console.log('✅ Template info updated immediately!');
            
            // After 4 seconds, clean up to normal display
            setTimeout(() => {
                if (titleElement && categoryElement) {
                    titleElement.textContent = newTemplateInfo.name;
                    titleElement.style.color = '';
                    categoryElement.textContent = newTemplateInfo.category;
                    categoryElement.style.color = '';
                    console.log('✅ Cleaned up to normal display');
                }
            }, 4000);
        }
    }
    
    console.log('🎉 IMMEDIATE UPDATE COMPLETE!');
}

// Keep the old function as backup (in case we need API data)
function updateCurrentTemplateDisplay(templateId) {
    console.log('API-based update for template ID:', templateId);
    
    fetch(`/user/templates/${templateId}/data`)
        .then(response => response.json())
        .then(data => {
            console.log('📡 Template data from API:', data);
            // This can be used as a fallback or verification
        })
        .catch(error => {
            console.error('❌ API Error:', error);
        });
}

// Template preview functions
function previewTemplateInModal(templateId) {
    event.stopPropagation(); // Prevent template selection
    const modal = document.getElementById('previewModal');
    const iframe = document.getElementById('previewFrame');
    iframe.src = `/user/templates/${templateId}/preview`;
    modal.style.display = 'block';
}

function closePreviewModal() {
    const modal = document.getElementById('previewModal');
    const iframe = document.getElementById('previewFrame');
    modal.style.display = 'none';
    iframe.src = '';
}

function publishCard() {
    // Add hidden input to indicate publishing
    const form = document.getElementById('editForm');
    const publishInput = document.createElement('input');
    publishInput.type = 'hidden';
    publishInput.name = 'publish';
    publishInput.value = '1';
    form.appendChild(publishInput);
    
    form.submit();
}

// Close modal when clicking outside
window.onclick = function(event) {
    const templateModal = document.getElementById('templateModal');
    const previewModal = document.getElementById('previewModal');
    
    if (event.target === templateModal) {
        closeTemplateModal();
    }
    
    if (event.target === previewModal) {
        closePreviewModal();
    }
}
</script>

<!-- External JavaScript with complex regex patterns -->
<script src="{{ asset('js/user-card-form.js') }}"></script>

@endsection 