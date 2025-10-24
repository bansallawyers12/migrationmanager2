/**
 * Dropdown Multi-Select Component
 * 
 * A reusable component for custom multi-select dropdowns with checkbox functionality.
 * Used in action pages for assignee selection.
 * 
 * Usage:
 * 1. Include this script after jQuery
 * 2. Add HTML structure with classes: .dropdown-multi-select, .dropdown-toggle, .checkbox-item
 * 3. The component will automatically initialize
 */

(function($) {
    'use strict';

    // Initialize on document ready
    $(document).ready(function() {
        initDropdownMultiSelect();
    });

    /**
     * Initialize dropdown multi-select functionality
     */
    function initDropdownMultiSelect() {
        // Toggle dropdown menu on button click
        $(document).on('click', '.dropdown-toggle', function(e) {
            e.stopPropagation();
            var $dropdown = $(this).parent('.dropdown-multi-select');
            
            // Close other dropdowns
            $('.dropdown-multi-select').not($dropdown).removeClass('show');
            
            // Toggle this dropdown
            $dropdown.toggleClass('show');
        });

        // Close the dropdown if clicked outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown-multi-select').length) {
                $('.dropdown-multi-select').removeClass('show');
            }
        });

        // Prevent dropdown from closing when clicking inside
        $(document).on('click', '.dropdown-multi-select .dropdown-menu', function(e) {
            e.stopPropagation();
        });

        // Handle checkbox click events
        $(document).on('change', '.checkbox-item', function() {
            updateSelectedValues();
        });

        // Handle "Select All" functionality
        $(document).on('change', '#select-all', function() {
            var isChecked = $(this).is(':checked');
            $('.checkbox-item').prop('checked', isChecked).trigger('change');
        });
    }

    /**
     * Update the hidden select input with selected values
     */
    function updateSelectedValues() {
        var selectedValues = [];
        
        // Collect selected checkboxes values
        $('.checkbox-item:checked').each(function() {
            selectedValues.push($(this).val());
        });
        
        // Set the selected values in the hidden select dropdown
        $('#rem_cat').val(selectedValues).trigger('change');
        
        // Update the display text
        updateDisplayText(selectedValues.length);
    }

    /**
     * Update the dropdown button text to show count
     * @param {number} count - Number of selected items
     */
    function updateDisplayText(count) {
        var $button = $('.dropdown-toggle');
        var baseText = $button.data('base-text') || 'Select Assignees';
        
        if (count > 0) {
            $button.find('.selected-count').text(' (' + count + ' selected)');
        } else {
            $button.find('.selected-count').text('');
        }
    }

    // Expose functions globally if needed
    window.DropdownMultiSelect = {
        init: initDropdownMultiSelect,
        updateValues: updateSelectedValues
    };

})(jQuery);

