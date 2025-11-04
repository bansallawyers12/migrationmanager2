<!-- Enhanced Date Filter Scripts -->
// Initialize datepickers for custom date range
$('.datepicker').datepicker({
    format: 'dd/mm/yyyy',
    autoclose: true,
    todayHighlight: true
});

// Quick Filter Chips Functionality
$('.quick-filter-chip').on('click', function() {
    var filterType = $(this).data('filter');
    
    // Remove active class from all chips
    $('.quick-filter-chip').removeClass('active');
    
    // Add active class to clicked chip
    $(this).addClass('active');
    
    // Set the hidden input value
    $('#date_filter_type').val(filterType);
    
    // Clear custom date fields and financial year when using quick filters
    $('#from_date').val('');
    $('#to_date').val('');
    $('#financial_year').val('');
    
    // Auto-submit form
    $('#filterForm').submit();
});

// Custom Date Range - Clear quick filters and FY when dates are entered
$('#from_date, #to_date').on('change', function() {
    if ($('#from_date').val() || $('#to_date').val()) {
        $('.quick-filter-chip').removeClass('active');
        $('#date_filter_type').val('custom');
        $('#financial_year').val('');
    }
});

// Financial Year - Clear other date filters when FY is selected
$('#financial_year').on('change', function() {
    if ($(this).val()) {
        $('.quick-filter-chip').removeClass('active');
        $('#date_filter_type').val('financial_year');
        $('#from_date').val('');
        $('#to_date').val('');
    }
});

// Clear Date Filters Button
$('#clearDateFilters').on('click', function() {
    $('.quick-filter-chip').removeClass('active');
    $('#date_filter_type').val('');
    $('#from_date').val('');
    $('#to_date').val('');
    $('#financial_year').val('');
    $('#filterForm').submit();
});

// Validation before form submit
$('#filterForm').on('submit', function(e) {
    var fromDate = $('#from_date').val();
    var toDate = $('#to_date').val();
    
    // If one custom date is filled, both should be filled
    if ((fromDate && !toDate) || (!fromDate && toDate)) {
        e.preventDefault();
        alert('Please select both From Date and To Date for custom range filtering.');
        return false;
    }
    
    // Validate date order if both are filled
    if (fromDate && toDate) {
        var from = parseDate(fromDate);
        var to = parseDate(toDate);
        
        if (from > to) {
            e.preventDefault();
            alert('From Date cannot be later than To Date.');
            return false;
        }
    }
});

// Helper function to parse dd/mm/yyyy format
function parseDate(dateStr) {
    var parts = dateStr.split('/');
    // month is 0-based in JavaScript Date
    return new Date(parts[2], parts[1] - 1, parts[0]);
}
