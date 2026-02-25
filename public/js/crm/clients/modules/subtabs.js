/**
 * Subtabs module - Document/Notes/Form subtab switching (inbox, sent, migrationdocuments, notes, personal, visa, form generation)
 * Extracted from detail-main.js - Phase 3i refactoring.
 * Requires: jQuery, ClientDetailConfig (for getCostAssignmentMigrationAgentDetail in subtab3)
 */
(function($) {
    'use strict';
    if (!$) return;

    $(document).ready(function() {
        // Handle document subtab switching
        $('.subtab-button').click(function() {

        // Remove active class from all document subtab buttons and panes

        $('.subtab-button').removeClass('active');

        $('.subtab-pane').removeClass('active');



        // Add active class to clicked button

        $(this).addClass('active');



        // Show corresponding pane

        const subtabId = $(this).data('subtab');

        $(`#${subtabId}-subtab`).addClass('active');



        if ($('.general_matter_checkbox_client_detail').is(':checked')) {

            var selectedMatter = $('.general_matter_checkbox_client_detail').val();

        } else {

            var selectedMatter = $('#sel_matter_id_client_detail').val();

        }



       



        if( subtabId == 'inbox') {

            if(selectedMatter != "" ) {

                $('#inbox-subtab #email-list').find('.email-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }



        if( subtabId == 'sent') {

            if(selectedMatter != "" ) {

                $('#sent-subtab #email-list1').find('.email-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }



        //alert(subtabId);

        if( subtabId == 'migrationdocuments') {

            //var selectedMatter = $('#sel_matter_id_client_detail').val();

            //console.log('selectedMatter&&&&==='+selectedMatter);

            if(selectedMatter != "" ) {

                $('#migrationdocuments-subtab .migdocumnetlist1').find('.drow').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }







        // Store the active tab in localStorage when a tab is clicked

        localStorage.setItem('subactiveTab', subtabId);

    });



    // On page load, check localStorage and activate the correct tab

    const subactiveTab = localStorage.getItem('subactiveTab');

    if (subactiveTab) {

        // Find the button corresponding to the stored tabId and trigger its click event

        const $subtargetButton = $(`.subtab-button[data-tab="${subactiveTab}"]`);

        if ($subtargetButton.length) {

            $subtargetButton.click(); // Trigger the click event to reuse the existing logic

        }

        // Clear localStorage to prevent persistence on future loads (optional)

        localStorage.removeItem('subactiveTab');

    }



    //Notes subtab click

    $('.subtab8-button').click(function(e) {

        e.preventDefault();



        // Remove active class from all buttons and panes

        $('.subtab8-button').removeClass('active');

        $('.subtab8-pane').removeClass('active');



        // Add active class to clicked button

        $(this).addClass('active');



        // Show corresponding pane

        const subtabId8 = $(this).data('subtab8'); //alert(subtabId8);

        $(`#${subtabId8}-subtab8`).addClass('active');

    });



    //Document subtab like - Personal

    $('.subtab2-button').click(function(e) {

        e.preventDefault();



        // Remove active class from all buttons and panes

        $('.subtab2-button').removeClass('active');

        $('.subtab2-pane').removeClass('active');



        // Add active class to clicked button

        $(this).addClass('active');



        // Show corresponding pane

        const subtabId2 = $(this).data('subtab2'); //alert(subtabId2);

        $(`#${subtabId2}-subtab2`).addClass('active');

    });



    //Document subtab like - Visa

    $('.subtab6-button').click(function(e) {

        e.preventDefault();



        // Remove active class from all buttons and panes

        $('.subtab6-button').removeClass('active');

        $('.subtab6-pane').removeClass('active');



        // Add active class to clicked button

        $(this).addClass('active');



        // Show corresponding pane

        const subtabId6 = $(this).data('subtab6'); //alert(subtabId6);

        $(`#${subtabId6}-subtab6`).addClass('active');

    });





    // Handle form generation subtab switching

    $('.subtab3-button').click(function() {

        // Remove active class from all document subtab buttons and panes

        $('.subtab3-button').removeClass('active');

        $('.subtab3-pane').removeClass('active');



        // Add active class to clicked button

        $(this).addClass('active');



        // Show corresponding pane

        const subtabId = $(this).data('subtab');

        $(`#${subtabId}-subtab`).addClass('active');



        // If switching to Create Cost Assignment subtab, load agent details and initialize calculations

        if (subtabId === 'createcostform') {

            $('#cost_assignment_client_id').val(window.ClientDetailConfig.clientId);

            var hidden_client_matter_id_assignment = $('#sel_matter_id_client_detail').val();

            $('#cost_assignment_client_matter_id').val(hidden_client_matter_id_assignment);

            if (window.ClientDetailConfig.clientId && hidden_client_matter_id_assignment) {

                // Use a small delay to ensure function is available, then load agent details
                setTimeout(function() {
                    // Use window function if available
                    if (typeof window.getCostAssignmentMigrationAgentDetail === 'function') {
                        window.getCostAssignmentMigrationAgentDetail(window.ClientDetailConfig.clientId, hidden_client_matter_id_assignment);
                    } else {
                        // Fallback: try to call it directly (in case it's in the same scope)
                        if (typeof getCostAssignmentMigrationAgentDetail === 'function') {
                            getCostAssignmentMigrationAgentDetail(window.ClientDetailConfig.clientId, hidden_client_matter_id_assignment);
                        } else {
                            console.error('getCostAssignmentMigrationAgentDetail function not found. Please refresh the page.');
                        }
                    }
                }, 100);

            }

            // Note: initializeCostAssignmentCalculations() will be called after data loads
            // in getCostAssignmentMigrationAgentDetail() success callback

        }



        if ($('.general_matter_checkbox_client_detail').is(':checked')) {

            var selectedMatter = $('.general_matter_checkbox_client_detail').val();

        } else {

            var selectedMatter = $('#sel_matter_id_client_detail').val();

        }



		if( subtabId == 'form956') {

            if(selectedMatter != "" ) {

                $('#form956-subtab #form-list').find('.form-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }



        if( subtabId == 'clientagreement') {

            if(selectedMatter != "" ) {

                $('#clientagreement-subtab #form-list2').find('.form-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }

        if( subtabId == 'costform') {

            if(selectedMatter != "" ) {

                $('#costform-subtab #form-list1').find('.form-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }
		/*if( subtabId == 'visaagreementform') {

            if(selectedMatter != "" ) {

                $('#visaagreementform-subtab #form-list2').find('.form-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }*/





        if( subtabId == 'costformL') {

            if(selectedMatter != "" ) {

                $('#costformL-subtab #form-list1').find('.form-card').each(function() {

                    if ($(this).data('matterid') == selectedMatter) {

                        $(this).show();

                    } else {

                        $(this).hide();

                    }

                });

            }  else {

                $(this).hide();

            }

        }

    });

    }); // end $(document).ready

})(typeof jQuery !== 'undefined' ? jQuery : null);
