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
        console.log('[EOI-ROI] DOM ready, initializing...');
        initEoiRoi();
        // Don't load data immediately - wait for tab activation
    });

    function initEoiRoi() {
        // Get client ID from page configuration
        if (window.ClientDetailConfig && window.ClientDetailConfig.encodeId) {
            state.clientId = window.ClientDetailConfig.encodeId;
            console.log('[EOI-ROI] Client ID detected:', state.clientId);
        } else {
            console.error('[EOI-ROI] Client ID not found - ClientDetailConfig.encodeId missing');
            return;
        }

        // Initialize components
        initializeSelect2();
        initializeDatepickers();
        bindEventHandlers();
        
        // Listen for tab activation via sidebar tabs (using correct ID)
        $(document).on('click', '.client-nav-button[data-tab="eoiroi"]', function() {
            console.log('[EOI-ROI] Tab button clicked, loading records...');
            setTimeout(function() {
                if (state.clientId) {
                    loadEoiRecords();
                }
            }, 200);
        });
        
        // Check if tab is already active (e.g., direct URL navigation)
        if ($('#eoiroi-tab').hasClass('active')) {
            console.log('[EOI-ROI] Tab already active on load, loading records...');
            loadEoiRecords();
        }
        
        // Always show points summary section when EOI-ROI tab is initialized
        $('#points-summary-section').show();
        
        // Check if data was pre-loaded by sidebar-tabs.js
        if (window.tempEoiData) {
            console.log('[EOI-ROI] Found pre-loaded data, rendering...');
            state.eoiRecords = window.tempEoiData;
            renderEoiTable();
            delete window.tempEoiData;
        }
        
        console.log('[EOI-ROI] Initialized, waiting for tab activation');
    }

    function initializeSelect2() {
        $('#eoi-states').select2({
            placeholder: 'Select state(s)',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: Infinity,
            dropdownAutoWidth: true,
            tags: false,
            tokenSeparators: [','],
            closeOnSelect: false
        });
        
        // Initialize occupation autocomplete
        initializeOccupationAutocomplete();
    }

    function initializeDatepickers() {
        $('.eoi-datepicker').datepicker({
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

        // EOI reference link click
        $(document).on('click', '.eoi-ref-link', handleEoiRefClick);

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

        const url = `/clients/${state.clientId}/eoi-roi`;

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
                    
                    // Always show points section and load points calculation
                    $('#points-summary-section').show();
                    loadPoints();
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
                    <td colspan="8" class="text-center">
                        <i class="fas fa-info-circle"></i> No EOI/ROI records found. Click "Add New EOI" to get started.
                    </td>
                </tr>
            `);
            return;
        }

        state.eoiRecords.forEach(function(eoi) {
            const row = $('<tr>').attr('data-eoi-id', eoi.id);
            
            row.html(`
                <td><a href="#" class="eoi-ref-link" data-eoi-id="${eoi.id}">${eoi.eoi_number || 'N/A'}</a></td>
                <td>${eoi.formatted_subclasses || 'N/A'}</td>
                <td>${eoi.formatted_states || 'N/A'}</td>
                <td>${eoi.occupation || 'N/A'}</td>
                <td><strong>${eoi.points || 0}</strong></td>
                <td>${eoi.submission_date || 'N/A'}</td>
                <td>${eoi.roi || 'N/A'}</td>
                <td><span class="badge-status ${eoi.status}">${capitalizeFirst(eoi.status)}</span></td>
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
        clearOccupationSelection();
        state.selectedEoiId = null;
    }

    // Handle EOI reference click
    function handleEoiRefClick(e) {
        e.preventDefault();
        const link = $(e.currentTarget);
        const eoiId = link.data('eoi-id');
        
        loadEoiRecord(eoiId);
    }

    // Load single EOI record
    function loadEoiRecord(eoiId) {
        const url = `/clients/${state.clientId}/eoi-roi/${eoiId}`;

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
        
        // Handle occupation data
        if (data.occupation) {
            $('#eoi-occupation').val(data.occupation);
            // If we have ANZSCO data, populate hidden fields
            if (data.anzsco_occupation_id) {
                $('#eoi-anzsco-id').val(data.anzsco_occupation_id);
            }
            if (data.anzsco_code) {
                $('#eoi-anzsco-code').val(data.anzsco_code);
            }
        }
        
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

        // Validate occupation is selected from database
        const occupationInput = $('#eoi-occupation');
        const anzscoId = $('#eoi-anzsco-id').val();
        if (!occupationInput.val() || !anzscoId) {
            showNotification('Please select a valid occupation from the dropdown', 'error');
            occupationInput.focus();
            return false;
        }

        const formData = new FormData($('#eoi-roi-form')[0]);
        const url = `/clients/${state.clientId}/eoi-roi`;

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

        const url = `/clients/${state.clientId}/eoi-roi/${state.selectedEoiId}`;

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
        if (!state.clientId) {
            console.error('[EOI-ROI] Cannot load points - client ID not set');
            return;
        }

        subclass = subclass || $('#points-subclass-selector').val() || '189';

        const url = `/clients/${state.clientId}/eoi-roi/calculate-points?subclass=${subclass || ''}`;
        console.log('[EOI-ROI] Loading points from URL:', url);

        $('#points-summary-content').html('<div class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Calculating points...</div>');

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('[EOI-ROI] Points calculation response:', response);
                if (response.success) {
                    state.currentPoints = response.data;
                    renderPointsSummary(response.data);
                } else {
                    console.error('[EOI-ROI] Points calculation failed:', response.message);
                    $('#points-summary-content').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Points calculation failed: ' + (response.message || 'Unknown error') + '</div>');
                }
            },
            error: function(xhr) {
                console.error('[EOI-ROI] Error calculating points:', xhr);
                let errorMsg = 'Error calculating points';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMsg = 'Points calculation endpoint not found (404)';
                } else if (xhr.status === 500) {
                    errorMsg = 'Server error during points calculation (500)';
                }
                $('#points-summary-content').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMsg + '</div>');
            }
        });
    }

    // Render points summary
    function renderPointsSummary(data) {
        const content = $('#points-summary-content');
        
        // Map category names to display names
        const categoryNames = {
            'age': 'Age',
            'english': 'English Test',
            'education': 'Education',
            'australian_work_experience': 'Australian Work Experience',
            'overseas_work_experience': 'Overseas Work Experience',
            'australian_study': 'Australian Study',
            'specialist_education': 'Specialist Education',
            'regional_study': 'Regional Study',
            'naati_ccl': 'NAATI/CCL',
            'professional_year': 'Professional Year',
            'partner': 'Partner Skills',
            'nomination': 'Nomination'
        };

        // Build the main table rows
        let tableRows = '';
        for (const [category, info] of Object.entries(data.breakdown)) {
            const displayCategory = categoryNames[category] || category.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            const details = info.detail || 'Not claimed';
            
            tableRows += `
                <tr>
                    <td class="points-category">${displayCategory}</td>
                    <td class="points-details">${details}</td>
                    <td class="points-value">${info.points}</td>
                </tr>
            `;
        }

        // Build warnings section
        let warningsHtml = '';
        if (data.warnings && data.warnings.length > 0) {
            warningsHtml += '<div class="points-warnings-title">Upcoming Changes</div>';
            
            data.warnings.forEach(function(warning) {
                const iconClass = warning.severity === 'high' ? 'fas fa-exclamation-triangle' : 'fas fa-info-circle';
                warningsHtml += `
                    <div class="points-warning severity-${warning.severity}">
                        <i class="${iconClass} points-warning-icon"></i>
                        ${warning.message}
                    </div>
                `;
            });
        }

        const html = `
            <div class="points-summary-layout">
                <div class="points-summary-main">
                    <table class="points-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Details</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                        </tbody>
                    </table>
                </div>
                
                <div class="points-summary-sidebar">
                    <div class="points-total-display">
                        <div class="points-total-number">${data.total}</div>
                        <div class="points-total-label">Current total</div>
                    </div>
                    
                    ${warningsHtml}
                    
                    <div class="points-info-text">
                        Points calculated based on current client data from Personal Details, Qualifications, Experience, and Test Scores sections.
                    </div>
                </div>
            </div>
        `;

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

    // Initialize occupation autocomplete
    function initializeOccupationAutocomplete() {
        const occupationInput = $('#eoi-occupation');
        const autocompleteContainer = $('.autocomplete-items');
        
        let searchTimeout;
        
        occupationInput.on('input', function() {
            const query = $(this).val().trim();
            
            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            // Clear autocomplete if input is empty
            if (query.length === 0) {
                autocompleteContainer.hide();
                clearOccupationSelection();
                return;
            }
            
            // Debounce search - wait 300ms after user stops typing
            searchTimeout = setTimeout(() => {
                if (query.length >= 2) {
                    searchOccupations(query, autocompleteContainer[0], $(this));
                }
            }, 300);
        });
        
        occupationInput.on('focus', function() {
            const query = $(this).val().trim();
            if (query.length >= 2) {
                autocompleteContainer.show();
            }
        });
        
        // Hide autocomplete when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.form-group').length) {
                autocompleteContainer.hide();
            }
        });
        
        // Handle keyboard navigation
        occupationInput.on('keydown', function(e) {
            const items = autocompleteContainer.find('.autocomplete-item');
            const selected = autocompleteContainer.find('.autocomplete-item.selected');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (selected.length) {
                    selected.removeClass('selected');
                    selected.next().addClass('selected');
                } else {
                    items.first().addClass('selected');
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (selected.length) {
                    selected.removeClass('selected');
                    selected.prev().addClass('selected');
                } else {
                    items.last().addClass('selected');
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selected.length) {
                    selected.click();
                }
            } else if (e.key === 'Escape') {
                autocompleteContainer.hide();
            }
        });
    }
    
    // Search occupations via API
    async function searchOccupations(query, autocompleteContainer, inputElement) {
        try {
            // Show loading indicator
            autocompleteContainer.innerHTML = '<div class="autocomplete-item"><span class="anzsco-loading"></span> Searching...</div>';
            autocompleteContainer.style.display = 'block';
            
            const response = await fetch(`/anzsco/search?q=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`Search failed: ${response.status} ${response.statusText}`);
            }
            
            const occupations = await response.json();
            
            if (occupations.length === 0) {
                autocompleteContainer.innerHTML = '<div class="autocomplete-item text-muted">No occupations found</div>';
                return;
            }
            
            // Build autocomplete items
            autocompleteContainer.innerHTML = '';
            occupations.forEach(occ => {
                const item = document.createElement('div');
                item.className = 'autocomplete-item';
                
                item.innerHTML = `
                    <div>
                        <span class="anzsco-code">${occ.anzsco_code}</span> - 
                        <span class="anzsco-title">${occ.occupation_title}</span>
                    </div>
                `;
                
                item.addEventListener('click', function() {
                    fillOccupationData(inputElement, occ);
                    autocompleteContainer.innerHTML = '';
                    autocompleteContainer.style.display = 'none';
                });
                
                autocompleteContainer.appendChild(item);
            });
            
        } catch (error) {
            console.error('Occupation search error:', error);
            autocompleteContainer.innerHTML = '<div class="autocomplete-item text-danger">Error searching occupations. Please try again.</div>';
        }
    }
    
    // Fill occupation data into form fields
    function fillOccupationData(inputElement, occupationData) {
        if (!inputElement) return;
        
        // Fill occupation name with code for display
        const displayText = `${occupationData.anzsco_code} - ${occupationData.occupation_title}`;
        inputElement.val(displayText);
        inputElement.addClass('from-database');
        inputElement.data('anzsco-id', occupationData.id);
        
        // Fill hidden fields
        $('#eoi-anzsco-id').val(occupationData.id);
        $('#eoi-anzsco-code').val(occupationData.anzsco_code);
        
        console.log('[EOI-ROI] Occupation selected:', occupationData.occupation_title, occupationData.anzsco_code);
    }
    
    // Clear occupation selection
    function clearOccupationSelection() {
        $('#eoi-anzsco-id').val('');
        $('#eoi-anzsco-code').val('');
        $('#eoi-occupation').removeClass('from-database').removeData('anzsco-id');
    }

    // Export for external access if needed
    window.EoiRoi = {
        reload: function() {
            console.log('[EOI-ROI] Manual reload triggered');
            loadEoiRecords();
        },
        refreshPoints: function() {
            console.log('[EOI-ROI] Manual refresh points triggered');
            loadPoints();
        },
        init: initEoiRoi
    };

})();

