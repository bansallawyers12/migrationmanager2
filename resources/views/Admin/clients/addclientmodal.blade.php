<!-- All Application-Related Modals moved to resources/views/Admin/clients/modals/applications.blade.php -->
@include('Admin.clients.modals.applications')

<!-- Appointment Modal moved to resources/views/Admin/clients/modals/appointment.blade.php -->

<!-- All Note-Related Modals moved to resources/views/Admin/clients/modals/notes.blade.php -->
@include('Admin.clients.modals.notes')

<!-- All Task-Related Modals moved to resources/views/Admin/clients/modals/tasks.blade.php -->
@include('Admin.clients.modals.tasks')

<!-- Education Modal moved to resources/views/Admin/clients/modals/education.blade.php -->
@include('Admin.clients.modals.education')

<!-- Financial & Invoicing Modals moved to resources/views/Admin/clients/modals/financial.blade.php -->
@include('Admin.clients.modals.financial')

<!-- Checklist Modals moved to resources/views/Admin/clients/modals/checklists.blade.php -->
@include('Admin.clients.modals.checklists')

<!-- Email & Mail Modals moved to resources/views/Admin/clients/modals/emails.blade.php -->
@include('Admin.clients.modals.emails')

<!-- Document & File Upload Modals moved to resources/views/Admin/clients/modals/documents.blade.php -->
@include('Admin.clients.modals.documents')

<!-- Forms & Agreements Modals moved to resources/views/Admin/clients/modals/forms.blade.php -->
@include('Admin.clients.modals.forms')

<!-- Client & Lead Management Modals moved to resources/views/Admin/clients/modals/client-management.blade.php -->
@include('Admin.clients.modals.client-management')

<!-- Activities & Appointments Modals moved to resources/views/Admin/clients/modals/activities.blade.php -->
@include('Admin.clients.modals.activities')

<!-- Financial modals (Commission Invoice, General Invoice, Payment Details) removed - now in modals/financial.blade.php -->

<!-- Create Application Note Modal moved to resources/views/Admin/clients/modals/notes.blade.php -->

<!-- Appointment Modal removed - now in modals/activities.blade.php -->

<!-- Checklist modals (create_checklist, openeducationdocsmodal, openmigrationdocsmodal) removed - now in modals/checklists.blade.php -->

<!-- All Payment Schedule Modals moved to resources/views/Admin/clients/modals/payment-schedules.blade.php -->
@include('Admin.clients.modals.payment-schedules')

<!-- Email modals (applicationemailmodal, uploadmail, uploadAndFetchMailModel, uploadSentAndFetchMailModel) removed - now in modals/emails.blade.php -->

<!-- Upload Document Modal removed - now in modals/documents.blade.php -->

<!-- Personal & Visa checklist modals removed - now in modals/checklists.blade.php -->

<!-- All Receipt-Related Modals moved to resources/views/Admin/clients/modals/receipts.blade.php -->
@include('Admin.clients.modals.receipts')

<!-- Convert Lead to Client Modal removed - now in modals/client-management.blade.php -->

<!-- Upload inbox/sent email modals removed - now in modals/emails.blade.php -->

<!-- Assign User Modal (create_action_popup), styles, and popuploader removed - now in modals/client-management.blade.php -->

<!-- Financial Modals (Edit Ledger Entry, Cost Assignments) removed - now in modals/financial.blade.php -->
<!-- Change Matter Assignee Modal removed - now in modals/client-management.blade.php -->

<!-- Document Category Modals (Add Personal Doc Category, Add Visa Doc Category) removed - now in modals/documents.blade.php -->

<!-- Activity Modals (edit_datetime_modal, notPickedCallModal, convertActivityToNoteModal) removed - now in modals/activities.blade.php -->

<script>
$(document).ready(function() {
    // Initialize the multi-select dropdown functionality
    initializeMultiSelectDropdown();

    // Handle checkbox changes
    $('.checkbox-item').on('change', function() {
        updateSelectedUsers();
        updateHiddenSelect();
    });

    // Handle search functionality
    $('#user-search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        filterUsers(searchTerm);
    });

    // Handle select all button
    $('#select-all-users').on('click', function() {
        $('.checkbox-item:visible').prop('checked', true).trigger('change');
    });

    // Handle select none button
    $('#select-none-users').on('click', function() {
        $('.checkbox-item:visible').prop('checked', false).trigger('change');
    });

    // Prevent dropdown from closing when clicking inside
    $('.dropdown-menu').on('click', function(e) {
        e.stopPropagation();
    });
});

function initializeMultiSelectDropdown() {
    // Update the button text initially
    updateSelectedUsers();
}

function updateSelectedUsers() {
    var selectedUsers = [];
    $('.checkbox-item:checked').each(function() {
        selectedUsers.push($(this).data('name'));
    });

    var buttonText = selectedUsers.length > 0 ?
        (selectedUsers.length === 1 ? selectedUsers[0] : selectedUsers.length + ' users selected') :
        'Assign User';

    $('#selected-users-text').text(buttonText);
}

function updateHiddenSelect() {
    var selectedValues = [];
    $('.checkbox-item:checked').each(function() {
        selectedValues.push($(this).val());
    });
    $('#rem_cat').val(selectedValues).trigger('change');
}

function filterUsers(searchTerm) {
    $('.user-item').each(function() {
        var userName = $(this).data('name');
        if (userName.includes(searchTerm)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });

    // Update select all/none buttons to only affect visible items
    var visibleCheckboxes = $('.checkbox-item:visible');
    var checkedVisibleCheckboxes = $('.checkbox-item:visible:checked');

    if (checkedVisibleCheckboxes.length === visibleCheckboxes.length && visibleCheckboxes.length > 0) {
        $('#select-all-users').text('Select None').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
    } else {
        $('#select-all-users').text('Select All').removeClass('btn-outline-secondary').addClass('btn-outline-primary');
    }
}

// Enhanced select all/none functionality
$('#select-all-users').on('click', function() {
    var $button = $(this);
    if ($button.text() === 'Select All') {
        $('.checkbox-item:visible').prop('checked', true).trigger('change');
        $button.text('Select None').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
    } else {
        $('.checkbox-item:visible').prop('checked', false).trigger('change');
        $button.text('Select All').removeClass('btn-outline-secondary').addClass('btn-outline-primary');
    }
});

// Clear search when dropdown is closed
$('#dropdownMenuButton').on('click', function() {
    setTimeout(function() {
        $('#user-search').val('');
        $('.user-item').show();
        $('#select-all-users').text('Select All').removeClass('btn-outline-secondary').addClass('btn-outline-primary');
    }, 100);
});

// Clear validation errors when modal is opened
$('#create_action_popup').on('show.bs.modal', function() {
    $('.custom-error').remove();
    $('.popuploader').hide();
});

// Clear validation errors when modal is closed
$('#create_action_popup').on('hidden.bs.modal', function() {
    $('.custom-error').remove();
    $('.popuploader').hide();
    // Reset form fields
    $('#assignnote').val('');
    $('#task_group').val('');
    $('.checkbox-item').prop('checked', false);
    updateSelectedUsers();
    updateHiddenSelect();
});

//Start Convert Activity to Note functionality
document.querySelectorAll('.convert-activity-to-note').forEach(function(icon) {
    icon.addEventListener('click', function() {
        const activityId = this.getAttribute('data-activity-id');
        const activitySubject = this.getAttribute('data-activity-subject');
        const activityDescription = this.getAttribute('data-activity-description');
        const activityCreatedBy = this.getAttribute('data-activity-created-by');
        const activityCreatedAt = this.getAttribute('data-activity-created-at');
        const clientId = this.getAttribute('data-client-id');
        
        // Show confirmation dialog
        if (confirm('Are you sure you want to convert this activity into Notes?')) {
            // Populate modal fields
            document.getElementById('convert_activity_id').value = activityId;
            document.getElementById('convert_client_id').value = clientId;
            
            // Process description: remove HTML tags and first span
            let cleanDescription = processDescription(activityDescription);
            
            // Set value for Summernote editor
            $('#convert_note_description').summernote('code', cleanDescription);
            
            // Set Type dropdown based on activity subject and description
            setNoteType(activitySubject, activityDescription);
            
            // Populate client matters dropdown
            populateClientMatters(clientId);
            
            // Show modal
            $('#convertActivityToNoteModal').modal('show');
        }
    });
});

// Function to process description: remove HTML tags and first span
function processDescription(description) {
    // Create a temporary div to parse HTML
    let tempDiv = document.createElement('div');
    tempDiv.innerHTML = description;
    
    // Remove the first span if it exists
    let firstSpan = tempDiv.querySelector('span');
    if (firstSpan) {
        firstSpan.remove();
    }
    
    // Get text content (removes all HTML tags)
    let cleanText = tempDiv.textContent || tempDiv.innerText || '';
    
    // Clean up extra whitespace
    cleanText = cleanText.replace(/\s+/g, ' ').trim();
    
    return cleanText;
}

// Function to set note type based on activity subject and description
function setNoteType(subject, description = '') {
    const typeSelect = document.getElementById('convert_note_type'); 
    
    // Reset to default
    typeSelect.value = '';
    
    // First check if description contains type information (like "Email", "Call", etc.)
    if (description) {
        const descText = description.toLowerCase();
        if (descText.includes('email')) {
            typeSelect.value = 'Email';
            return;
        } else if (descText.includes('call')) {
            typeSelect.value = 'Call';
            return;
        } else if (descText.includes('person') || descText.includes('in-person')) {
            typeSelect.value = 'In-Person';
            return;
        } else if (descText.includes('attention')) {
            typeSelect.value = 'Attention';
            return;
        }
    }
    
    // If no type found in description, check subject content
    if (subject.includes('call') || subject.includes('Call')) {
        typeSelect.value = 'Call';
    } else if (subject.includes('email') || subject.includes('Email')) {
        typeSelect.value = 'Email';
    } else if (subject.includes('person') || subject.includes('Person')) {
        typeSelect.value = 'In-Person';
    } else if (subject.includes('attention') || subject.includes('Attention')) {
        typeSelect.value = 'Attention';
    } else {
        typeSelect.value = 'Others';
    }
}

// Function to populate client matters dropdown
function populateClientMatters(clientId) {
    // Clear existing options
    const select = document.getElementById('convert_client_matter_id');
    select.innerHTML = '<option value="">Select Client Matter</option>';
    
    // Fetch client matters via AJAX
    fetch(`/admin/get-client-matters/${clientId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.matters) {
                data.matters.forEach(matter => {
                    const option = document.createElement('option');
                    option.value = matter.id;
                    
                    // Check if it's a general matter (starts with GN_)
                    if (matter.client_unique_matter_no && 
                        matter.client_unique_matter_no.startsWith('GN_')) {
                        option.textContent = 'General Matter - ' + matter.client_unique_matter_no;
                    } else {
                        option.textContent = matter.display_name;
                    }
                    
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching client matters:', error);
        });
}

// Handle form submission
document.getElementById('convertActivityToNoteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate required fields
    const clientMatterId = document.getElementById('convert_client_matter_id').value;
    const noteType = document.getElementById('convert_note_type').value;
    
    if (!clientMatterId) {
        alert('Please select a Client Matter');
        return;
    }
    
    if (!noteType) {
        alert('Please select a Type');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('/admin/convert-activity-to-note', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Activity successfully converted to note!');
            $('#convertActivityToNoteModal').modal('hide');
            // Optionally refresh the page or update the UI
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to convert activity to note'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while converting activity to note');
    });
});
//End Convert Activity to Note functionality
</script>