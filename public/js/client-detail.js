/**
 * Client Detail Page JavaScript
 * Extracted from detail.blade.php
 */

$(document).ready(function() {
    // Activity Feed Height Adjustment
    function adjustActivityFeedHeight() {
        let mainContentHeight = $('.main-content').outerHeight();
        let activityFeedHeight = $('.activity-feed').outerHeight();
        let windowHeight = $(window).height();

        if (mainContentHeight <= activityFeedHeight) { 
            // Case 1: main-content smaller → activity-feed should use viewport height
            $('.activity-feed').css('max-height', `calc(100vh - 120px)`);
        } else { 
            // Case 2: main-content bigger → activity-feed should match main-content height
            $('.activity-feed').css('max-height', mainContentHeight + 'px');
        }
    }

    // Run on load
    adjustActivityFeedHeight();

    // Run on resize (for responsiveness)
    $(window).on('resize', function () {
        adjustActivityFeedHeight();
    });

    // Update Personal Document Category
    $('.update-personal-cat-title').on('click', function() {
        var id = $(this).data('id');
        var newTitle = prompt('Enter new title for the category:');
        if (newTitle) {
            $.ajax({
                url: '/admin/update-personal-doc-category',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                    title: newTitle
                },
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    });

    // Update Visa Document Category
    $('.update-visa-cat-title').on('click', function() {
        var id = $(this).data('id');
        var newTitle = prompt('Enter new title for the category:');
        if (newTitle) {
            $.ajax({
                url: '/admin/update-visa-doc-category',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                    title: newTitle
                },
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    });

    // File download function
    function downloadFile(url, fileName) {
        // Create a temporary anchor element
        const link = document.createElement('a');
        link.href = url;
        link.download = fileName; // Set the desired file name
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // File preview function
    function previewFile(fileType, fileUrl, containerId) {
        const container = $(`.${containerId}`);
        
        // Clear previous content
        container.empty();
        
        if (fileType === 'pdf') {
            container.html(`<iframe src="${fileUrl}" width="100%" height="500px" style="border: none;"></iframe>`);
        } else if (fileType === 'image') {
            container.html(`<img src="${fileUrl}" alt="Preview" style="max-width: 100%; height: auto;">`);
        } else {
            container.html(`<p>Preview not available for this file type.</p>`);
        }
    }

    // Client Portal Toggle Functionality
    $('#client-portal-toggle').on('change', function() {
        var isChecked = $(this).is(':checked');
        var clientId = $(this).data('client-id');
        var toggleElement = $(this);

        // Disable toggle during request
        toggleElement.prop('disabled', true);

        $.ajax({
            url: window.clientPortalToggleUrl || '/admin/clients/toggle-portal',
            method: 'POST',
            data: {
                client_id: clientId,
                status: isChecked,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Success: ' + response.message);
                } else {
                    // Revert toggle state on error
                    toggleElement.prop('checked', !isChecked);
                    alert('Error: ' + (response.message || 'Error updating client portal status'));
                }
            },
            error: function(xhr, status, error) {
                // Revert toggle state on error
                toggleElement.prop('checked', !isChecked);
                alert('Error: ' + error);
            },
            complete: function() {
                // Re-enable toggle
                toggleElement.prop('disabled', false);
            }
        });
    });

    // Make functions globally available
    window.downloadFile = downloadFile;
    window.previewFile = previewFile;
});
