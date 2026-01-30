/**
 * Activity Feed Functionality
 * Handles filtering, width toggle, and activity feed interactions
 */

(function($) {
    'use strict';

    /**
     * Initialize Activity Feed functionality
     */
    function init() {
        setupFilterButtons();
        setupWidthToggle();
    }

    /**
     * Setup activity filter buttons
     */
    function setupFilterButtons() {
        $('.activity-filter-btn').on('click', function() {
            // Remove active class from all buttons
            $('.activity-filter-btn').removeClass('active');
            
            // Add active class to clicked button
            $(this).addClass('active');
            
            // Get filter type
            var filterType = $(this).data('filter');
            
            // Show/hide activities based on filter
            filterActivities(filterType);
        });
    }

    /**
     * Filter activities based on type
     * @param {string} filterType - The type of filter to apply (all, activity, note, document, accounting)
     */
    function filterActivities(filterType) {
        if (filterType === 'all') {
            $('.feed-item.activity').show();
        } else if (filterType === 'activity') {
            // Show both activity-type-activity AND activity-type-sms (merged)
            $('.feed-item.activity').hide();
            $('.feed-item.activity-type-activity, .feed-item.activity-type-sms').show();
        } else if (filterType === 'note') {
            // Show only actual notes (exclude activity edits, SMS, documents, and accounting)
            $('.feed-item.activity').each(function() {
                var $item = $(this);
                // Hide Activity edits, SMS, document, and accounting activities, show everything else (notes)
                if (!$item.hasClass('activity-type-sms') && 
                    !$item.hasClass('activity-type-activity') &&
                    !$item.hasClass('activity-type-document') && 
                    !$item.hasClass('activity-type-financial')) {
                    $item.show();
                } else {
                    $item.hide();
                }
            });
        } else if (filterType === 'document') {
            $('.feed-item.activity').hide();
            // Show document activities - check both class and subject text
            $('.feed-item.activity').each(function() {
                var $item = $(this);
                var hasDocumentClass = $item.hasClass('activity-type-document');
                
                // If it has the document class, show it
                if (hasDocumentClass) {
                    $item.show();
                    return;
                }
                
                // Fallback: Check subject text for document-related keywords
                // This handles legacy activities that don't have activity_type set
                var subject = $item.find('.feed-content strong').text().toLowerCase();
                var subjectText = subject || '';
                
                // Document-related patterns to match (but exclude accounting-related receipt documents)
                var documentPatterns = [
                    'document',
                    'added.*document',
                    'updated.*document',
                    'deleted.*document',
                    'renamed.*document',
                    'added.*migration document',
                    'updated.*migration document',
                    'added.*personal document',
                    'updated.*personal document',
                    'added.*visa document',
                    'updated.*visa document',
                    'added.*personal checklist',
                    'added.*visa checklist',
                    'updated.*checklist',
                    'signed document',
                    'signed cost agreement',
                    'document.*attached',
                    'document.*detached'
                ];
                
                // Check if subject matches any document pattern (but not accounting receipt documents)
                var isAccountingReceiptDoc = /(receipt document|journal receipt document|client receipt document|office receipt document)/i.test(subjectText);
                var isDocument = !isAccountingReceiptDoc && documentPatterns.some(function(pattern) {
                    var regex = new RegExp(pattern, 'i');
                    return regex.test(subjectText);
                });
                
                if (isDocument) {
                    $item.show();
                }
            });
        } else if (filterType === 'accounting') {
            $('.feed-item.activity').hide();
            // Show accounting activities - check both class and subject text
            $('.feed-item.activity').each(function() {
                var $item = $(this);
                var hasFinancialClass = $item.hasClass('activity-type-financial');
                
                // If it has the financial class, show it
                if (hasFinancialClass) {
                    $item.show();
                    return;
                }
                
                // Fallback: Check subject text for accounting-related keywords
                // This handles legacy activities that don't have activity_type set
                var subject = $item.find('.feed-content strong').text().toLowerCase();
                var subjectText = subject || '';
                
                // Accounting-related patterns to match
                var accountingPatterns = [
                    'invoice',
                    'added invoice',
                    'updated invoice',
                    'deleted invoice',
                    'receipt',
                    'office receipt',
                    'client receipt',
                    'journal receipt',
                    'receipt document',
                    'journal receipt document',
                    'client receipt document',
                    'office receipt document',
                    'added.*receipt',
                    'updated.*receipt',
                    'ledger',
                    'client funds ledger',
                    'fee transfer',
                    'allocation',
                    'allocated',
                    'payment',
                    'deposit',
                    'withdrawal',
                    'balance',
                    'cost agreement',
                    'account'
                ];
                
                // Check if subject matches any accounting pattern
                var isAccounting = accountingPatterns.some(function(pattern) {
                    var regex = new RegExp(pattern, 'i');
                    return regex.test(subjectText);
                });
                
                if (isAccounting) {
                    $item.show();
                }
            });
        } else {
            // Show only activities with specific type (for other filters like document, accounting)
            $('.feed-item.activity').hide();
            $('.feed-item.activity-type-' + filterType).show();
        }
    }

    /**
     * Setup width toggle checkbox
     */
    function setupWidthToggle() {
        $('#increase-activity-feed-width').on('change', function() {
            if ($(this).is(':checked')) {
                $('.activity-feed').addClass('wide-mode');
                $('.main-content').addClass('compact-mode');
            } else {
                $('.activity-feed').removeClass('wide-mode');
                $('.main-content').removeClass('compact-mode');
            }
            
            // Adjust Activity Feed height after layout change
            if (typeof adjustActivityFeedHeight === 'function') {
                adjustActivityFeedHeight();
                
                // Force a small delay to ensure CSS transitions complete
                setTimeout(function() {
                    adjustActivityFeedHeight();
                }, 150);
            }
        });
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        init();
    });

    // Expose public API
    window.ActivityFeed = {
        init: init,
        filterActivities: filterActivities
    };

})(jQuery);

