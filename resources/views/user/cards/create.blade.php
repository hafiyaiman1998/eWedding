@extends('layouts.user.user')

@section('title', 'Create Wedding Card')
@section('page_title', 'Create Wedding Card')
@section('page_subtitle', 'Choose a template and personalize your invitation')

@section('content')
<div class="creation-wizard">
    <!-- Step 1: Template Selection -->
    <div class="wizard-step active" id="step1">
        <div class="content-card">
            <div class="step-header">
                <h2 class="step-title">
                    <i class="fas fa-palette"></i>
                    Choose Your Template
                </h2>
                <p class="step-description">Select the perfect design for your special day</p>
            </div>

            <div class="templates-grid">
                @foreach($templates as $template)
                    <div class="template-card" data-template-id="{{ $template->id }}" onclick="selectTemplate({{ $template->id }})">
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
                            <h3 class="template-name">{{ $template->name }}</h3>
                            <p class="template-category">{{ ucfirst($template->category) }}</p>
                            @if($template->description)
                                <p class="template-description">{{ $template->description }}</p>
                            @endif
                        </div>
                        
                        <div class="template-actions">
                            <button type="button" class="btn btn-secondary" onclick="previewTemplate({{ $template->id }})">
                                <i class="fas fa-eye"></i>
                                Preview
                            </button>
                            <button type="button" class="btn btn-primary select-btn">
                                <i class="fas fa-check"></i>
                                Select
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="step-actions">
                <button type="button" class="btn btn-primary" id="nextStep1" onclick="nextStep(2)" disabled>
                    <i class="fas fa-arrow-right"></i>
                    Continue with Selected Template
                </button>
            </div>
        </div>
    </div>

    <!-- Step 2: Wedding Details -->
    <div class="wizard-step" id="step2">
        <div class="content-card">
            <div class="step-header">
                <h2 class="step-title">
                    <i class="fas fa-heart"></i>
                    Wedding Details
                </h2>
                <p class="step-description">Fill in your wedding information based on the selected template</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-error">
                    <h4><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('user.cards.store') }}" id="cardForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="design_template_id" id="selectedTemplateId">
                
                <div class="form-sections">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="title" class="form-label">Card Title *</label>
                                <input type="text" id="title" name="title" class="form-input @error('title') error @enderror" 
                                       value="{{ old('title') }}" placeholder="e.g., Sarah & Ahmad's Wedding" required>
                                <small class="form-help">This will be displayed as the main title of your card</small>
                                @error('title')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Template Variables Section -->
                    <div class="form-section" id="templateVariablesSection" style="display: none;">
                        <h3 class="section-title">
                            <i class="fas fa-cogs"></i>
                            Wedding Information
                        </h3>
                        <p class="section-description">Customize your wedding details based on the selected template:</p>
                        <div id="dynamicFields"></div>
                        <input type="hidden" name="card_details_json" id="card_details_json">
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
                                      class="form-textarea @error('custom_message') error @enderror" rows="4" 
                                      placeholder="Add a personal message to your guests...">{{ old('custom_message') }}</textarea>
                            <small class="form-help">This message will appear on your wedding card</small>
                            @error('custom_message')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="step-actions">
                    <button type="button" class="btn btn-secondary" onclick="prevStep(1)">
                        <i class="fas fa-arrow-left"></i>
                        Back to Templates
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Create Wedding Card
                    </button>
                </div>
            </form>
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
.creation-wizard {
    max-width: 1000px;
    margin: 0 auto;
}

.wizard-step {
    display: none;
}

.wizard-step.active {
    display: block;
}

.content-card {
    background: white;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

.step-header {
    text-align: center;
    margin-bottom: 40px;
}

.step-title {
    color: #2d3748;
    font-size: 2rem;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.step-description {
    color: #718096;
    font-size: 1.1rem;
}

.templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.template-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    background: white;
}

.template-card:hover {
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.template-card.selected {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

.template-preview {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.template-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.template-placeholder {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f7fafc;
    color: #a0aec0;
}

.template-placeholder i {
    font-size: 3rem;
    margin-bottom: 10px;
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

.template-info {
    padding: 20px;
}

.template-name {
    color: #2d3748;
    font-size: 1.2rem;
    margin-bottom: 5px;
}

.template-category {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.template-description {
    color: #4a5568;
    font-size: 0.9rem;
    line-height: 1.4;
}

.template-actions {
    padding: 0 20px 20px;
    display: flex;
    gap: 10px;
}

.template-actions .btn {
    flex: 1;
}

.form-sections {
    margin-bottom: 30px;
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #e2e8f0;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.section-title {
    color: #2d3748;
    font-size: 1.4rem;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-description {
    color: #718096;
    margin-bottom: 20px;
    font-size: 14px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-label {
    color: #2d3748;
    font-weight: 500;
    font-size: 14px;
}

.form-input, .form-select, .form-textarea, .form-file {
    padding: 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-input:focus, .form-select:focus, .form-textarea:focus, .form-file:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.error, .form-select.error, .form-textarea.error, .form-file.error {
    border-color: #e53e3e;
}

.error-message {
    color: #e53e3e;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

.form-help {
    color: #718096;
    font-size: 12px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-secondary {
    background: #718096;
    color: white;
}

.btn-secondary:hover {
    background: #4a5568;
}

.step-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 900px;
    max-height: 80vh;
    overflow: hidden;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: #718096;
}

.modal-body {
    height: 600px;
}

.alert {
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    border: 2px solid transparent;
}

.alert-error {
    background: rgba(231, 76, 60, 0.1);
    border-color: rgba(231, 76, 60, 0.3);
    color: #e74c3c;
}

.alert h4 {
    margin: 0 0 15px 0;
    font-size: 16px;
    font-weight: 600;
}

.alert ul {
    margin: 0;
    padding-left: 20px;
}

.alert li {
    margin-bottom: 8px;
    font-size: 14px;
}

/* Dynamic Fields Styles */
.variable-field {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 15px;
    align-items: start;
    margin-bottom: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.variable-label {
    font-weight: 500;
    color: #2d3748;
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
    border: 1px solid #cbd5e0;
    border-radius: 6px;
    font-size: 14px;
}

.variable-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.variable-file-input {
    padding: 8px 12px;
    border: 2px dashed #cbd5e0;
    border-radius: 6px;
    font-size: 14px;
    background: #f8fafc;
    cursor: pointer;
    transition: border-color 0.2s;
}

.variable-file-input:hover {
    border-color: #667eea;
    background: #edf2f7;
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
    border: 1px solid #e2e8f0;
}

.btn-remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(239, 68, 68, 0.9);
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
    background: rgba(239, 68, 68, 1);
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
    border: 1px solid #e2e8f0;
}

.btn-remove-video {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(239, 68, 68, 0.9);
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
    background: rgba(239, 68, 68, 1);
}

.loop-variable-section {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.loop-variable-section h4 {
    color: #2d3748;
    font-size: 16px;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.loop-variable-section .variable-field {
    background: white;
    border: 1px solid #cbd5e0;
    margin-bottom: 10px;
}

@media (max-width: 768px) {
    .templates-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .step-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .step-actions .btn {
        width: 100%;
    }
    
    .variable-field {
        grid-template-columns: 1fr;
        gap: 8px;
    }
}
</style>

<script>
let selectedTemplateId = null;
let currentTemplateVariables = {};

// Initialize the form state on page load
document.addEventListener('DOMContentLoaded', function() {
    @if ($errors->any() || old('design_template_id'))
        // If there are validation errors or old input, show step 2
        selectedTemplateId = {{ old('design_template_id', 'null') }};
        if (selectedTemplateId) {
            document.getElementById('selectedTemplateId').value = selectedTemplateId;
            
            // Mark the selected template as selected
            const templateCard = document.querySelector(`[data-template-id="${selectedTemplateId}"]`);
            if (templateCard) {
                templateCard.classList.add('selected');
            }
            
            // Fetch template data and show step 2
            fetchTemplateData(selectedTemplateId);
            nextStep(2);
        }
    @endif
});

// Navigation functions
function nextStep(step) {
    document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
    document.getElementById(`step${step}`).classList.add('active');
}

function prevStep(step) {
    document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
    document.getElementById(`step${step}`).classList.add('active');
}

// Template selection
function selectTemplate(templateId) {
    // Remove previous selection
    document.querySelectorAll('.template-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to current card
    document.querySelector(`[data-template-id="${templateId}"]`).classList.add('selected');
    
    selectedTemplateId = templateId;
    document.getElementById('selectedTemplateId').value = templateId;
    document.getElementById('nextStep1').disabled = false;
    
    // Fetch template data and generate dynamic form fields
    fetchTemplateData(templateId);
}

function fetchTemplateData(templateId) {
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
                    } else {
                        // Set empty default for user to fill
                        extractedVariables[varName] = '';
                    }
                });
                
                // Merge with existing template variables
                currentTemplateVariables = { ...extractedVariables, ...currentTemplateVariables };
            }
            
            generateDynamicFields(currentTemplateVariables);
            
            // Auto-populate title if bride and groom names are available
            if (currentTemplateVariables.bride_name && currentTemplateVariables.groom_name) {
                const titleInput = document.getElementById('title');
                if (titleInput && !titleInput.value.trim()) {
                    titleInput.value = `${currentTemplateVariables.bride_name} & ${currentTemplateVariables.groom_name}'s Wedding`;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching template data:', error);
        });
}

function generateDynamicFields(variables) {
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
        createVariableField(key, regularVariables[key], container);
    });
    
    // Create loop sections
    Object.keys(loopVariables).forEach(baseName => {
        createLoopSection(baseName, loopVariables[baseName], container);
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
    
    // For create form, use empty values to let users input their own data
    const emptyValue = '';
    
    // Determine input type based on variable name (following admin template logic)
    if (isDateTimeVariable(key)) {
        inputElement = createDateTimeInput(key, emptyValue);
    } else if (isDateVariable(key)) {
        inputElement = createDateInput(key, emptyValue);
    } else if (isTimeVariable(key)) {
        inputElement = createTimeInput(key, emptyValue);
    } else if (isImageVariable(key)) {
        inputElement = createImageInput(key, emptyValue);
    } else if (isVideoVariable(key)) {
        inputElement = createVideoInput(key, emptyValue);
    } else if (isAudioVariable(key)) {
        inputElement = createAudioInput(key, emptyValue);
    } else if (isTextareaVariable(key)) {
        inputElement = createTextareaInput(key, emptyValue);
    } else {
        inputElement = createTextInput(key, emptyValue);
    }
    
    inputContainer.appendChild(inputElement);
    fieldDiv.appendChild(label);
    fieldDiv.appendChild(inputContainer);
    container.appendChild(fieldDiv);
}

function createLoopSection(baseName, items, container) {
    const sectionDiv = document.createElement('div');
    sectionDiv.className = 'loop-variable-section';
    
    const title = document.createElement('h4');
    title.innerHTML = `<i class="fas fa-images"></i> ${formatFieldLabel(baseName)} Collection`;
    sectionDiv.appendChild(title);
    
    const maxIndex = Math.max(...Object.keys(items).map(k => parseInt(k)));
    
    for (let i = 1; i <= maxIndex; i++) {
        const key = `${baseName}_${i}`;
        // For create form, use empty values to let users input their own data
        const emptyValue = '';
        createVariableField(key, emptyValue, sectionDiv);
    }
    
    container.appendChild(sectionDiv);
}

function createTextInput(key, defaultValue) {
    const input = document.createElement('input');
    input.type = 'text';
    input.name = `card_details[${key}]`;
    input.className = 'variable-input';
    // Use old input value if available (for validation errors)
    const oldValue = @json(old('card_details', []));
    input.value = oldValue[key] || defaultValue || '';
    input.placeholder = formatFieldLabel(key);
    return input;
}

function createTextareaInput(key, defaultValue) {
    const textarea = document.createElement('textarea');
    textarea.name = `card_details[${key}]`;
    textarea.className = 'variable-input';
    // Use old input value if available (for validation errors)
    const oldValue = @json(old('card_details', []));
    textarea.value = oldValue[key] || defaultValue || '';
    textarea.placeholder = formatFieldLabel(key);
    textarea.rows = 3;
    return textarea;
}

function createDateInput(key, defaultValue) {
    const input = document.createElement('input');
    input.type = 'date';
    input.name = `card_details[${key}]`;
    input.className = 'variable-input';
    // Use old input value if available (for validation errors)
    const oldValue = @json(old('card_details', []));
    input.value = oldValue[key] || defaultValue || '';
    return input;
}

function createTimeInput(key, defaultValue) {
    const input = document.createElement('input');
    input.type = 'time';
    input.name = `card_details[${key}]`;
    input.className = 'variable-input';
    // Use old input value if available (for validation errors)
    const oldValue = @json(old('card_details', []));
    input.value = oldValue[key] || defaultValue || '';
    return input;
}

function createDateTimeInput(key, defaultValue) {
    const input = document.createElement('input');
    input.type = 'datetime-local';
    input.name = `card_details[${key}]`;
    input.className = 'variable-input';
    // Use old input value if available (for validation errors)
    const oldValue = @json(old('card_details', []));
    
    // Handle existing value formatting for datetime-local
    let value = oldValue[key] || defaultValue || '';
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
        showAudioPreview(container, defaultValue);
        
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
        showImagePreview(container, defaultValue);
        
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
        showVideoPreview(container, defaultValue);
        
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
    });
    
    preview.appendChild(img);
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
    });
    
    preview.appendChild(video);
    preview.appendChild(removeBtn);
    
    // Insert preview at the beginning of the container
    container.insertBefore(preview, container.firstChild);
}

// Variable extraction now handled by external JS file

// Helper functions moved to external JS file

// Template preview
function previewTemplate(templateId) {
    const modal = document.getElementById('previewModal');
    const iframe = document.getElementById('previewFrame');
    iframe.src = `/user/templates/${templateId}/preview`;
    modal.classList.add('active');
}

function closePreviewModal() {
    const modal = document.getElementById('previewModal');
    const iframe = document.getElementById('previewFrame');
    modal.classList.remove('active');
    iframe.src = '';
}

// Form submission
document.getElementById('cardForm').addEventListener('submit', function(e) {
    // Collect all form data including files
    const formData = new FormData(this);
    
    // You can add any additional processing here if needed
    // The form will submit normally with all the dynamic fields
});

// Close modal when clicking outside
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePreviewModal();
    }
});
</script>

<!-- External JavaScript with complex regex patterns -->
<script src="{{ asset('js/user-card-form.js') }}"></script>

@endsection 