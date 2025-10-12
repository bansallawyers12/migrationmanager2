/**
 * EOI/ROI Management JavaScript
 * Handles CRUD operations for EOI/ROI records and points calculation
 */

(function() {
    'use strict';

    // State management
    const state = {
        clientId: null,
        selectedEoiId: null,
        eoiRecords: [],
        currentPoints: null
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        initEoiRoi();
    });

    function initEoiRoi() {
        // Get client ID from page data
        const clientDetailElement = document.querySelector('[data-client-id]');
        if (clientDetailElement) {
            state.clientId = clientDetailElement.dataset.clientId;
        }

        // Initialize components
        initializeSelect2();
        initializeDatepickers();
        bindEventHandlers();
        
        // Load EOI records when tab is activated
        $('#eoiroi').on('show', function() {
            loadEoiRecords();
        });
        
        // If tab is already visible, load records
        if ($('#eoiroi').is(':visible')) {
            loadEoiRecords();
        }
    }

    function initializeSelect2() {
        $('#eoi-states').select2({
            placeholder: 'Select state(s)',
            allowClear: true
        });
    }

    function initializeDatepickers() {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    }

    function bindEventHandlers() {
        // Add new EOI button
        $('#btn-add-eoi').on('click', showAddForm);

        // Form submission
        $('#eoi-roi-form').on('submit', handleFormSubmit);

        // Cancel button
        $('#btn-cancel-eoi').on('click', hideForm);

        // Delete button
        $('#btn-delete-eoi').on('click', handleDelete);

        // Table row click
        $(document).on('click', '#eoi-roi-tbody tr:not(.no-data-row)', handleRowClick);

        // Password toggle
        $('#toggle-password').on('click', togglePasswordVisibility);

        // Points refresh
        $('#btn-refresh-points').on('click', refreshPoints);

        // Points subclass selector
        $('#points-subclass-selector').on('change', handleSubclassChange);
    }

    // Load EOI records from API
    function loadEoiRecords() {
        if (!state.clientId) {
            console.error('Client ID not found');
            return;
        }

        const url = `/admin/clients/${state.clientId}/eoi-roi`;

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    state.eoiRecords = response.data;
                    renderEoiTable();
                    
                    // Load points if there are EOI records
                    if (state.eoiRecords.length > 0) {
                        $('#points-summary-section').show();
                        loadPoints();
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading EOI records:', xhr);
                showNotification('Error loading EOI records', 'error');
            }
        });
    }

    // Render EOI table
    function renderEoiTable() {
        const tbody = $('#eoi-roi-tbody');
        tbody.empty();

        if (state.eoiRecords.length === 0) {
            tbody.html(`
                <tr class="no-data-row">
                    <td colspan="9" class="text-center">
                        <i class="fas fa-info-circle"></i> No EOI/ROI records found. Click "Add New EOI" to get started.
                    </td>
                </tr>
            `);
            return;
        }

        state.eoiRecords.forEach(function(eoi) {
            const row = $('<tr>').attr('data-eoi-id', eoi.id);
            
            row.html(`
                <td>${eoi.eoi_number || 'N/A'}</td>
                <td>${eoi.formatted_subclasses || 'N/A'}</td>
                <td>${eoi.formatted_states || 'N/A'}</td>
                <td>${eoi.occupation || 'N/A'}</td>
                <td><strong>${eoi.points || 0}</strong></td>
                <td>${eoi.submission_date || 'N/A'}</td>
                <td>${eoi.roi || 'N/A'}</td>
                <td><span class="badge-status ${eoi.status}">${capitalizeFirst(eoi.status)}</span></td>
                <td>
                    <button class="btn btn-sm btn-primary" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            `);
            
            tbody.append(row);
        });
    }

    // Show add form
    function showAddForm() {
        resetForm();
        $('#form-title').text('Add New EOI Record');
        $('#btn-delete-eoi').hide();
        $('#eoi-roi-form-section').slideDown();
        state.selectedEoiId = null;
    }

    // Hide form
    function hideForm() {
        $('#eoi-roi-form-section').slideUp();
        resetForm();
    }

    // Reset form
    function resetForm() {
        $('#eoi-roi-form')[0].reset();
        $('#eoi-id').val('');
        $('#eoi-states').val(null).trigger('change');
        $('input[name="eoi_subclasses[]"]').prop('checked', false);
        state.selectedEoiId = null;
    }

    // Handle row click
    function handleRowClick(e) {
        const row = $(e.currentTarget);
        const eoiId = row.data('eoi-id');
        
        loadEoiRecord(eoiId);
    }

    // Load single EOI record
    function loadEoiRecord(eoiId) {
        const url = `/admin/clients/${state.clientId}/eoi-roi/${eoiId}`;

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    populateForm(response.data);
                    $('#form-title').text('Edit EOI Record');
                    $('#btn-delete-eoi').show();
                    $('#eoi-roi-form-section').slideDown();
                    state.selectedEoiId = eoiId;
                }
            },
            error: function(xhr) {
                console.error('Error loading EOI record:', xhr);
                showNotification('Error loading EOI record', 'error');
            }
        });
    }

    // Populate form with data
    function populateForm(data) {
        $('#eoi-id').val(data.id);
        $('#eoi-number').val(data.eoi_number);
        $('#eoi-occupation').val(data.occupation);
        $('#eoi-points').val(data.points);
        $('#eoi-submission-date').val(data.submission_date);
        $('#eoi-invitation-date').val(data.invitation_date);
        $('#eoi-nomination-date').val(data.nomination_date);
        $('#eoi-roi').val(data.roi);
        $('#eoi-status').val(data.status);

        // Set subclasses
        $('input[name="eoi_subclasses[]"]').prop('checked', false);
        if (data.eoi_subclasses) {
            data.eoi_subclasses.forEach(function(subclass) {
                $(`input[name="eoi_subclasses[]"][value="${subclass}"]`).prop('checked', true);
            });
        }

        // Set states
        if (data.eoi_states) {
            $('#eoi-states').val(data.eoi_states).trigger('change');
        }
    }

    // Handle form submission
    function handleFormSubmit(e) {
        e.preventDefault();

        // Validate at least one subclass is selected
        const checkedSubclasses = $('input[name="eoi_subclasses[]"]:checked');
        if (checkedSubclasses.length === 0) {
            showNotification('Please select at least one subclass', 'error');
            return false;
        }

        // Validate at least one state is selected
        const selectedStates = $('#eoi-states').val();
        if (!selectedStates || selectedStates.length === 0) {
            showNotification('Please select at least one state', 'error');
            return false;
        }

        const formData = new FormData($('#eoi-roi-form')[0]);
        const url = `/admin/clients/${state.clientId}/eoi-roi`;

        // Show loading
        $('#btn-save-eoi').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    hideForm();
                    loadEoiRecords();
                    loadPoints(); // Refresh points
                }
            },
            error: function(xhr) {
                console.error('Error saving EOI record:', xhr);
                let errorMsg = 'Error saving EOI record';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                showNotification(errorMsg, 'error');
            },
            complete: function() {
                $('#btn-save-eoi').prop('disabled', false).html('<i class="fas fa-save"></i> Save EOI');
            }
        });

        return false;
    }

    // Handle delete
    function handleDelete() {
        if (!state.selectedEoiId) {
            showNotification('No EOI record selected', 'error');
            return;
        }

        if (!confirm('Are you sure you want to delete this EOI record? This action cannot be undone.')) {
            return;
        }

        const url = `/admin/clients/${state.clientId}/eoi-roi/${state.selectedEoiId}`;

        $.ajax({
            url: url,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    hideForm();
                    loadEoiRecords();
                    loadPoints();
                }
            },
            error: function(xhr) {
                console.error('Error deleting EOI record:', xhr);
                showNotification('Error deleting EOI record', 'error');
            }
        });
    }

    // Load points calculation
    function loadPoints(subclass) {
        if (!state.clientId) return;

        subclass = subclass || $('#points-subclass-selector').val() || null;

        const url = `/admin/clients/${state.clientId}/eoi-roi/calculate-points?subclass=${subclass || ''}`;

        $('#points-summary-content').html('<div class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Calculating points...</div>');

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    state.currentPoints = response.data;
                    renderPointsSummary(response.data);
                }
            },
            error: function(xhr) {
                console.error('Error calculating points:', xhr);
                $('#points-summary-content').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Error calculating points</div>');
            }
        });
    }

    // Render points summary
    function renderPointsSummary(data) {
        const content = $('#points-summary-content');
        
        let html = `
            <div class="points-total-badge">${data.total} Points</div>
            
            <div class="points-breakdown">
                <h4>Points Breakdown</h4>
        `;

        // Render breakdown
        for (const [category, info] of Object.entries(data.breakdown)) {
            const displayCategory = category.replace('_', ' ').toUpperCase();
            html += `
                <div class="points-breakdown-item">
                    <span><strong>${displayCategory}:</strong> ${info.detail}</span>
                    <span class="badge badge-primary">${info.points} pts</span>
                </div>
            `;
        }

        html += '</div>';

        // Render warnings
        if (data.warnings && data.warnings.length > 0) {
            html += '<div class="points-warnings"><h4>Upcoming Changes & Warnings</h4>';
            
            data.warnings.forEach(function(warning) {
                html += `
                    <div class="points-warning severity-${warning.severity}">
                        <strong>${warning.type.replace('_', ' ').toUpperCase()}:</strong> ${warning.message}
                    </div>
                `;
            });
            
            html += '</div>';
        }

        content.html(html);
    }

    // Refresh points
    function refreshPoints() {
        loadPoints();
    }

    // Handle subclass change
    function handleSubclassChange() {
        const subclass = $('#points-subclass-selector').val();
        loadPoints(subclass);
    }

    // Toggle password visibility
    function togglePasswordVisibility() {
        const input = $('#eoi-password');
        const icon = $('#toggle-password i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }

    // Show notification
    function showNotification(message, type) {
        type = type || 'info';
        
        // Use toastr if available
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            alert(message);
        }
    }

    // Capitalize first letter
    function capitalizeFirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Export for external access if needed
    window.EoiRoi = {
        reload: loadEoiRecords,
        refreshPoints: loadPoints
    };

})();

