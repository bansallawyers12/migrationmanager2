// Dashboard Optimized JavaScript
$(document).ready(function() {
    initializeDashboard();
    applyInitialColumnVisibility();
    initializeEventHandlers();
});

function initializeDashboard() {
    console.log('Dashboard initialized');
}

function initializeEventHandlers() {
    // Stage change handler
    $(document).on('change', '.stageCls', function () {
        let stageId = $(this).val();
        let itemId = $(this).attr('id').split('_')[1];
        if (stageId) {
            updateStage(itemId, stageId);
        }
    });

    // Extend deadline
    $(document).on('click', '#extend_deadline', extendDeadline);
    $(document).delegate('.btn-extend_note_deadline', 'click', openExtendDeadlineModal);
    
    // Filter form
    $('#filterForm').on('submit', submitFilterForm);
    $('.stage-select').on('change', function() {
        $('#filterForm').submit();
    });

    // Column toggle
    $('#columnToggleBtn').on('click', toggleColumnDropdown);
    $('input[name="column"]').on('change', toggleColumn);
    $('#toggleAllColumns').on('change', toggleAllColumns);

    // Search and actions
    $('input[name="client_name"]').on('blur keypress', handleSearchInput);
    $(document).on('click', '.action-button', handleActionClick);
}

function updateStage(itemId, stageId) {
    $.ajax({
        url: window.dashboardRoutes.updateStage,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        method: 'POST',
        data: { item_id: itemId, stage_id: stageId },
        success: function (response) {
            showNotification(response.success ? 'Stage updated successfully!' : 'Failed to update stage.', response.success ? 'success' : 'error');
            if (response.success) setTimeout(() => location.reload(), 1000);
        },
        error: function (xhr, status, error) {
            showNotification('An error occurred while updating status.', 'error');
        }
    });
}

function extendDeadline() {
    $(".popuploader").show();
    let flag = true;
    $(".custom-error").remove();

    if ($('#assignnote').val() === '') {
        $('#assignnote').after("<span class='custom-error'>Note field is required.</span>");
        flag = false;
    }
    
    if ($('#note_deadline').val() === '') {
        $('#note_deadline').after("<span class='custom-error'>Note Deadline is required.</span>");
        flag = false;
    }

    if (flag) {
        $.ajax({
            type: 'POST',
            url: window.dashboardRoutes.extendDeadline,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: {
                note_id: $('#note_id').val(),
                unique_group_id: $('#unique_group_id').val(),
                description: $('#assignnote').val(),
                note_deadline: $('#note_deadline').val()
            },
            success: function(response) {
                $('.popuploader').hide();
                $('#extend_note_popup').modal('hide');
                showNotification('Deadline extended successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                $('.popuploader').hide();
                showNotification('Failed to extend deadline.', 'error');
            }
        });
    } else {
        $('.popuploader').hide();
    }
}

function openExtendDeadlineModal($button) {
    $('#note_id').val($button.attr("data-noteid"));
    $('#unique_group_id').val($button.attr("data-uniquegroupid"));
    $('#assignnote').val($button.attr("data-assignnote"));
    $('#note_deadline').val($button.attr("data-deadlinedate"));
    $('#extend_note_popup').modal('show');
}

function submitFilterForm(e) {
    e.preventDefault();
    const formData = $('#filterForm').serialize();
    const url = new URL(window.location.href);
    const params = new URLSearchParams(formData);
    params.delete('page');
    url.search = params.toString();
    window.location.href = url.toString();
}

function toggleColumnDropdown() {
    $('#columnDropdown').toggleClass('show');
}

function toggleColumn($checkbox) {
    const columnValue = $checkbox.val();
    const isChecked = $checkbox.is(':checked');
    
    if (isChecked) {
        $('.col-' + columnValue).removeClass('col-hidden');
    } else {
        $('.col-' + columnValue).addClass('col-hidden');
    }
    
    updateVisibleCount();
    updateToggleAllState();
    saveColumnPreferences();
}

function toggleAllColumns($checkbox) {
    const isChecked = $checkbox.is(':checked');
    $('input[name="column"]').prop('checked', isChecked);
    
    if (isChecked) {
        $('[class*="col-"]').removeClass('col-hidden');
    } else {
        $('[class*="col-"]').addClass('col-hidden');
    }
    
    updateVisibleCount();
    saveColumnPreferences();
}

function updateVisibleCount() {
    const visibleCount = $('input[name="column"]:checked').length;
    $('.visible-count').text(visibleCount);
}

function updateToggleAllState() {
    const totalColumns = $('input[name="column"]').length;
    const checkedColumns = $('input[name="column"]:checked').length;
    
    if (checkedColumns === 0) {
        $('#toggleAllColumns').prop('indeterminate', false).prop('checked', false);
    } else if (checkedColumns === totalColumns) {
        $('#toggleAllColumns').prop('indeterminate', false).prop('checked', true);
    } else {
        $('#toggleAllColumns').prop('indeterminate', true);
    }
}

function saveColumnPreferences() {
    const visibleColumns = [];
    $('input[name="column"]:checked').each(function() {
        visibleColumns.push($(this).val());
    });

    $.ajax({
        url: window.dashboardRoutes.columnPreferences,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { visible_columns: visibleColumns },
        success: function() {
            console.log('Column preferences saved');
        },
        error: function() {
            console.error('Failed to save column preferences');
        }
    });
}

function applyInitialColumnVisibility() {
    const visibleColumns = window.dashboardData.visibleColumns || [];
    const allColumns = ['matter', 'client_id', 'client_name', 'dob', 'migration_agent', 'person_responsible', 'person_assisting', 'stage', 'action'];
    
    allColumns.forEach(function(column) {
        if (!visibleColumns.includes(column)) {
            $('.col-' + column).addClass('col-hidden');
            $('input[name="column"][value="' + column + '"]').prop('checked', false);
        }
    });
    
    updateVisibleCount();
    updateToggleAllState();
}

function handleSearchInput(e) {
    if (e.type === 'blur' && $(this).val().trim() !== '') {
        $('#filterForm').submit();
    }
    if (e.which === 13) {
        e.preventDefault();
        $('#filterForm').submit();
    }
}

function handleActionClick() {
    window.location.href = $(this).data('href');
}

window.clearFiltersAndReset = function() {
    window.location.href = window.dashboardRoutes.dashboard;
}

window.closeNotesDeadlineAction = function(noteid, noteuniqueid) {
    if (confirm('Are you sure, you want to close this note deadline?')) {
        if (noteid == '' && noteuniqueid == '') {
            showNotification('Please select note to close the deadline.', 'error');
            return false;
        }
        
        $('.popuploader').show();
        $.ajax({
            type: 'post',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: window.dashboardRoutes.updateTaskCompleted,
            data: {'id': noteid, 'unique_group_id': noteuniqueid},
            success: function() {
                $('.popuploader').hide();
                showNotification('Task completed successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                $('.popuploader').hide();
                showNotification('Failed to complete task.', 'error');
            }
        });
    } else {
        $('.popuploader').hide();
    }
}

function showNotification(message, type = 'info') {
    // Simple notification - can be enhanced with toast library
    if (type === 'error') {
        alert('Error: ' + message);
    } else if (type === 'success') {
        alert('Success: ' + message);
    } else {
        alert(message);
    }
}
