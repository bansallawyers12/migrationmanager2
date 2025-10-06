                        <!-- Edit View -->
                        <div id="addressInfoEdit" class="edit-view" style="display: none;">
                            <style>
                                .address-search-container {
                                    position: relative;
                                }
                                .autocomplete-suggestions {
                                    position: absolute;
                                    top: 100%;
                                    left: 0;
                                    right: 0;
                                    background: white;
                                    border: 1px solid #ddd;
                                    border-top: none;
                                    max-height: 200px;
                                    overflow-y: auto;
                                    z-index: 1000;
                                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                                }
                                .autocomplete-suggestion {
                                    padding: 10px;
                                    cursor: pointer;
                                    border-bottom: 1px solid #eee;
                                }
                                .autocomplete-suggestion:hover {
                                    background-color: #f5f5f5;
                                }
                                .address-entry-wrapper {
                                    border: 1px solid #e0e0e0;
                                    border-radius: 8px;
                                    padding: 20px;
                                    margin-bottom: 20px;
                                    background-color: #fafafa;
                                }
                                .address-entry-wrapper:last-child {
                                    border: 2px dashed #ccc;
                                    background-color: #fff;
                                }
                                .remove-address-btn {
                                    float: right;
                                    background: #dc3545;
                                    color: white;
                                    border: none;
                                    border-radius: 50%;
                                    width: 30px;
                                    height: 30px;
                                    cursor: pointer;
                                    margin-top: -10px;
                                    margin-right: -10px;
                                }
                                .remove-address-btn:hover {
                                    background: #c82333;
                                }
                                .address-fields-grid {
                                    display: grid;
                                    grid-template-columns: 1fr 1fr;
                                    gap: 15px;
                                }
                                .address-fields-grid.full-width {
                                    grid-template-columns: 1fr;
                                }
                                .form-group {
                                    margin-bottom: 15px;
                                }
                                .form-group label {
                                    display: block;
                                    margin-bottom: 5px;
                                    font-weight: 600;
                                    color: #333;
                                }
                                .form-group input {
                                    width: 100%;
                                    padding: 10px;
                                    border: 1px solid #ddd;
                                    border-radius: 4px;
                                    font-size: 14px;
                                }
                                .form-group input:focus {
                                    outline: none;
                                    border-color: #007bff;
                                    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
                                }
                                .date-fields {
                                    display: grid;
                                    grid-template-columns: 1fr 1fr;
                                    gap: 15px;
                                }
                                .add-another-address {
                                    background: #28a745;
                                    color: white;
                                    border: none;
                                    padding: 10px 20px;
                                    border-radius: 4px;
                                    cursor: pointer;
                                    margin-top: 20px;
                                }
                                .add-another-address:hover {
                                    background: #218838;
                                }
                            </style>
                            
                            <div id="addresses-container">
                                @if(count($clientAddresses) > 0)
                                    @foreach($clientAddresses as $index => $address)
                                    <div class="address-entry-wrapper" data-address-index="{{ $index }}">
                                        @if($index > 0)
                                            <button type="button" class="remove-address-btn" onclick="removeAddressEntry(this)">&times;</button>
                                        @endif
                                        
                                        <input type="hidden" name="address_id[]" value="{{ $address->id }}">
                                        
                                        <!-- Address Search -->
                                        <div class="form-group address-search-container">
                                            <label for="address_search_{{ $index }}">Search Address</label>
                                            <input type="text" 
                                                   id="address_search_{{ $index }}" 
                                                   name="address_search[]" 
                                                   class="address-search-input" 
                                                   placeholder="Start typing an address..."
                                                   autocomplete="off"
                                                   data-address-index="{{ $index }}"
                                                   value="{{ $address->address_line_1 ? $address->address_line_1 . ', ' . $address->suburb . ', ' . $address->state : $address->address }}">
                                        </div>
                                        
                                        <!-- Structured Address Fields -->
                                        <div class="address-fields-grid">
                                            <div class="form-group">
                                                <label for="address_line_1_{{ $index }}">Address Line 1 *</label>
                                                <input type="text" 
                                                       id="address_line_1_{{ $index }}" 
                                                       name="address_line_1[]" 
                                                       value="{{ $address->address_line_1 ?? '' }}"
                                                       placeholder="Street number and name"
                                                       required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="address_line_2_{{ $index }}">Address Line 2</label>
                                                <input type="text" 
                                                       id="address_line_2_{{ $index }}" 
                                                       name="address_line_2[]" 
                                                       value="{{ $address->address_line_2 ?? '' }}"
                                                       placeholder="Apartment, suite, unit, etc.">
                                            </div>
                                        </div>
                                        
                                        <div class="address-fields-grid">
                                            <div class="form-group">
                                                <label for="suburb_{{ $index }}">Suburb *</label>
                                                <input type="text" 
                                                       id="suburb_{{ $index }}" 
                                                       name="suburb[]" 
                                                       value="{{ $address->suburb ?? $address->city ?? '' }}"
                                                       placeholder="Suburb"
                                                       required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="state_{{ $index }}">State *</label>
                                                <input type="text" 
                                                       id="state_{{ $index }}" 
                                                       name="state[]" 
                                                       value="{{ $address->state ?? '' }}"
                                                       placeholder="State"
                                                       required>
                                            </div>
                                        </div>
                                        
                                        <div class="address-fields-grid">
                                            <div class="form-group">
                                                <label for="zip_{{ $index }}">Postcode *</label>
                                                <input type="text" 
                                                       id="zip_{{ $index }}" 
                                                       name="zip[]" 
                                                       value="{{ $address->zip ?? '' }}"
                                                       placeholder="Postcode"
                                                       required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="country_{{ $index }}">Country</label>
                                                <input type="text" 
                                                       id="country_{{ $index }}" 
                                                       name="country[]" 
                                                       value="{{ $address->country ?? 'Australia' }}"
                                                       placeholder="Country"
                                                       required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="regional_code_{{ $index }}">Regional Code</label>
                                            <input type="text" 
                                                   id="regional_code_{{ $index }}" 
                                                   name="regional_code[]" 
                                                   value="{{ $address->regional_code ?? '' }}"
                                                   placeholder="Regional code (auto-calculated)"
                                                   readonly>
                                        </div>
                                        
                                        <div class="date-fields">
                                            <div class="form-group">
                                                <label for="address_start_date_{{ $index }}">Start Date</label>
                                                <input type="text" 
                                                       id="address_start_date_{{ $index }}" 
                                                       name="address_start_date[]" 
                                                       value="{{ $address->start_date ? date('d/m/Y', strtotime($address->start_date)) : '' }}"
                                                       placeholder="dd/mm/yyyy"
                                                       class="date-picker">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="address_end_date_{{ $index }}">End Date</label>
                                                <input type="text" 
                                                       id="address_end_date_{{ $index }}" 
                                                       name="address_end_date[]" 
                                                       value="{{ $address->end_date ? date('d/m/Y', strtotime($address->end_date)) : '' }}"
                                                       placeholder="dd/mm/yyyy"
                                                       class="date-picker">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <!-- Default empty address entry -->
                                    <div class="address-entry-wrapper address-template" data-address-index="0">
                                        <input type="hidden" name="address_id[]" value="">
                                        
                                        <!-- Address Search -->
                                        <div class="form-group address-search-container">
                                            <label for="address_search_0">Search Address</label>
                                            <input type="text" 
                                                   id="address_search_0" 
                                                   name="address_search[]" 
                                                   class="address-search-input" 
                                                   placeholder="Start typing an address..."
                                                   autocomplete="off"
                                                   data-address-index="0">
                                        </div>
                                        
                                        <!-- Structured Address Fields -->
                                        <div class="address-fields-grid">
                                            <div class="form-group">
                                                <label for="address_line_1_0">Address Line 1 *</label>
                                                <input type="text" 
                                                       id="address_line_1_0" 
                                                       name="address_line_1[]" 
                                                       value=""
                                                       placeholder="Street number and name"
                                                       required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="address_line_2_0">Address Line 2</label>
                                                <input type="text" 
                                                       id="address_line_2_0" 
                                                       name="address_line_2[]" 
                                                       value=""
                                                       placeholder="Apartment, suite, unit, etc.">
                                            </div>
                                        </div>
                                        
                                        <div class="address-fields-grid">
                                            <div class="form-group">
                                                <label for="suburb_0">Suburb *</label>
                                                <input type="text" 
                                                       id="suburb_0" 
                                                       name="suburb[]" 
                                                       value=""
                                                       placeholder="Suburb"
                                                       required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="state_0">State *</label>
                                                <input type="text" 
                                                       id="state_0" 
                                                       name="state[]" 
                                                       value=""
                                                       placeholder="State"
                                                       required>
                                            </div>
                                        </div>
                                        
                                        <div class="address-fields-grid">
                                            <div class="form-group">
                                                <label for="zip_0">Postcode *</label>
                                                <input type="text" 
                                                       id="zip_0" 
                                                       name="zip[]" 
                                                       value=""
                                                       placeholder="Postcode"
                                                       required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="country_0">Country</label>
                                                <input type="text" 
                                                       id="country_0" 
                                                       name="country[]" 
                                                       value="Australia"
                                                       placeholder="Country"
                                                       required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="regional_code_0">Regional Code</label>
                                            <input type="text" 
                                                   id="regional_code_0" 
                                                   name="regional_code[]" 
                                                   value=""
                                                   placeholder="Regional code (auto-calculated)"
                                                   readonly>
                                        </div>
                                        
                                        <div class="date-fields">
                                            <div class="form-group">
                                                <label for="address_start_date_0">Start Date</label>
                                                <input type="text" 
                                                       id="address_start_date_0" 
                                                       name="address_start_date[]" 
                                                       value=""
                                                       placeholder="dd/mm/yyyy"
                                                       class="date-picker">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="address_end_date_0">End Date</label>
                                                <input type="text" 
                                                       id="address_end_date_0" 
                                                       name="address_end_date[]" 
                                                       value=""
                                                       placeholder="dd/mm/yyyy"
                                                       class="date-picker">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <button type="button" class="add-another-address" onclick="addAnotherAddress()">
                                <i class="fas fa-plus"></i> Add Another Address
                            </button>

                            <div class="edit-actions">
                                <button type="button" class="btn btn-primary" onclick="saveAddressInfo()">Save</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit('addressInfo')">Cancel</button>
                            </div>

                            <script>
                            // Regional Code Classification Function for Australian Migration
                            function getRegionalCodeInfo(postCode) {
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
                            }

                            // Helper function to validate Australian postcodes
                            function isValidAustralianPostcode(postcode) {
                                return /^\d{4}$/.test(postcode);
                            }

                            $(document).ready(function() {
                                let addressIndex = {{ count($clientAddresses) }};
                                
                                // Initialize date pickers
                                $('.date-picker').datepicker({
                                    format: 'dd/mm/yyyy',
                                    autoclose: true,
                                    todayHighlight: true
                                });
                                
                                // Auto-calculate regional code based on postcode
                                $(document).on('input', 'input[name="zip[]"]', function() {
                                    const postcode = $(this).val();
                                    const $wrapper = $(this).closest('.address-entry-wrapper');
                                    const $regionalCode = $wrapper.find('input[name="regional_code[]"]');
                                    
                                    if (postcode && isValidAustralianPostcode(postcode)) {
                                        const regionalInfo = getRegionalCodeInfo(postcode);
                                        $regionalCode.val(regionalInfo);
                                    } else {
                                        $regionalCode.val('');
                                    }
                                });
                                
                                // Address search functionality
                                $(document).on('input', '.address-search-input', function() {
                                    const query = $(this).val();
                                    const $wrapper = $(this).closest('.address-entry-wrapper');
                                    const addressIndex = $wrapper.data('address-index');
                                    
                                    if (query.length < 3) {
                                        $wrapper.find('.autocomplete-suggestions').remove();
                                        return;
                                    }
                                    
                                    $.ajax({
                                        url: '{{ route("admin.clients.searchAddressFull") }}',
                                        method: 'POST',
                                        data: { query: query },
                                        success: function(response) {
                                            if (response.predictions) {
                                                let suggestions = '<div class="autocomplete-suggestions">';
                                                response.predictions.forEach(function(prediction) {
                                                    suggestions += `<div class="autocomplete-suggestion" data-place-id="${prediction.place_id}">${prediction.description}</div>`;
                                                });
                                                suggestions += '</div>';
                                                
                                                $wrapper.find('.autocomplete-suggestions').remove();
                                                $wrapper.find('.address-search-container').append(suggestions);
                                            }
                                        }
                                    });
                                });
                                
                                // Handle address selection
                                $(document).on('click', '.autocomplete-suggestion', function() {
                                    const placeId = $(this).data('place-id');
                                    const description = $(this).text();
                                    const $wrapper = $(this).closest('.address-entry-wrapper');
                                    
                                    $wrapper.find('.address-search-input').val(description);
                                    $wrapper.find('.autocomplete-suggestions').remove();
                                    
                                    // Get place details
                                    $.ajax({
                                        url: '{{ route("admin.clients.getPlaceDetails") }}',
                                        method: 'POST',
                                        data: { place_id: placeId },
                                        success: function(response) {
                                            if (response.result && response.result.address_components) {
                                                const components = response.result.address_components;
                                                const formattedAddress = response.result.formatted_address;
                                                
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
                                                if (postcode && isValidAustralianPostcode(postcode)) {
                                                    const regionalInfo = getRegionalCodeInfo(postcode);
                                                    $wrapper.find('input[name="regional_code[]"]').val(regionalInfo);
                                                }
                                            }
                                        }
                                    });
                                });
                                
                                // Close suggestions when clicking outside
                                $(document).on('click', function(e) {
                                    if (!$(e.target).closest('.address-search-container').length) {
                                        $('.autocomplete-suggestions').remove();
                                    }
                                });
                            });
                            
                            function addAnotherAddress() {
                                const $container = $('#addresses-container');
                                const $template = $('.address-entry-wrapper:last').clone();
                                
                                addressIndex++;
                                
                                // Update IDs and names
                                $template.removeClass('address-template');
                                $template.attr('data-address-index', addressIndex);
                                $template.find('input, label').each(function() {
                                    const $this = $(this);
                                    const id = $this.attr('id');
                                    const name = $this.attr('name');
                                    
                                    if (id) {
                                        $this.attr('id', id.replace(/\d+$/, addressIndex));
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
                                
                                // Initialize date picker for new fields
                                $template.find('.date-picker').datepicker({
                                    format: 'dd/mm/yyyy',
                                    autoclose: true,
                                    todayHighlight: true
                                });
                            }
                            
                            function removeAddressEntry(button) {
                                if (confirm('Are you sure you want to remove this address?')) {
                                    $(button).closest('.address-entry-wrapper').remove();
                                }
                            }
                            </script>
                        </div>
