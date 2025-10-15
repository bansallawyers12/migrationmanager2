@extends('layouts.admin')
@section('title', 'Send SMS')

@section('styles')
<style>
    .sms-send-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .sms-send-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .sms-send-header h2 {
        margin: 0 0 10px 0;
        font-size: 1.8rem;
        font-weight: 600;
    }
    
    .sms-send-header p {
        margin: 0;
        opacity: 0.9;
    }
    
    .sms-send-form {
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
    }
    
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .phone-input-container {
        position: relative;
    }
    
    .phone-validator {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.2rem;
    }
    
    .phone-validator.valid {
        color: #28a745;
    }
    
    .phone-validator.invalid {
        color: #dc3545;
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
    
    .template-selector {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .template-selector:hover {
        border-color: #667eea;
        background: #f0f3ff;
    }
    
    .template-preview {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        display: none;
    }
    
    .template-preview.show {
        display: block;
    }
    
    .template-variables {
        margin-top: 15px;
    }
    
    .variable-input {
        margin-bottom: 10px;
    }
    
    .provider-info {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .provider-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .provider-badge.cellcast {
        background-color: #ffe6e6;
        color: #d63031;
    }
    
    .provider-badge.twilio {
        background-color: #e6f7ff;
        color: #0066cc;
    }
    
    .send-button {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 15px 30px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .send-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .send-button:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
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
    
    .client-search {
        position: relative;
    }
    
    .client-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 8px 8px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }
    
    .client-suggestion {
        padding: 12px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f1f3f4;
        transition: background-color 0.2s ease;
    }
    
    .client-suggestion:hover {
        background-color: #f8f9fa;
    }
    
    .client-suggestion:last-child {
        border-bottom: none;
    }
    
    .alert {
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .loading-spinner {
        background: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 15px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="sms-send-container">
    <a href="{{ route('admin.sms.dashboard') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to SMS Dashboard
    </a>

    <div class="sms-send-header">
        <h2><i class="fas fa-paper-plane"></i> Send SMS Message</h2>
        <p>Send SMS messages to clients or any phone number</p>
    </div>

    <form id="smsForm" class="sms-send-form">
        @csrf
        
        {{-- Recipient Section --}}
        <div class="form-section">
            <h4 class="section-title">
                <i class="fas fa-user"></i>
                Recipient Information
            </h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone">Phone Number <span class="text-danger">*</span></label>
                        <div class="phone-input-container">
                            <input type="text" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control" 
                                   placeholder="+61 400 000 000 or 0400 000 000"
                                   required>
                            <div class="phone-validator" id="phoneValidator"></div>
                        </div>
                        <small class="form-text text-muted">
                            Australian numbers will use Cellcast, international numbers will use Twilio
                        </small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="client_search">Link to Client (Optional)</label>
                        <div class="client-search">
                            <input type="text" 
                                   id="client_search" 
                                   class="form-control" 
                                   placeholder="Search client name...">
                            <input type="hidden" id="client_id" name="client_id">
                            <input type="hidden" id="contact_id" name="contact_id">
                            <div class="client-suggestions" id="clientSuggestions"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="provider-info" id="providerInfo" style="display: none;">
                <strong>Provider: </strong>
                <span class="provider-badge" id="providerBadge"></span>
                <span id="providerDescription"></span>
            </div>
        </div>

        {{-- Template Section --}}
        <div class="form-section">
            <h4 class="section-title">
                <i class="fas fa-file-alt"></i>
                Message Template (Optional)
            </h4>
            
            <div class="template-selector">
                <p><i class="fas fa-magic"></i> <strong>Use SMS Template</strong></p>
                <p class="text-muted">Select a pre-defined template to speed up message creation</p>
                <select id="template_id" name="template_id" class="form-control">
                    <option value="">Choose a template...</option>
                    {{-- Templates will be loaded via AJAX --}}
                </select>
            </div>
            
            <div class="template-preview" id="templatePreview">
                <h6><i class="fas fa-eye"></i> Template Preview:</h6>
                <div id="templateContent"></div>
                <div class="template-variables" id="templateVariables"></div>
            </div>
        </div>

        {{-- Message Section --}}
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
                          placeholder="Enter your SMS message here..."
                          maxlength="1600"
                          required></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 1600 characters
                </div>
                <div class="sms-parts-info">
                    <i class="fas fa-info-circle"></i>
                    This message will be sent as <strong><span id="smsParts">1</span> SMS part(s)</strong>.
                    Each part can contain up to 160 characters.
                </div>
            </div>
        </div>

        {{-- Submit Section --}}
        <div class="form-section">
            <button type="submit" class="send-button" id="sendButton">
                <i class="fas fa-paper-plane"></i>
                Send SMS Message
            </button>
        </div>
    </form>
</div>

{{-- Loading Overlay --}}
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <div class="spinner"></div>
        <h5>Sending SMS...</h5>
        <p class="text-muted">Please wait while we send your message</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let phoneTimeout;
    let templateTimeout;
    
    // Phone validation
    $('#phone').on('input', function() {
        clearTimeout(phoneTimeout);
        const phone = $(this).val().trim();
        const validator = $('#phoneValidator');
        const providerInfo = $('#providerInfo');
        
        if (phone.length === 0) {
            validator.removeClass('valid invalid').empty();
            providerInfo.hide();
            return;
        }
        
        phoneTimeout = setTimeout(() => {
            validatePhone(phone);
        }, 500);
    });
    
    function validatePhone(phone) {
        // Basic client-side validation (server will do final validation)
        const auMobileRegex = /^(\+?61|0)[4-5]\d{8}$/;
        const intlRegex = /^\+\d{10,15}$/;
        const validator = $('#phoneValidator');
        const providerInfo = $('#providerInfo');
        const providerBadge = $('#providerBadge');
        const providerDescription = $('#providerDescription');
        
        if (auMobileRegex.test(phone.replace(/\s/g, ''))) {
            validator.removeClass('invalid').addClass('valid').html('<i class="fas fa-check"></i>');
            providerBadge.removeClass('twilio').addClass('cellcast').html('<i class="fas fa-flag"></i> Cellcast');
            providerDescription.text('Australian mobile number - will be sent via Cellcast');
            providerInfo.show();
        } else if (intlRegex.test(phone.replace(/\s/g, ''))) {
            validator.removeClass('invalid').addClass('valid').html('<i class="fas fa-check"></i>');
            providerBadge.removeClass('cellcast').addClass('twilio').html('<i class="fas fa-globe"></i> Twilio');
            providerDescription.text('International number - will be sent via Twilio');
            providerInfo.show();
        } else {
            validator.removeClass('valid').addClass('invalid').html('<i class="fas fa-times"></i>');
            providerInfo.hide();
        }
    }
    
    // Message character counting
    $('#message').on('input', function() {
        const length = $(this).val().length;
        const charCount = $('#charCount');
        const smsParts = $('#smsParts');
        
        charCount.text(length);
        const parts = Math.ceil(length / 160) || 1;
        smsParts.text(parts);
        
        // Update counter color
        charCount.removeClass('warning danger');
        if (length > 1200) {
            charCount.addClass('danger');
        } else if (length > 800) {
            charCount.addClass('warning');
        }
    });
    
    // Template loading
    loadTemplates();
    
    $('#template_id').on('change', function() {
        const templateId = $(this).val();
        if (templateId) {
            loadTemplate(templateId);
        } else {
            $('#templatePreview').removeClass('show');
            $('#message').val('').trigger('input');
        }
    });
    
    function loadTemplates() {
        $.get('{{ route("admin.sms.templates.active") }}')
            .done(function(response) {
                if (response.success) {
                    const select = $('#template_id');
                    select.find('option:not(:first)').remove();
                    
                    response.data.forEach(template => {
                        select.append(`<option value="${template.id}">${template.title}</option>`);
                    });
                }
            })
            .fail(function() {
                console.log('Failed to load templates - will be available in Sprint 4');
            });
    }
    
    function loadTemplate(templateId) {
        $.get(`/admin/sms/templates/${templateId}`)
            .done(function(response) {
                if (response.success) {
                    const template = response.data;
                    $('#templateContent').html(`<em>${template.message}</em>`);
                    $('#message').val(template.message).trigger('input');
                    
                    // Handle template variables (future feature)
                    if (template.variables && template.variables.length > 0) {
                        let variablesHtml = '<h6>Template Variables:</h6>';
                        template.variables.forEach(variable => {
                            variablesHtml += `
                                <div class="variable-input">
                                    <label>${variable}:</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="variables[${variable}]" placeholder="Enter ${variable}">
                                </div>
                            `;
                        });
                        $('#templateVariables').html(variablesHtml);
                    } else {
                        $('#templateVariables').empty();
                    }
                    
                    $('#templatePreview').addClass('show');
                }
            })
            .fail(function() {
                alert('Failed to load template');
            });
    }
    
    // Client search (future feature)
    $('#client_search').on('input', function() {
        const query = $(this).val().trim();
        if (query.length >= 2) {
            // Future: AJAX client search
            console.log('Client search will be implemented in Sprint 4');
        } else {
            $('#clientSuggestions').hide();
        }
    });
    
    // Form submission
    $('#smsForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const loadingOverlay = $('#loadingOverlay');
        
        // Show loading
        loadingOverlay.show();
        
        $.ajax({
            url: '/admin/sms/send',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            loadingOverlay.hide();
            
            if (response.success) {
                // Success
                $('body').prepend(`
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i>
                        <strong>SMS Sent Successfully!</strong>
                        ${response.message}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                `);
                
                // Reset form
                $('#smsForm')[0].reset();
                $('#message').trigger('input');
                $('#providerInfo').hide();
                $('#templatePreview').removeClass('show');
                $('#phoneValidator').removeClass('valid invalid').empty();
                
                // Auto-hide success message
                setTimeout(() => {
                    $('.alert-success').fadeOut();
                }, 5000);
                
            } else {
                // Error
                $('body').prepend(`
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Failed to Send SMS:</strong>
                        ${response.message}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                `);
            }
            
            // Scroll to top to show message
            $('html, body').animate({ scrollTop: 0 }, 500);
        })
        .fail(function(xhr) {
            loadingOverlay.hide();
            
            let errorMessage = 'An error occurred while sending the SMS.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            $('body').prepend(`
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Error:</strong> ${errorMessage}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `);
            
            $('html, body').animate({ scrollTop: 0 }, 500);
        });
    });
    
    // Initialize character counter
    $('#message').trigger('input');
});
</script>
@endsection
