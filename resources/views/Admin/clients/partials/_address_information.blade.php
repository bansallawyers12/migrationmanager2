{{-- Address Information Section --}}
<section class="form-section">
    <div class="section-header">
        <h3><i class="fas fa-home"></i> Address Information</h3>
        <div class="section-actions">
            <button type="button" class="edit-section-btn" onclick="toggleEditMode('addressInfo')">
                <i class="fas fa-pen"></i>
            </button>
            <button type="button" class="add-section-btn" onclick="addAddress()" title="Add Address">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    
    {{-- Summary View --}}
    <div id="addressInfoSummary" class="summary-view">
        @if($clientAddresses->count() > 0)
            <div>
                @foreach($clientAddresses as $index => $address)
                    <div class="address-entry-compact" style="margin-bottom: 12px; padding: 12px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #007bff;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: center;">
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">ADDRESS:</span>
                                <span class="summary-value" style="color: #212529; font-weight: 500;">
                                    @php
                                        $addressParts = array_filter([
                                            $address->address_line_1,
                                            $address->address_line_2,
                                            $address->suburb,
                                            $address->state,
                                            $address->zip,
                                            ($address->country && $address->country !== 'Australia') ? $address->country : null
                                        ]);
                                        
                                        if (!empty($addressParts)) {
                                            echo implode(', ', $addressParts);
                                        } elseif ($address->address) {
                                            echo $address->address;
                                        } else {
                                            echo 'Not set';
                                        }
                                    @endphp
                                </span>
                            </div>
                            @if($address->start_date)
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">START DATE:</span>
                                <span class="summary-value" style="color: #212529;">{{ date('d/m/Y', strtotime($address->start_date)) }}</span>
                            </div>
                            @endif
                            @if($address->end_date)
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">END DATE:</span>
                                <span class="summary-value" style="color: #212529;">{{ date('d/m/Y', strtotime($address->end_date)) }}</span>
                            </div>
                            @endif
                            @if($address->regional_code)
                            <div class="summary-item-inline">
                                <span class="summary-label" style="font-weight: 600; color: #6c757d; font-size: 0.85em;">REGIONAL CODE:</span>
                                <span class="summary-value" style="color: #212529; font-weight: 500;">{{ $address->regional_code }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <p>No addresses added yet.</p>
            </div>
        @endif
    </div>

    {{-- Edit View --}}
    <div id="addressInfoEdit" 
         class="edit-view" 
         style="display: none;"
         data-search-route="{{ route('admin.clients.searchAddressFull') }}"
         data-details-route="{{ route('admin.clients.getPlaceDetails') }}"
         data-csrf-token="{{ csrf_token() }}"
         data-address-count="{{ count($clientAddresses) }}">
        
        <div id="addresses-container">
            @if(count($clientAddresses) > 0)
                @foreach($clientAddresses as $index => $address)
                @include('Admin.clients.partials._address_entry', [
                    'index' => $index,
                    'address' => $address,
                    'showRemoveButton' => $index > 0
                ])
                @endforeach
            @else
                {{-- Default empty address entry --}}
                @include('Admin.clients.partials._address_entry', [
                    'index' => 0,
                    'address' => null,
                    'showRemoveButton' => false
                ])
            @endif
        </div>
        
        <button type="button" class="add-another-address" onclick="addAnotherAddress()">
            <i class="fas fa-plus"></i> Add Another Address
        </button>

        <div class="edit-actions">
            <button type="button" class="btn btn-primary" onclick="console.log('Save button clicked!'); saveAddressInfo();">Save</button>
            <button type="button" class="btn btn-secondary" onclick="cancelEdit('addressInfo')">Cancel</button>
        </div>
    </div>
</section>

<script>
// Regional Code Classification Function for Australian Migration
window.getRegionalCodeInfo = function(postCode) {
    // NT - Northern Territory: 0800 to 0999 (All are Regional centres)
    if(
        ( postCode >= 800 && postCode <= 999 )
    ){
        var postCodeInfo = "Regional Centre NT";
    }

    // ACT - Australian Capital Territory: 0200 to 0299 and 2600 to 2639
    else if(
        ( postCode >= 200 && postCode <= 299 )
        ||
        ( postCode >= 2600 && postCode <= 2639 )
    ){
        var postCodeInfo = "Regional City ACT";
    }

    //2259, 2264 to 2308, 2500 to 2526, 2528 to 2535 and 2574
    else if(
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
        var postCodeInfo = "Regional City NSW";
    }

    //2250 to 2258, 2260 to 2263, 2311 to 2490, 2527, 2536 to 2551, 2575 to 2599, 2640 to 2739, 2753 to 2754, 2756 to 2758 and 2773 to 2898
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
        ( postCode >=2575 && postCode <= 2599 )
        ||
        ( postCode >=2640 && postCode <= 2739 )
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
        var postCodeInfo = "Regional City VIC";
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
        var postCodeInfo = "Regional City QLD";
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

    // QLD Metro Area - All other QLD postcodes (4000-4999)
    else if(
        ( postCode >= 4000 && postCode <= 4999 )
    ){
        var postCodeInfo = "Metro Area QLD";
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
        var postCodeInfo = "Regional City WA";
    }

    // WA Regional centres - All other WA postcodes (6000-6999)
    else if(
        ( postCode >= 6000 && postCode <= 6999 )
    ){
        var postCodeInfo = "Regional Centre WA";
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
        var postCodeInfo = "Regional City SA";
    }

    // SA Regional centres - All other SA postcodes (5000-5999)
    else if(
        ( postCode >= 5000 && postCode <= 5999 )
    ){
        var postCodeInfo = "Regional Centre SA";
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
        var postCodeInfo = "Regional City TAS";
    }

    // TAS Regional centres - All other TAS postcodes (7000-7999)
    else if(
        ( postCode >= 7000 && postCode <= 7999 )
    ){
        var postCodeInfo = "Regional Centre TAS";
    }

    // Other Australian Territories (Christmas Island, Cocos Islands)
    else if(
        ( postCode == 6798 )  // Christmas Island
        ||
        ( postCode == 6799 )  // Cocos (Keeling) Islands
    ){
        var postCodeInfo = "Regional Centre - Other Territories";
    }

    else {
        var postCodeInfo = '';
    }
    return postCodeInfo;
};

// Helper function to validate Australian postcodes
window.isValidAustralianPostcode = function(postcode) {
    return /^\d{4}$/.test(postcode);
};

// Initialize regional code calculation when DOM is ready
$(document).ready(function() {
    console.log('🚀 DOM ready - initializing regional code functionality');
    
    // Auto-calculate regional code based on postcode input
    $(document).on('input', 'input[name="zip[]"]', function() {
        console.log('📝 Postcode input detected:', $(this).val());
        const postcode = $(this).val();
        const $wrapper = $(this).closest('.address-entry-wrapper');
        const $regionalCode = $wrapper.find('input[name="regional_code[]"]');
        
        console.log('🔍 Wrapper found:', $wrapper.length > 0);
        console.log('🔍 Regional code field found:', $regionalCode.length > 0);
        
        if (postcode && window.isValidAustralianPostcode(postcode)) {
            console.log('✅ Valid postcode:', postcode);
            const regionalInfo = window.getRegionalCodeInfo(postcode);
            console.log('🏷️ Regional info:', regionalInfo);
            $regionalCode.val(regionalInfo);
            console.log('🔢 Regional code calculated:', regionalInfo, 'from postcode:', postcode);
        } else {
            console.log('❌ Invalid postcode or empty:', postcode);
            $regionalCode.val('');
        }
    });
    
    // Also bind to the edit mode toggle to ensure functionality works
    $(document).on('click', '.edit-section-btn', function() {
        console.log('✏️ Edit mode toggled - regional code functions should be available');
        console.log('🔧 getRegionalCodeInfo available:', typeof window.getRegionalCodeInfo);
        console.log('🔧 isValidAustralianPostcode available:', typeof window.isValidAustralianPostcode);
    });
    
    console.log('✅ Regional code functionality initialized');
    console.log('🔧 getRegionalCodeInfo available:', typeof window.getRegionalCodeInfo);
    console.log('🔧 isValidAustralianPostcode available:', typeof window.isValidAustralianPostcode);
    
    // Test function for debugging - call testRegionalCode() in console
    window.testRegionalCode = function(postcode) {
        console.log('🧪 Testing regional code for:', postcode);
        if (window.isValidAustralianPostcode(postcode)) {
            const result = window.getRegionalCodeInfo(postcode);
            console.log('✅ Result:', result);
            return result;
        } else {
            console.log('❌ Invalid postcode format');
            return null;
        }
    };
    
    console.log('🧪 Test function available: testRegionalCode(postcode)');
});
</script>

