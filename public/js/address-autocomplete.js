/**
 * Address Autocomplete Module
 * Handles Google Places address autocomplete and regional code calculation
 */

(function() {
    'use strict';
    
    console.log('🔍 Address autocomplete script loading...');
    console.log('📌 saveAddressInfo function available?', typeof window.saveAddressInfo);
    
    // Regional Code Classification Function for Australian Migration
    window.getRegionalCodeInfo = function(postCode) {
        //2259, 2264 to 2308, 2500 to 2526, 2528 to 2535 and 2574
        if(
            ( postCode ==2259)
            ||
            ( postCode >=2264 && postCode <= 2308 )
            ||
            ( postCode >=2500 && postCode <= 2526 )
            ||
            ( postCode >=2528 && postCode <= 2535 )
            ||
            ( postCode == 2574)
        ){
            var postCodeInfo = "Cities and major regional centres of NSW";
        }

        //2250 to 2258, 2260 to 2263, 2311 to 2490, 2527, 2536 to 2551, 2575 to 2739, 2753 to 2754, 2756 to 2758 and 2773 to 2898
        else if(
            ( postCode >=2250 && postCode <= 2258 )
            ||
            ( postCode >=2260 && postCode <= 2263 )
            ||
            ( postCode >=2311 && postCode <= 2490 )
            ||
            ( postCode == 2527)
            ||
            ( postCode >=2536 && postCode <= 2551 )
            ||
            ( postCode >=2575 && postCode <= 2739 )
            ||
            ( postCode >=2753 && postCode <= 2754 )
            ||
            ( postCode >=2756 && postCode <= 2758 )
            ||
            ( postCode >=2773 && postCode <= 2898 )
        ){
            var postCodeInfo = "Regional Centre NSW";
        }

        // NSW Metro Area - All other NSW postcodes (2000-2999)
        else if(
            ( postCode >= 2000 && postCode <= 2999 )
        ){
            var postCodeInfo = "Metro Area NSW";
        }

        //3211 to 3232, 3235, 3240, 3328, 3330 to 3333, 3340 and 3342
        else if(
            ( postCode >=3211 && postCode <= 3232 )
            ||
            ( postCode == 3235)
            ||
            ( postCode == 3240 )
            ||
            ( postCode == 3328)
            ||
            ( postCode >=3330 && postCode <= 3333 )
            ||
            ( postCode == 3340)
            ||
            ( postCode == 3342)
        ){
            var postCodeInfo = "Cities and major regional centres of VIC";
        }

        //3097 to 3099, 3139, 3233 to 3234, 3236 to 3239, 3241 to 3325, 3329, 3334, 3341,
        //3345 to 3424, 3430 to 3799, 3809 to 3909, 3912 to 3971 and 3978 to 3996
        else if(
            ( postCode >=3097 && postCode <= 3099 )
            ||
            ( postCode == 3139)
            ||
            ( postCode >= 3233 && postCode <= 3234 )
            ||
            ( postCode >= 3236 && postCode <= 3239 )
            ||
            ( postCode >= 3241 && postCode <= 3325 )
            ||
            ( postCode == 3329 )
            ||
            ( postCode == 3334 )
            ||
            ( postCode == 3341 )
            ||
            ( postCode >= 3345 && postCode <= 3424 )
            ||
            ( postCode >= 3430 && postCode <= 3799 )
            ||
            ( postCode >= 3809 && postCode <= 3909 )
            ||
            ( postCode >= 3912 && postCode <= 3971 )
            ||
            ( postCode >= 3978 && postCode <= 3996 )
        ){
            var postCodeInfo = "Regional Centre VIC";
        }

        // VIC Metro Area - All other VIC postcodes (3000-3999)
        else if(
            ( postCode >= 3000 && postCode <= 3999 )
        ){
            var postCodeInfo = "Metro Area VIC";
        }

        //4019 to 4022*, 4025*, 4037*, 4074*, 4076 to 4078*, 4207 to 4275, 4300 to 4301*,
        //4303 to 4305*, 4500 to 4506*, 4508 to 4512*, 4514 to 4516*, 4517 to 4519, 4521*,
        //4550 to 4551, 4553 to 4562, 4564 to 4569 and 4571 to 4575
        else if(
            ( postCode >=4019 && postCode <= 4022 )
            ||
            ( postCode == 4025)
            ||
            ( postCode == 4037)
            ||
            ( postCode == 4074 )
            ||
            ( postCode >= 4076 && postCode <= 4078 )
            ||
            ( postCode >= 4207 && postCode <= 4275 )
            ||
            ( postCode >= 4300 && postCode <= 4301 )
            ||
            ( postCode >= 4303 && postCode <= 4305 )
            ||
            ( postCode >= 4500 && postCode <= 4506 )
            ||
            ( postCode >= 4508 && postCode <= 4512 )
            ||
            ( postCode >= 4514 && postCode <= 4516 )
            ||
            ( postCode >= 4517 && postCode <= 4519 )
            ||
            ( postCode == 4521 )
            ||
            ( postCode >= 4550 && postCode <= 4551 )
            ||
            ( postCode >= 4553 && postCode <= 4562 )
            ||
            ( postCode >= 4564 && postCode <= 4569 )
            ||
            ( postCode >= 4571 && postCode <= 4575 )
        ){
            var postCodeInfo = "Cities and major regional centres of QLD";
        }

        //4124, 4125, 4133, 4183 to 4184, 4280 to 4287, 4306 to 4498, 4507, 4552, 4563,
        //4570 and 4580 to 4895
        else if(
            ( postCode == 4124 )
            ||
            ( postCode == 4125)
            ||
            ( postCode == 4133)
            ||
            ( postCode >= 4183 && postCode <= 4184 )
            ||
            ( postCode >= 4280 && postCode <= 4287 )
            ||
            ( postCode >= 4306 && postCode <= 4498 )
            ||
            ( postCode == 4507)
            ||
            ( postCode == 4552 )
            ||
            ( postCode == 4563 )
            ||
            ( postCode == 4570 )
            ||
            ( postCode >= 4580 && postCode <= 4895 )
        ){
            var postCodeInfo = "Regional Centre QLD";
        }

        //6000 to 6038, 6050 to 6083, 6090 to 6182, 6208 to 6211, 6214 and 6556 to 6558
        else if(
            ( postCode >= 6000 && postCode <= 6038 )
            ||
            ( postCode >= 6050 && postCode <= 6083 )
            ||
            ( postCode >= 6090 && postCode <= 6182 )
            ||
            ( postCode >= 6208 && postCode <= 6211 )
            ||
            ( postCode == 6214 )
            ||
            ( postCode >= 6556 && postCode <= 6558 )
        ){
            var postCodeInfo = "Cities and major regional centres of WA";
        }

        //5000 to 5171, 5173 to 5174, 5231 to 5235, 5240 to 5252, 5351 and 5950 to 5960
        else if(
            ( postCode >= 5000 && postCode <= 5171 )
            ||
            ( postCode >= 5173 && postCode <= 5174 )
            ||
            ( postCode >= 5231 && postCode <= 5235 )
            ||
            ( postCode >= 5240 && postCode <= 5252 )
            ||
            ( postCode == 5351 )
            ||
            ( postCode >= 5950 && postCode <= 5960 )
        ){
            var postCodeInfo = "Cities and major regional centres of SA";
        }
        //7000, 7004 to 7026, 7030 to 7109, 7140 to 7151 and 7170 to 7177
        else if(
            ( postCode == 7000 )
            ||
            ( postCode >= 7004 && postCode <= 7026 )
            ||
            ( postCode >= 7030 && postCode <= 7109 )
            ||
            ( postCode >= 7140 && postCode <= 7151 )
            ||
            ( postCode >= 7170 && postCode <= 7177 )
        ){
            var postCodeInfo = "Cities and major regional centres of TAS";
        }
        else {
            var postCodeInfo = '';
        }
        return postCodeInfo;
    }
    
    // Helper function to validate Australian postcodes
    window.isValidAustralianPostcode = function(postcode) {
        return /^\d{4}$/.test(postcode);
    }
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        console.log('🔍 External address-autocomplete.js loading...');
        initAddressAutocomplete();
        console.log('✅ External address-autocomplete.js initialized');
    });
    
    /**
     * Initialize address autocomplete functionality
     */
    function initAddressAutocomplete() {
        console.log('✅ jQuery ready - Address autocomplete initialized');
        console.log('📍 Address search inputs found:', $('.address-search-input').length);
        
        // Get configuration from data attributes
        const config = getAutocompleteConfig();
        
        // Initialize date pickers (with error handling)
        initDatePickers();
        
        // Set up event listeners
        bindRegionalCodeCalculation();
        bindAddressSearch(config);
        bindAddressSelection(config);
        bindClickOutside();
    }
    
    /**
     * Get autocomplete configuration from DOM
     */
    function getAutocompleteConfig() {
        const container = document.getElementById('addressInfoEdit');
        return {
            searchRoute: container?.dataset.searchRoute || '',
            detailsRoute: container?.dataset.detailsRoute || '',
            csrfToken: container?.dataset.csrfToken || '',
            addressCount: parseInt(container?.dataset.addressCount || '0')
        };
    }
    
    /**
     * Initialize Bootstrap datepickers with error handling
     */
    function initDatePickers() {
        try {
            if (typeof $.fn.datepicker !== 'undefined') {
                $('.date-picker').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true
                });
                console.log('✅ Datepicker initialized');
            } else {
                console.warn('⚠️ Datepicker not available, skipping...');
            }
        } catch(e) {
            console.warn('⚠️ Datepicker initialization failed:', e.message);
        }
    }
    
    /**
     * Bind regional code auto-calculation based on postcode
     */
    function bindRegionalCodeCalculation() {
        $(document).on('input', 'input[name="zip[]"]', function() {
            const postcode = $(this).val();
            const $wrapper = $(this).closest('.address-entry-wrapper');
            const $regionalCode = $wrapper.find('input[name="regional_code[]"]');
            
            if (postcode && window.isValidAustralianPostcode(postcode)) {
                const regionalInfo = window.getRegionalCodeInfo(postcode);
                $regionalCode.val(regionalInfo);
                console.log('🔢 Regional code calculated:', regionalInfo, 'from postcode:', postcode);
            } else {
                $regionalCode.val('');
            }
        });
    }
    
    /**
     * Bind address search functionality
     */
    function bindAddressSearch(config) {
        $(document).on('input', '.address-search-input', function() {
            console.log('🔍 Input detected in address search field:', $(this).val());
            const query = $(this).val();
            const $wrapper = $(this).closest('.address-entry-wrapper');
            
            if (query.length < 3) {
                console.log('⏸️ Query too short (less than 3 chars)');
                $wrapper.find('.autocomplete-suggestions').remove();
                return;
            }
            
            console.log('🚀 Sending AJAX request to backend...');
            $.ajax({
                url: config.searchRoute,
                method: 'POST',
                data: { 
                    query: query,
                    _token: config.csrfToken
                },
                success: function(response) {
                    console.log('✅ Address search response:', response);
                    if (response.predictions) {
                        renderSuggestions($wrapper, response.predictions);
                    } else {
                        console.log('No predictions in response');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Address search error:', error);
                    console.error('Response:', xhr.responseText);
                }
            });
        });
    }
    
    /**
     * Render autocomplete suggestions
     */
    function renderSuggestions($wrapper, predictions) {
        let suggestions = '<div class="autocomplete-suggestions">';
        predictions.forEach(function(prediction) {
            suggestions += `<div class="autocomplete-suggestion" data-place-id="${prediction.place_id}">${prediction.description}</div>`;
        });
        suggestions += '</div>';
        
        $wrapper.find('.autocomplete-suggestions').remove();
        $wrapper.find('.address-search-container').append(suggestions);
    }
    
    /**
     * Bind address selection handler
     */
    function bindAddressSelection(config) {
        $(document).on('click', '.autocomplete-suggestion', function() {
            const placeId = $(this).data('place-id');
            const description = $(this).text();
            const $wrapper = $(this).closest('.address-entry-wrapper');
            
            $wrapper.find('.address-search-input').val(description);
            $wrapper.find('.autocomplete-suggestions').remove();
            
            // Get place details
            fetchPlaceDetails(config, placeId, $wrapper);
        });
    }
    
    /**
     * Fetch place details from Google Places API
     */
    function fetchPlaceDetails(config, placeId, $wrapper) {
        $.ajax({
            url: config.detailsRoute,
            method: 'POST',
            data: { 
                place_id: placeId,
                _token: config.csrfToken
            },
            success: function(response) {
                console.log('Place details response:', response);
                if (response.result && response.result.address_components) {
                    populateAddressFields($wrapper, response.result);
                } else {
                    console.log('No address components in response');
                }
            },
            error: function(xhr, status, error) {
                console.error('Place details error:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }
    
    /**
     * Populate address fields from Google Places response
     */
    function populateAddressFields($wrapper, result) {
        const components = result.address_components;
        
        // Extract address components
        let addressLine1 = '';
        let suburb = '';
        let state = '';
        let postcode = '';
        let country = 'Australia';
        
        components.forEach(function(component) {
            if (component.types.includes('street_number')) {
                addressLine1 += component.long_name + ' ';
            }
            if (component.types.includes('route')) {
                addressLine1 += component.long_name;
            }
            if (component.types.includes('locality')) {
                suburb = component.long_name;
            }
            if (component.types.includes('administrative_area_level_1')) {
                state = component.short_name;
            }
            if (component.types.includes('postal_code')) {
                postcode = component.long_name;
            }
            if (component.types.includes('country')) {
                country = component.long_name;
            }
        });
        
        // Populate form fields
        $wrapper.find('input[name="address_line_1[]"]').val(addressLine1.trim());
        $wrapper.find('input[name="suburb[]"]').val(suburb);
        $wrapper.find('input[name="state[]"]').val(state);
        $wrapper.find('input[name="zip[]"]').val(postcode);
        $wrapper.find('input[name="country[]"]').val(country);
        
        // Auto-calculate regional code
        if (postcode && window.isValidAustralianPostcode(postcode)) {
            const regionalInfo = window.getRegionalCodeInfo(postcode);
            $wrapper.find('input[name="regional_code[]"]').val(regionalInfo);
            console.log('🔢 Regional code auto-filled:', regionalInfo, 'from postcode:', postcode);
        }
    }
    
    /**
     * Close suggestions when clicking outside
     */
    function bindClickOutside() {
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.address-search-container').length) {
                $('.autocomplete-suggestions').remove();
            }
        });
    }
    
    /**
     * Add another address entry
     */
    window.addAnotherAddress = function() {
        const $container = $('#addresses-container');
        const $template = $('.address-entry-wrapper:last').clone();
        
        const config = getAutocompleteConfig();
        window.addressIndex = (window.addressIndex || config.addressCount) + 1;
        
        // Update IDs and names
        $template.removeClass('address-template');
        $template.attr('data-address-index', window.addressIndex);
        $template.find('input, label').each(function() {
            const $this = $(this);
            const id = $this.attr('id');
            const name = $this.attr('name');
            
            if (id) {
                $this.attr('id', id.replace(/\d+$/, window.addressIndex));
            }
            if (name) {
                $this.attr('name', name);
            }
        });
        
        // Clear values
        $template.find('input[type="text"]').val('');
        $template.find('input[name="country[]"]').val('Australia');
        $template.find('input[name="address_id[]"]').val('');
        
        // Add remove button
        $template.prepend('<button type="button" class="remove-address-btn" onclick="removeAddressEntry(this)">&times;</button>');
        
        // Insert before the add button
        $template.insertBefore($('.add-another-address'));
        
        // Initialize date picker for new fields (with error handling)
        try {
            if (typeof $.fn.datepicker !== 'undefined') {
                $template.find('.date-picker').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true
                });
            }
        } catch(e) {
            console.warn('⚠️ Datepicker failed for new address field');
        }
    };
    
    /**
     * Remove address entry
     */
    window.removeAddressEntry = function(button) {
        if (confirm('Are you sure you want to remove this address?')) {
            $(button).closest('.address-entry-wrapper').remove();
        }
    };
    
})();

