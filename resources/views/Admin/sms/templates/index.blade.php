@extends('layouts.admin')
@section('title', 'SMS Templates')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<style>
    .templates-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .templates-header-content h2 {
        margin: 0 0 10px 0;
        font-size: 1.8rem;
        font-weight: 600;
    }
    
    .templates-header-content p {
        margin: 0;
        opacity: 0.9;
        font-size: 1rem;
    }
    
    .templates-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .btn-create-template {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 12px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-create-template:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }
    
    .templates-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        border-color: #667eea;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
    }
    
    .stat-card.active {
        border-color: #28a745;
    }
    
    .stat-card.inactive {
        border-color: #dc3545;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #495057;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .templates-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .templates-table {
        width: 100%;
        margin: 0;
    }
    
    .templates-table th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #495057;
        font-weight: 600;
        border: none;
        padding: 15px 12px;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .templates-table td {
        padding: 15px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .templates-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .template-name {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }
    
    .template-description {
        font-size: 0.85rem;
        color: #6c757d;
        margin: 0;
    }
    
    .template-message-preview {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 0.9rem;
        color: #6c757d;
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        border-left: 3px solid #667eea;
    }
    
    .template-category {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #e9ecef;
        color: #495057;
    }
    
    .template-category.verification {
        background: #fff3cd;
        color: #856404;
    }
    
    .template-category.notification {
        background: #cce5ff;
        color: #004085;
    }
    
    .template-category.reminder {
        background: #ffe0e6;
        color: #c92a2a;
    }
    
    .template-category.welcome {
        background: #d4edda;
        color: #155724;
    }
    
    .status-toggle {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }
    
    .status-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .status-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }
    
    .status-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .status-slider {
        background-color: #28a745;
    }
    
    input:checked + .status-slider:before {
        transform: translateX(26px);
    }
    
    .usage-count {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .usage-count.high {
        color: #28a745;
        font-weight: 600;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: flex-end;
    }
    
    .btn-action {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 0.8rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-edit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-edit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-delete {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        color: white;
    }
    
    .btn-delete:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-use {
        background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
        color: white;
    }
    
    .btn-use:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(78, 205, 196, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .no-templates {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .no-templates i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #dee2e6;
    }
    
    .coming-soon-badge {
        background: linear-gradient(135deg, #ffd93d 0%, #ff9f1c 100%);
        color: #333;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-left: 8px;
    }
</style>
@endsection

@section('content')
<div class="listing-container">
    <div class="templates-header">
        <div class="templates-header-content">
            <h2><i class="fas fa-file-alt"></i> SMS Templates</h2>
            <p>Create and manage reusable SMS message templates</p>
        </div>
        <div class="templates-actions">
            <a href="{{ route('admin.sms.templates.create') }}" class="btn-create-template">
                <i class="fas fa-plus"></i>
                Create Template
                <span class="coming-soon-badge">Sprint 4</span>
            </a>
        </div>
    </div>

    {{-- Template Statistics --}}
    <div class="templates-stats">
        <div class="stat-card">
            <div class="stat-number">{{ $templates->total() }}</div>
            <div class="stat-label">Total Templates</div>
        </div>
        
        <div class="stat-card active">
            <div class="stat-number">{{ $templates->where('is_active', true)->count() }}</div>
            <div class="stat-label">Active Templates</div>
        </div>
        
        <div class="stat-card inactive">
            <div class="stat-number">{{ $templates->where('is_active', false)->count() }}</div>
            <div class="stat-label">Inactive Templates</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number">{{ $templates->sum('usage_count') }}</div>
            <div class="stat-label">Total Uses</div>
        </div>
    </div>

    {{-- Templates Table --}}
    <div class="templates-container">
        <table class="templates-table">
            <thead>
                <tr>
                    <th>Template</th>
                    <th>Category</th>
                    <th>Message Preview</th>
                    <th>Usage Count</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr>
                        <td>
                            <div class="template-name">{{ $template->title }}</div>
                            @if($template->description)
                                <div class="template-description">{{ $template->description }}</div>
                            @endif
                        </td>
                        <td>
                            @if($template->category)
                                <span class="template-category {{ $template->category }}">
                                    {{ ucfirst($template->category) }}
                                </span>
                            @else
                                <span class="text-muted">Uncategorized</span>
                            @endif
                        </td>
                        <td>
                            <div class="template-message-preview" title="{{ $template->message }}">
                                {{ $template->message }}
                            </div>
                        </td>
                        <td>
                            <div class="usage-count {{ $template->usage_count > 10 ? 'high' : '' }}">
                                <i class="fas fa-chart-line"></i>
                                {{ $template->usage_count }} uses
                            </div>
                        </td>
                        <td>
                            <label class="status-toggle">
                                <input type="checkbox" 
                                       {{ $template->is_active ? 'checked' : '' }}
                                       onchange="toggleTemplateStatus({{ $template->id }}, this.checked)">
                                <span class="status-slider"></span>
                            </label>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.sms.send.create') }}?template={{ $template->id }}" 
                                   class="btn-action btn-use" 
                                   title="Use Template">
                                    <i class="fas fa-paper-plane"></i>
                                </a>
                                
                                <a href="{{ route('admin.sms.templates.edit', $template->id) }}" 
                                   class="btn-action btn-edit" 
                                   title="Edit Template">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button type="button" 
                                        class="btn-action btn-delete" 
                                        title="Delete Template"
                                        onclick="deleteTemplate({{ $template->id }}, '{{ addslashes($template->title) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="no-templates">
                                <i class="fas fa-file-alt"></i>
                                <h5>No Templates Found</h5>
                                <p>Create your first SMS template to get started with quick messaging.</p>
                                <a href="{{ route('admin.sms.templates.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Template
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($templates->hasPages())
            <div class="listing-pagination">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
// Toggle template status
function toggleTemplateStatus(templateId, isActive) {
    $.ajax({
        url: `/admin/sms/templates/${templateId}`,
        method: 'PUT',
        data: {
            is_active: isActive ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            // Show success message
            showNotification('Template status updated successfully', 'success');
        } else {
            // Revert toggle and show error
            $(`input[onchange*="${templateId}"]`).prop('checked', !isActive);
            showNotification('Failed to update template status', 'error');
        }
    })
    .fail(function() {
        // Revert toggle and show error
        $(`input[onchange*="${templateId}"]`).prop('checked', !isActive);
        showNotification('Error updating template status', 'error');
    });
}

// Delete template
function deleteTemplate(templateId, templateName) {
    if (!confirm(`Are you sure you want to delete the template "${templateName}"?\n\nThis action cannot be undone.`)) {
        return;
    }
    
    $.ajax({
        url: `/admin/sms/templates/${templateId}`,
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            // Remove row from table
            $(`button[onclick*="${templateId}"]`).closest('tr').fadeOut(function() {
                $(this).remove();
                
                // Check if table is now empty
                if ($('.templates-table tbody tr:visible').length === 0) {
                    location.reload();
                }
            });
            
            showNotification('Template deleted successfully', 'success');
        } else {
            showNotification(response.message || 'Failed to delete template', 'error');
        }
    })
    .fail(function() {
        showNotification('Error deleting template', 'error');
    });
}

// Show notification
function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    
    const alert = $(`
        <div class="alert ${alertClass} alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-${icon}"></i> ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(alert);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        alert.fadeOut();
    }, 5000);
}

$(document).ready(function() {
    // Add tooltip for long message previews
    $('[title]').tooltip();
    
    // Future: Real-time usage statistics
    // Future: Template search and filtering
    // Future: Bulk operations
    
    console.log('SMS Templates page initialized - Sprint 4 will add full CRUD functionality');
});
</script>
@endsection
