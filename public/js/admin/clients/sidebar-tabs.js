/**
 * Client Detail Page - Sidebar Tab Management
 * Dedicated file for handling sidebar navigation tabs
 * Handles tab switching, URL updates, and content visibility
 */

(function($) {
    'use strict';

    // Module state
    const SidebarTabs = {
        clientId: '',
        matterId: '',
        selectedMatter: '',
        initialized: false
    };

    /**
     * Initialize sidebar tabs
     * NOTE: This should be called from within $(document).ready() - don't wrap it again
     */
    function init(config) {
        if (SidebarTabs.initialized) {
            return;
        }

        SidebarTabs.clientId = config.clientId;
        SidebarTabs.matterId = config.matterId;
        SidebarTabs.selectedMatter = config.selectedMatter || '';
        
        // Setup event handlers immediately (caller ensures DOM is ready)
        setupTabClickHandlers();
        setupBrowserNavigation();
        activateInitialTab(config.activeTab);
        
        // Hide grid data by default
        $('.grid_data').hide();
        
        SidebarTabs.initialized = true;
    }

    /**
     * Setup tab click handlers
     */
    function setupTabClickHandlers() {
        // IMPORTANT: Attach handlers DIRECTLY to each button element
        // This ensures our handler runs BEFORE any delegated handlers that might stop propagation
        $('.client-nav-button').each(function() {
            const $button = $(this);
            const tabId = $button.data('tab');
            
            // Remove any existing handler on this specific button
            $button.off('click.sidebarTabs');
            
            // Attach handler directly with namespace
            $button.on('click.sidebarTabs', function(e) {
                // Stop event from propagating to other handlers
                e.preventDefault();
                e.stopImmediatePropagation();
                
                if (!tabId) {
                    console.error('[SidebarTabs] No tab ID found on button');
                    return false;
                }
                
                activateTab(tabId);
                return false;
            });
        });
    }

    /**
     * Activate a specific tab
     */
    function activateTab(tabId) {
        // Remove active class from all sidebar buttons and panes
        $('.client-nav-button').removeClass('active');
        $('.tab-pane').removeClass('active');
        
        // Add active class to the clicked button
        $(`.client-nav-button[data-tab="${tabId}"]`).addClass('active');
        
        // Show the corresponding tab pane
        const $tabPane = $(`#${tabId}-tab`);
        if ($tabPane.length) {
            $tabPane.addClass('active');
        } else {
            console.error('[SidebarTabs] Tab pane not found:', `#${tabId}-tab`);
        }
        
        // Update URL
        updateUrl(tabId);
        
        // Handle activity feed visibility
        if (tabId === 'personaldetails') {
            $('#activity-feed').show();
            $('#main-content').css('flex', '1');
        } else {
            handleMatterSpecificTab(tabId);
            $('#activity-feed').hide();
        }
        
        // Store active tab
        localStorage.setItem('activeTab', tabId);
    }

    /**
     * Update URL without reloading page
     */
    function updateUrl(tabId) {
        let newUrl = '/admin/clients/detail/' + SidebarTabs.clientId;
        if (SidebarTabs.matterId && SidebarTabs.matterId !== '') {
            newUrl += '/' + SidebarTabs.matterId;
        }
        newUrl += '/' + tabId;
        
        window.history.pushState({tab: tabId}, '', newUrl);
    }

    /**
     * Handle matter-specific tab content
     */
    function handleMatterSpecificTab(tabId) {
        // Get selected matter
        if ($('.general_matter_checkbox_client_detail').is(':checked')) {
            SidebarTabs.selectedMatter = $('.general_matter_checkbox_client_detail').val();
        } else {
            SidebarTabs.selectedMatter = $('#sel_matter_id_client_detail').val();
        }

        const activeSubTab = $('.subtab-button.active').data('subtab');

        // Filter content by matter
        switch(tabId) {
            case 'noteterm':
                filterNotesByMatter(SidebarTabs.selectedMatter);
                break;
            case 'visadocuments':
                filterVisaDocumentsByMatter(SidebarTabs.selectedMatter);
                break;
            case 'conversations':
                if (activeSubTab === 'inbox') {
                    filterEmailsByMatter(SidebarTabs.selectedMatter, 'inbox');
                } else if (activeSubTab === 'sent') {
                    filterEmailsByMatter(SidebarTabs.selectedMatter, 'sent');
                }
                break;
            case 'application':
                if (typeof showClientMatterApplicationData === 'function') {
                    showClientMatterApplicationData(SidebarTabs.selectedMatter);
                }
                break;
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
     * Setup browser navigation (back/forward buttons)
     */
    function setupBrowserNavigation() {
        window.addEventListener('popstate', function(event) {
            if (event.state && event.state.tab) {
                activateTab(event.state.tab);
            }
        });
    }

    /**
     * Activate initial tab from URL or default
     */
    function activateInitialTab(activeTabFromUrl) {
        const tabId = activeTabFromUrl || 'personaldetails';
        
        if (tabId !== 'personaldetails') {
            // Trigger click on the button for non-default tabs
            const $button = $(`.client-nav-button[data-tab="${tabId}"]`);
            if ($button.length) {
                $button.click();
            } else {
                // Try to find a close match (singular vs plural issue)
                const availableTabs = [];
                $('.client-nav-button').each(function() {
                    availableTabs.push($(this).data('tab'));
                });
                
                const closeTabs = availableTabs.filter(t => 
                    t.startsWith(tabId) || tabId.startsWith(t)
                );
                
                if (closeTabs.length > 0) {
                    $(`.client-nav-button[data-tab="${closeTabs[0]}"]`).click();
                }
            }
        }
    }

    // Expose public API
    window.SidebarTabs = {
        init: init,
        activateTab: activateTab,
        filterNotesByMatter: filterNotesByMatter,
        filterVisaDocumentsByMatter: filterVisaDocumentsByMatter,
        filterEmailsByMatter: filterEmailsByMatter
    };

})(jQuery);

