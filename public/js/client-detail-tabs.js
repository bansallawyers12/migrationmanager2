/**
 * Client Detail Page - Tab Management JavaScript
 * Handles tab switching, URL updates, matter switching, and document filtering
 */

(function($) {
    'use strict';

    // Global variables
    let selectedMatter = '';
    let clientId = '';
    let matterId = '';

    /**
     * Initialize client detail page
     */
    function initClientDetailPage(config) {
        clientId = config.clientId;
        matterId = config.matterId;
        selectedMatter = config.selectedMatter || '';

        // Initialize event handlers
        initTabSwitching();
        initMatterSwitching();
        initBrowserNavigation();
        initPageLoad(config.activeTab);
        
        // Hide grid data by default
        $('.grid_data').hide();
    }

    /**
     * Function to update URL without reloading page
     */
    function updateTabUrl(tabId) {
        let newUrl = '/admin/clients/detail/' + clientId;
        if (matterId && matterId !== '') {
            newUrl += '/' + matterId;
        }
        newUrl += '/' + tabId;
        window.history.pushState({tab: tabId}, '', newUrl);
    }

    /**
     * Function to get current matter reference number from dropdown or checkbox
     */
    function getCurrentMatterRefNo() {
        if ($('.general_matter_checkbox_client_detail:checked').length) {
            return $('.general_matter_checkbox_client_detail:checked').data('clientuniquematterno');
        } else {
            return $('#sel_matter_id_client_detail option:selected').data('clientuniquematterno');
        }
    }

    /**
     * Initialize tab switching functionality
     */
    function initTabSwitching() {
        // Handle main tab switching (both horizontal and vertical tabs)
        $(document).on('click', '.tab-button, .vertical-tab-button, .client-nav-button', function() {
            // Remove active class from all buttons and panes
            $('.tab-button, .vertical-tab-button, .client-nav-button').removeClass('active');
            $('.tab-pane').removeClass('active');

            // Add active class to clicked button
            $(this).addClass('active');

            // Show corresponding pane
            const tabId = $(this).data('tab');
            $(`#${tabId}-tab`).addClass('active');
            
            // Update URL
            updateTabUrl(tabId);

            // Show/hide Activity Feed based on tab
            if (tabId === 'personaldetails') {
                $('#activity-feed').show();
                $('#main-content').css('flex', '1');
            } else {
                handleMatterSpecificTab(tabId);
                $('#activity-feed').hide();
            }

            // Store the active tab in localStorage when a tab is clicked
            localStorage.setItem('activeTab', tabId);
        });
    }

    /**
     * Handle matter-specific tab content filtering
     */
    function handleMatterSpecificTab(tabId) {
        // Get selected matter
        if ($('.general_matter_checkbox_client_detail').is(':checked')) {
            selectedMatter = $('.general_matter_checkbox_client_detail').val();
        } else {
            selectedMatter = $('#sel_matter_id_client_detail').val();
        }

        // Get active subtab
        const activeSubTab = $('.subtab-button.active').data('subtab');

        // Filter Notes by matter
        if (tabId === 'noteterm') {
            filterNotesByMatter(selectedMatter);
        }

        // Filter Visa Documents by matter
        if (tabId === 'visadocuments') {
            filterVisaDocumentsByMatter(selectedMatter);
        }

        // Filter Emails by matter
        if (tabId === 'conversations' && activeSubTab === 'inbox') {
            filterEmailsByMatter(selectedMatter, 'inbox');
        }

        if (tabId === 'conversations' && activeSubTab === 'sent') {
            filterEmailsByMatter(selectedMatter, 'sent');
        }

        // Handle Client Portal tab click specifically
        if (tabId === 'application') {
            showClientMatterApplicationData(selectedMatter);
        }
    }

    /**
     * Filter notes by matter
     */
    function filterNotesByMatter(matterId) {
        if (matterId !== "") {
            $('#noteterm-tab').find('.note-card-redesign').each(function() {
                if ($(this).data('matterid') == matterId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $('#noteterm-tab').find('.note-card-redesign').each(function() {
                if ($(this).data('matterid') == '') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    }

    /**
     * Filter visa documents by matter
     */
    function filterVisaDocumentsByMatter(matterId) {
        if (matterId !== "") {
            $('#visadocuments-tab .migdocumnetlist1').find('.drow').each(function() {
                if ($(this).data('matterid') == matterId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $('#visadocuments-tab .migdocumnetlist1').find('.drow').hide();
        }
    }

    /**
     * Filter emails by matter
     */
    function filterEmailsByMatter(matterId, folder) {
        const selector = folder === 'inbox' ? '#inbox-subtab #email-list' : '#sent-subtab #email-list1';
        
        if (matterId !== "") {
            $(selector).find('.email-card').each(function() {
                if ($(this).data('matterid') == matterId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $(selector).find('.email-card').hide();
        }
    }

    /**
     * Initialize matter switching functionality
     * NOTE: Matter selection change handlers are in detail-main.js to avoid duplicate bindings
     * This function is kept for potential future use but currently does nothing
     */
    function initMatterSwitching() {
        // Matter dropdown and checkbox change handlers are handled in detail-main.js
        // This prevents duplicate event bindings that cause infinite refresh loops
        // Checkbox exclusivity logic is also in detail-main.js
    }

    /**
     * Handle browser back/forward buttons
     */
    function initBrowserNavigation() {
        window.addEventListener('popstate', function(event) {
            if (event.state && event.state.tab) {
                const tabId = event.state.tab;
                const $targetButton = $(`.tab-button[data-tab="${tabId}"], .vertical-tab-button[data-tab="${tabId}"], .client-nav-button[data-tab="${tabId}"]`);
                if ($targetButton.length) {
                    // Manually trigger tab change without updating URL again
                    $('.tab-button, .vertical-tab-button, .client-nav-button').removeClass('active');
                    $('.tab-pane').removeClass('active');
                    $targetButton.addClass('active');
                    $(`#${tabId}-tab`).addClass('active');
                    
                    // Handle matter-specific content
                    if (tabId !== 'personaldetails') {
                        handleMatterSpecificTab(tabId);
                    }
                }
            }
        });
    }

    /**
     * Initialize page load - activate tab from URL
     */
    function initPageLoad(activeTabFromUrl) {
        $(document).ready(function() {
            if (activeTabFromUrl && activeTabFromUrl !== 'personaldetails') {
                // Find and click the button for the tab from URL
                const $targetButton = $(`.tab-button[data-tab="${activeTabFromUrl}"], .vertical-tab-button[data-tab="${activeTabFromUrl}"], .client-nav-button[data-tab="${activeTabFromUrl}"]`);
                if ($targetButton.length) {
                    $targetButton.click();
                }
            }
        });
    }

    /**
     * Handle Client Portal tab - Load application data
     */
    function showClientMatterApplicationData(selectedMatter) {
        // Show loading message in the application tab
        $('#application-tab').html('<h4>Please wait, upserting application record...</h4>');
        
        // Step 1: Insert/Update record in applications table
        $.ajax({
            url: '/admin/load-application-insert-update-data',
            type: 'POST',
            data: {
                client_id: clientId,
                client_matter_id: selectedMatter
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(upsertResponse) {
                // Handle success - load application content
                console.log('Application data loaded successfully');
            },
            error: function(xhr, status, error) {
                $('#application-tab').html('<h4 class="text-danger">Error loading application data. Please try again.</h4>');
                console.error('Error:', error);
            }
        });
    }

    /**
     * Initialize matter selection from URL on page load
     * NOTE: This function is deprecated - matter initialization is now handled in detail-main.js
     * Kept for backward compatibility but should not be called
     */
    function initMatterFromUrl(matterIdInUrl) {
        // Matter initialization is now handled in detail-main.js with proper initialization guard
        // to prevent infinite refresh loops
        console.log('initMatterFromUrl is deprecated - handled by detail-main.js');
    }

    // Expose initialization function to global scope
    window.ClientDetailTabs = {
        init: initClientDetailPage,
        initMatterFromUrl: initMatterFromUrl,
        filterNotesByMatter: filterNotesByMatter,
        filterVisaDocumentsByMatter: filterVisaDocumentsByMatter,
        filterEmailsByMatter: filterEmailsByMatter
    };

    // Auto-initialize when document is ready
    $(document).ready(function() {
        if (window.ClientDetailConfig) {
            initClientDetailPage(window.ClientDetailConfig);
        }
    });

})(jQuery);

