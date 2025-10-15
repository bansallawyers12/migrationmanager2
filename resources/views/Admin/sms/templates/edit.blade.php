@extends('layouts.admin')
@section('title', 'Edit SMS Template')

@section('styles')
<style>
    .template-form-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .template-form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .template-form-header h2 {
        margin: 0 0 10px 0;
        font-size: 1.8rem;
        font-weight: 600;
    }
    
    .template-form-header p {
        margin: 0;
        opacity: 0.9;
    }
    
    .template-form-content {
        background: white;
        padding: 30px;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f8f9fa;
    }
    
    .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        display: block;
    }
    
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .message-textarea {
        resize: vertical;
        min-height: 120px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .char-counter {
        text-align: right;
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .char-counter.warning {
        color: #fd7e14;
    }
    
    .char-counter.danger {
        color: #dc3545;
    }
    
    .sms-parts-info {
        background: #e7f3ff;
        border-left: 4px solid #4dabf7;
        padding: 12px 15px;
        border-radius: 0 8px 8px 0;
        margin-top: 10px;
        font-size: 0.9rem;
        color: #2c5aa0;
    }
    
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
    }
    
    .category-option {
        position: relative;
    }
    
    .category-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .category-label {
        display: block;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .category-option input[type="radio"]:checked + .category-label {
        border-color: #667eea;
        background: #f0f3ff;
        color: #667eea;
    }
    
    .category-label:hover {
        border-color: #667eea;
        background: #f8f9fa;
    }
    
    .variables-section {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-top: 15px;
    }
    
    .variable-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        padding: 8px 12px;
        background: white;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }
    
    .variable-input {
        flex: 1;
        border: none;
        outline: none;
        padding: 4px 8px;
        background: transparent;
    }
    
    .btn-remove-variable {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 4px 8px;
        cursor: pointer;
        font-size: 0.8rem;
    }
    
    .btn-add-variable {
        background: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 15px;
        cursor: pointer;
        font-size: 0.9rem;
        margin-top: 10px;
    }
    
    .template-stats {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .stat-item {
        display: inline-block;
        margin-right: 20px;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .stat-value {
        font-weight: 600;
        color: #495057;
    }
    
    .template-preview {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }
    
    .preview-message {
        background: white;
        padding: 15px;
        border-radius: 6px;
        border-left: 3px solid #667eea;
        font-size: 1rem;
        line-height: 1.4;
        margin-bottom: 10px;
    }
    
    .preview-stats {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .form-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 15px 30px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 150px;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .btn-secondary {
        background: #6c757d;
        border: none;
        color: white;
        padding: 15px 30px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 150px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-secondary:hover {
        background: #545b62;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        color: white;
        text-decoration: none;
    }
    
    .back-button {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .back-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        color: white;
        text-decoration: none;
    }
    
    .help-text {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 5px;
        line-height: 1.4;
    }
    
    .coming-soon-notice {
        background: linear-gradient(135deg, #ffd93d 0%, #ff9f1c 100%);
        color: #333;
        padding: 15px 20px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 20px;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
<div class="template-form-container">
    <a href="{{ route('admin.sms.templates.index') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Templates
    </a>

    <div class="coming-soon-notice">
        <i class="fas fa-info-circle"></i>
        <strong>Sprint 4 Feature:</strong> This form is being prepared for Sprint 4. Template editing will be fully functional then!
    </div>

    <div class="template-form-header">
        <h2><i class="fas fa-edit"></i> Edit SMS Template</h2>
        <p>Update your reusable SMS message template</p>
    </div>

    <form id="templateForm" class="template-form-content" action="{{ route('admin.sms.templates.update', $template->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        {{-- Template Statistics --}}
        <div class="template-stats">
            <div class="stat-item">
                <span class="stat-value">{{ $template->usage_count }}</span>
                times used
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ $template->created_at->format('M j, Y') }}</span>
                created
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ $template->updated_at->format('M j, Y') }}</span>
                last updated
            </div>
            @if($template->usage_count > 0)
            <div class="stat-item text-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Changes will affect future uses only
            </div>
            @endif
        </div>
        
        {{-- Basic Information --}}
        <div class="form-section">
            <h4 class="section-title">
                <i class="fas fa-info-circle"></i>
                Template Information
            </h4>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="title">Template Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-control" 
                               value="{{ $template->title }}"
                               placeholder="e.g., Appointment Reminder"
                               required>
                        <div class="help-text">
                            Give your template a descriptive name that makes it easy to find later.
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="is_active">Status</label>
                        <select id="is_active" name="is_active" class="form-control">
                            <option value="1" {{ $template->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$template->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description (Optional)</label>
                <textarea id="description" 
                          name="description" 
                          class="form-control" 
                          rows="2"
                          placeholder="Brief description of when to use this template...">{{ $template->description }}</textarea>
            </div>
        </div>

        {{-- Category --}}
        <div class="form-section">
            <h4 class="section-title">
                <i class="fas fa-tags"></i>
                Template Category
            </h4>
            
            <div class="category-grid">
                <div class="category-option">
                    <input type="radio" id="cat_verification" name="category" value="verification" {{ $template->category === 'verification' ? 'checked' : '' }}>
                    <label for="cat_verification" class="category-label">
                        <i class="fas fa-shield-alt"></i><br>
                        Verification
                    </label>
                </div>
                
                <div class="category-option">
                    <input type="radio" id="cat_notification" name="category" value="notification" {{ $template->category === 'notification' ? 'checked' : '' }}>
                    <label for="cat_notification" class="category-label">
                        <i class="fas fa-bell"></i><br>
                        Notification
                    </label>
                </div>
                
                <div class="category-option">
                    <input type="radio" id="cat_reminder" name="category" value="reminder" {{ $template->category === 'reminder' ? 'checked' : '' }}>
                    <label for="cat_reminder" class="category-label">
                        <i class="fas fa-calendar-alt"></i><br>
                        Reminder
                    </label>
                </div>
                
                <div class="category-option">
                    <input type="radio" id="cat_welcome" name="category" value="welcome" {{ $template->category === 'welcome' ? 'checked' : '' }}>
                    <label for="cat_welcome" class="category-label">
                        <i class="fas fa-hand-peace"></i><br>
                        Welcome
                    </label>
                </div>
                
                <div class="category-option">
                    <input type="radio" id="cat_other" name="category" value="" {{ empty($template->category) ? 'checked' : '' }}>
                    <label for="cat_other" class="category-label">
                        <i class="fas fa-ellipsis-h"></i><br>
                        Other
                    </label>
                </div>
            </div>
        </div>

        {{-- Message Content --}}
        <div class="form-section">
            <h4 class="section-title">
                <i class="fas fa-comment"></i>
                Message Content
            </h4>
            
            <div class="form-group">
                <label for="message">SMS Message <span class="text-danger">*</span></label>
                <textarea id="message" 
                          name="message" 
                          class="form-control message-textarea" 
                          placeholder="Enter your SMS template message here. Use {variable_name} for dynamic content..."
                          maxlength="1600"
                          required>{{ $template->message }}</textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 1600 characters
                </div>
                <div class="sms-parts-info">
                    <i class="fas fa-info-circle"></i>
                    This message will be sent as <strong><span id="smsParts">1</span> SMS part(s)</strong>.
                    Each part can contain up to 160 characters.
                </div>
            </div>
            
            {{-- Variables --}}
            <div class="variables-section">
                <h6><i class="fas fa-code"></i> Template Variables</h6>
                <p class="text-muted">Add variables that can be replaced when sending the SMS. Use {variable_name} in your message.</p>
                
                <div id="variablesList">
                    <!-- Variables will be loaded here -->
                </div>
                
                <button type="button" class="btn-add-variable" onclick="addVariable()">
                    <i class="fas fa-plus"></i> Add Variable
                </button>
                
                <div class="help-text" style="margin-top: 10px;">
                    <strong>Common variables:</strong> {client_name}, {appointment_date}, {appointment_time}, {amount}, {deadline}
                </div>
            </div>
        </div>

        {{-- Preview --}}
        <div class="form-section">
            <h4 class="section-title">
                <i class="fas fa-eye"></i>
                Template Preview
            </h4>
            
            <div class="template-preview">
                <h6>Message Preview:</h6>
                <div class="preview-message" id="previewMessage">
                    Enter your message above to see a preview here...
                </div>
                <div class="preview-stats" id="previewStats">
                    Character count: 0 | SMS parts: 1
                </div>
            </div>
        </div>

        {{-- Form Buttons --}}
        <div class="form-buttons">
            <a href="{{ route('admin.sms.templates.index') }}" class="btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Update Template
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
let variableCount = 0;
const existingVariables = @json($template->variables ?? []);

// Message character counting and preview
$('#message').on('input', function() {
    const message = $(this).val();
    const length = message.length;
    
    // Update character counter
    $('#charCount').text(length);
    const parts = Math.ceil(length / 160) || 1;
    $('#smsParts').text(parts);
    
    // Update counter color
    const charCounter = $('#charCount');
    charCounter.removeClass('warning danger');
    if (length > 1200) {
        charCounter.addClass('danger');
    } else if (length > 800) {
        charCounter.addClass('warning');
    }
    
    // Update preview
    updatePreview();
});

// Update preview
function updatePreview() {
    const message = $('#message').val();
    const previewMessage = $('#previewMessage');
    const previewStats = $('#previewStats');
    
    if (message.trim() === '') {
        previewMessage.text('Enter your message above to see a preview here...');
        previewStats.text('Character count: 0 | SMS parts: 1');
        return;
    }
    
    // Replace variables with example values for preview
    let preview = message;
    preview = preview.replace(/\{client_name\}/g, 'John Smith');
    preview = preview.replace(/\{appointment_date\}/g, 'March 15, 2024');
    preview = preview.replace(/\{appointment_time\}/g, '2:30 PM');
    preview = preview.replace(/\{amount\}/g, '$150.00');
    preview = preview.replace(/\{deadline\}/g, 'March 20, 2024');
    
    // Replace any remaining variables with placeholder
    preview = preview.replace(/\{([^}]+)\}/g, '[Example Value]');
    
    previewMessage.text(preview);
    
    const charCount = message.length;
    const parts = Math.ceil(charCount / 160) || 1;
    previewStats.text(`Character count: ${charCount} | SMS parts: ${parts}`);
}

// Add variable
function addVariable(variableName = '') {
    variableCount++;
    
    const variableHtml = `
        <div class="variable-item" id="variable-${variableCount}">
            <span>{</span>
            <input type="text" 
                   class="variable-input" 
                   name="variables[]" 
                   value="${variableName}"
                   placeholder="variable_name"
                   onchange="updateVariablesList()">
            <span>}</span>
            <button type="button" class="btn-remove-variable" onclick="removeVariable(${variableCount})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    $('#variablesList').append(variableHtml);
}

// Remove variable
function removeVariable(id) {
    $(`#variable-${id}`).fadeOut(function() {
        $(this).remove();
        updateVariablesList();
    });
}

// Update variables list
function updateVariablesList() {
    // This would update the variables array for the backend
    // For now, just update the preview
    updatePreview();
}

// Load existing variables
function loadExistingVariables() {
    if (existingVariables && existingVariables.length > 0) {
        existingVariables.forEach(variable => {
            addVariable(variable);
        });
    }
}

// Form submission
$('#templateForm').on('submit', function(e) {
    e.preventDefault();
    
    // Collect variables
    const variables = [];
    $('.variable-input').each(function() {
        const value = $(this).val().trim();
        if (value && !variables.includes(value)) {
            variables.push(value);
        }
    });
    
    // Add variables as hidden input
    $('input[name="variables"]').remove();
    if (variables.length > 0) {
        $(this).append(`<input type="hidden" name="variables" value='${JSON.stringify(variables)}'>`);
    }
    
    // Show loading state
    const submitBtn = $('.btn-primary');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
    
    // Submit form
    $.ajax({
        url: $(this).attr('action'),
        method: 'PUT',
        data: $(this).serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            // Success - redirect to templates list
            window.location.href = "{{ route('admin.sms.templates.index') }}";
        } else {
            // Error
            alert('Error: ' + (response.message || 'Failed to update template'));
        }
    })
    .fail(function(xhr) {
        let errorMessage = 'An error occurred while updating the template.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
            const errors = Object.values(xhr.responseJSON.errors).flat();
            errorMessage = errors.join('\n');
        }
        
        alert('Error: ' + errorMessage);
    })
    .always(function() {
        // Restore button
        submitBtn.prop('disabled', false).html(originalText);
    });
});

$(document).ready(function() {
    // Load existing variables
    loadExistingVariables();
    
    // Initialize character counter
    $('#message').trigger('input');
});
</script>
@endsection
