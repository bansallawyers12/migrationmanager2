<div class="tab-pane active" id="companydetails-tab">
    <div class="content-grid">
        {{-- Company Information Card --}}
        <div class="card" style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3><i class="fas fa-building"></i> Company Information</h3>
                <a href="{{ route('clients.edit', base64_encode(convert_uuencode($fetchedData->id))) }}" 
                   class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                <div class="field-group">
                    <span class="field-label">Company Name:</span>
                    <span class="field-value">{{ $fetchedData->company->company_name ?? 'N/A' }}</span>
                </div>
                @if($fetchedData->company->trading_name)
                <div class="field-group">
                    <span class="field-label">Trading Name:</span>
                    <span class="field-value">{{ $fetchedData->company->trading_name }}</span>
                </div>
                @endif
                @if($fetchedData->company->ABN_number)
                <div class="field-group">
                    <span class="field-label">ABN:</span>
                    <span class="field-value">{{ $fetchedData->company->ABN_number }}</span>
                </div>
                @endif
                @if($fetchedData->company->ACN)
                <div class="field-group">
                    <span class="field-label">ACN:</span>
                    <span class="field-value">{{ $fetchedData->company->ACN }}</span>
                </div>
                @endif
                @if($fetchedData->company->company_type)
                <div class="field-group">
                    <span class="field-label">Business Type:</span>
                    <span class="field-value">{{ $fetchedData->company->company_type }}</span>
                </div>
                @endif
                @if($fetchedData->company->company_website)
                <div class="field-group">
                    <span class="field-label">Website:</span>
                    <span class="field-value">
                        <a href="{{ $fetchedData->company->company_website }}" target="_blank" rel="noopener noreferrer">
                            {{ $fetchedData->company->company_website }}
                        </a>
                    </span>
                </div>
                @endif
                
                {{-- Company Phone Number --}}
                <div class="field-group">
                    <span class="field-label">Phone:</span>
                    <span class="field-value">
                        <?php
                        if( \App\Models\ClientContact::where('client_id', $fetchedData->id)->exists()) {
                            $companyContacts = \App\Models\ClientContact::select('phone','country_code','contact_type','is_verified','verified_at')
                                ->where('client_id', $fetchedData->id)
                                ->where('contact_type', '!=', 'Not In Use')
                                ->get();
                        } else {
                            if( \App\Models\Admin::where('id', $fetchedData->id)->exists()){
                                $companyContacts = \App\Models\Admin::select('phone','country_code','contact_type')
                                    ->where('id', $fetchedData->id)
                                    ->get();
                            } else {
                                $companyContacts = [];
                            }
                        }
                        if( !empty($companyContacts) && count($companyContacts)>0 ){
                            $phonenoStr = "";
                            foreach($companyContacts as $conKey=>$conVal){
                                $check_verified_phoneno = $conVal->country_code."".$conVal->phone;
                                if( isset($conVal->country_code) && $conVal->country_code != "" ){
                                    $country_code = $conVal->country_code;
                                } else {
                                    $country_code = "";
                                }

                                // Format phone number to Australian standard
                                $formattedPhone = \App\Helpers\PhoneValidationHelper::formatAustralianPhone($conVal->phone, $country_code);

                                if( isset($conVal->contact_type) && $conVal->contact_type != "" ){
                                    if ( $conVal->is_verified ) {
                                        $phonenoStr .= $formattedPhone.' <i class="fas fa-check-circle verified-icon fa-lg" style="color: #28a745;" title="Verified on ' . ($conVal->verified_at ? $conVal->verified_at->format('M j, Y g:i A') : 'Unknown') . '"></i> <br/>';
                                    } else {
                                        $phonenoStr .= $formattedPhone.' <i class="far fa-circle unverified-icon fa-lg" style="color: #6c757d;" title="Not verified"></i> <br/>';
                                    }
                                } else {
                                    if ( isset($conVal->is_verified) && $conVal->is_verified ) {
                                        $phonenoStr .= $formattedPhone.' <i class="fas fa-check-circle verified-icon fa-lg" style="color: #28a745;" title="Verified on ' . ($conVal->verified_at ? $conVal->verified_at->format('M j, Y g:i A') : 'Unknown') . '"></i> <br/>';
                                    } else {
                                        $phonenoStr .= $formattedPhone.' <i class="far fa-circle unverified-icon fa-lg" style="color: #6c757d;" title="Not verified"></i> <br/>';
                                    }
                                }
                            }
                            echo $phonenoStr;
                        } else {
                            echo "N/A";
                        }?>
                    </span>
                </div>

                {{-- Company Email Address --}}
                <div class="field-group">
                    <span class="field-label">Email:</span>
                    <span class="field-value">
                        <?php
                        if( \App\Models\ClientEmail::where('client_id', $fetchedData->id)->exists()) {
                            $companyEmails = \App\Models\ClientEmail::select('email','email_type','is_verified','verified_at')
                                ->where('client_id', $fetchedData->id)
                                ->get();
                        } else {
                            if( \App\Models\Admin::where('id', $fetchedData->id)->exists()){
                                $companyEmails = \App\Models\Admin::select('email','email_type')
                                    ->where('id', $fetchedData->id)
                                    ->get();
                            } else {
                                $companyEmails = [];
                            }
                        }
                        if( !empty($companyEmails) && count($companyEmails)>0 ){
                            $emailStr = "";
                            foreach($companyEmails as $emailKey=>$emailVal){
                                $check_verified_email = $emailVal->email_type."".$emailVal->email;
                                if( isset($emailVal->email_type) && $emailVal->email_type != "" ){
                                    if ( $emailVal->is_verified ) {
                                        $emailStr .= $emailVal->email.' <i class="fas fa-check-circle verified-icon fa-lg" style="color: #28a745;" title="Verified on ' . ($emailVal->verified_at ? $emailVal->verified_at->format('M j, Y g:i A') : 'Unknown') . '"></i> <br/>';
                                    } else {
                                        $emailStr .= $emailVal->email.' <i class="far fa-circle unverified-icon fa-lg" style="color: #6c757d;" title="Not verified"></i> <br/>';
                                    }
                                } else {
                                    if ( isset($emailVal->is_verified) && $emailVal->is_verified ) {
                                        $emailStr .= $emailVal->email.' <i class="fas fa-check-circle verified-icon fa-lg" style="color: #28a745;" title="Verified on ' . ($emailVal->verified_at ? $emailVal->verified_at->format('M j, Y g:i A') : 'Unknown') . '"></i> <br/>';
                                    } else {
                                        $emailStr .= $emailVal->email.' <i class="far fa-circle unverified-icon fa-lg" style="color: #6c757d;" title="Not verified"></i> <br/>';
                                    }
                                }
                            }
                            echo $emailStr;
                        } else {
                            echo "N/A";
                        }?>
                    </span>
                </div>
            </div>
        </div>
        
        {{-- Primary Contact Person Card --}}
        @if($fetchedData->company->contactPerson)
            <div class="card" style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3><i class="fas fa-user-tie"></i> Primary Contact Person</h3>
                    <a href="{{ route('clients.detail', base64_encode(convert_uuencode($fetchedData->company->contactPerson->id))) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> View Profile
                    </a>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                    <div class="field-group">
                        <span class="field-label">Name:</span>
                        <span class="field-value">
                            <a href="{{ route('clients.detail', base64_encode(convert_uuencode($fetchedData->company->contactPerson->id))) }}" 
                               style="color: #007bff; text-decoration: none;">
                                {{ $fetchedData->company->contactPerson->first_name }} {{ $fetchedData->company->contactPerson->last_name }}
                            </a>
                        </span>
                    </div>
                    @if($fetchedData->company->contact_person_position)
                    <div class="field-group">
                        <span class="field-label">Position:</span>
                        <span class="field-value">{{ $fetchedData->company->contact_person_position }}</span>
                    </div>
                    @endif
                    @if($fetchedData->company->contactPerson->email)
                    <div class="field-group">
                        <span class="field-label">Email:</span>
                        <span class="field-value">
                            <a href="mailto:{{ $fetchedData->company->contactPerson->email }}" style="color: #007bff; text-decoration: none;">
                                {{ $fetchedData->company->contactPerson->email }}
                            </a>
                        </span>
                    </div>
                    @endif
                    @if($fetchedData->company->contactPerson->phone)
                    <div class="field-group">
                        <span class="field-label">Phone:</span>
                        <span class="field-value">{{ $fetchedData->company->contactPerson->phone }}</span>
                    </div>
                    @endif
                    @if($fetchedData->company->contactPerson->client_id)
                    <div class="field-group">
                        <span class="field-label">Client ID:</span>
                        <span class="field-value">{{ $fetchedData->company->contactPerson->client_id }}</span>
                    </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Tags Section --}}
        <div class="card">
            <h3><i class="fas fa-address-card"></i> Tag(s):   
                <span class="float-right text-muted" style="margin-left:180px;">
                <a href="javascript:;" data-id="{{$fetchedData->id}}" class="btn btn-primary opentagspopup btn-sm">  Add</a>
                </span>
            </h3>
           

            <div class="" style="overflow-wrap: break-word; word-wrap: break-word; max-width: 100%;">
                <?php 
                $normalTags = [];
                $redTags = [];
                $redTagCount = 0;
                
                if($fetchedData->tagname != ''){
                    $rs = explode(',', $fetchedData->tagname);
                    
                    // Separate IDs and names for bulk query optimization
                    $tagIds = [];
                    $tagNames = [];
                    
                    foreach($rs as $key=>$r){
                        $r = trim($r);
                        if (empty($r)) continue;
                        
                        // Separate numeric IDs from tag names
                        if (is_numeric($r) && $r > 0) {
                            $tagIds[] = (int)$r;
                        } else {
                            $tagNames[] = $r;
                        }
                    }
                    
                    // Bulk fetch tags by IDs (single query for all IDs)
                    $tagsByIds = [];
                    if (!empty($tagIds)) {
                        $tagsByIds = \App\Models\Tag::whereIn('id', $tagIds)->get()->keyBy('id');
                    }
                    
                    // Bulk fetch tags by names (single query for all names)
                    $tagsByNames = [];
                    if (!empty($tagNames)) {
                        $tagsByNames = \App\Models\Tag::whereIn('name', $tagNames)->get()->keyBy('name');
                    }
                    
                    // Process all tags and categorize them
                    foreach($rs as $key=>$r){
                        $r = trim($r);
                        if (empty($r)) continue;
                        
                        $stagd = null;
                        
                        // Try to get tag by ID first
                        if (is_numeric($r) && $r > 0) {
                            $stagd = $tagsByIds[(int)$r] ?? null;
                        }
                        
                        // If not found by ID, try by name
                        if (!$stagd) {
                            $stagd = $tagsByNames[$r] ?? null;
                        }
                        
                        // Categorize tag if found
                        if($stagd) {
                            if($stagd->tag_type == 'red') {
                                $redTags[] = $stagd;
                                $redTagCount++;
                            } else {
                                $normalTags[] = $stagd;
                            }
                        }
                    }
                }
                
                // Display normal tags
                foreach($normalTags as $tag) { ?>
                    <span class="ui label tag-normal ag-flex ag-align-center ag-space-between" style="display: inline-flex; margin: 5px 5px 5px 0;">
                        <span class="col-hr-1" style="font-size: 12px;">{{@$tag->name}}</span>
                    </span>
                <?php }
                
                // Display red tags section (hidden by default)
                if($redTagCount > 0) { ?>
                    <div class="red-tags-section" style="display: none; margin-top: 10px;">
                        <div style="margin-bottom: 5px; font-size: 11px; color: #dc3545; font-weight: bold;">
                            <i class="fas fa-exclamation-triangle"></i> Red Tags:
                        </div>
                        <?php foreach($redTags as $tag) { ?>
                            <span class="ui label tag-red ag-flex ag-align-center ag-space-between" style="display: inline-flex; margin: 5px 5px 5px 0; background-color: #dc3545; border: 1px solid #c82333;">
                                <span class="col-hr-1" style="font-size: 12px;">{{@$tag->name}}</span>
                            </span>
                        <?php } ?>
                    </div>
                    
                    <div style="margin-top: 10px;">
                        <a href="javascript:;" id="toggleRedTags" class="btn btn-sm btn-outline-danger" data-client-id="{{$fetchedData->id}}">
                            <i class="fas fa-eye"></i> Show Red Tags (<span id="redTagCount">{{$redTagCount}}</span>)
                        </a>
                    </div>
                <?php }
                ?>
            </div>
        </div>
        <style>
            .ui.label:first-child {
                margin-left: 0;
            }
            .ui.label {
                display: inline-block;
                line-height: 1;
                vertical-align: baseline;
                margin: 0 0.14285714em;
                background-color: #6777ef;
                background-image: none;
                padding: 0.5833em 0.833em;
                color: #fff;
                text-transform: none;
                font-weight: 700;
                border: 0 solid transparent;
                border-radius: 0.28571429rem;
                -webkit-transition: background .1s ease;
                transition: background .1s ease;
            }
            .ui.label.tag-red {
                background-color: #dc3545 !important;
                border: 1px solid #c82333 !important;
                color: #fff !important;
            }
            .ui.label.tag-normal {
                background-color: #6777ef;
            }
            .ag-align-center {
                align-items: center;
            }
            .ag-space-between {
                justify-content: space-between;
            }
            .col-hr-1 {
                margin-right: 5px !important;
            }
            .red-tags-section {
                padding: 10px;
                background-color: #fff5f5;
                border-left: 3px solid #dc3545;
                border-radius: 4px;
                margin-top: 10px;
            }
            #toggleRedTags {
                transition: all 0.3s ease;
            }
            #toggleRedTags:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
            }
        </style>
    </div>
</div>

<!-- Red Tags Toggle JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Red Tags Toggle Functionality
    const toggleRedTagsBtn = document.getElementById('toggleRedTags');
    const redTagsSection = document.querySelector('.red-tags-section');
    
    if (toggleRedTagsBtn && redTagsSection) {
        // Store toggle state in sessionStorage
        const storageKey = 'redTagsVisible_' + toggleRedTagsBtn.getAttribute('data-client-id');
        const isVisible = sessionStorage.getItem(storageKey) === 'true';
        
        // Set initial state
        if (isVisible) {
            redTagsSection.style.display = 'block';
            toggleRedTagsBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Red Tags (<span id="redTagCount">' + document.getElementById('redTagCount').textContent + '</span>)';
            toggleRedTagsBtn.classList.remove('btn-outline-danger');
            toggleRedTagsBtn.classList.add('btn-danger');
        }
        
        toggleRedTagsBtn.addEventListener('click', function() {
            const isCurrentlyVisible = redTagsSection.style.display !== 'none';
            
            if (isCurrentlyVisible) {
                // Hide red tags
                redTagsSection.style.display = 'none';
                this.innerHTML = '<i class="fas fa-eye"></i> Show Red Tags (<span id="redTagCount">' + document.getElementById('redTagCount').textContent + '</span>)';
                this.classList.remove('btn-danger');
                this.classList.add('btn-outline-danger');
                sessionStorage.setItem(storageKey, 'false');
            } else {
                // Show red tags
                redTagsSection.style.display = 'block';
                this.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Red Tags (<span id="redTagCount">' + document.getElementById('redTagCount').textContent + '</span>)';
                this.classList.remove('btn-outline-danger');
                this.classList.add('btn-danger');
                sessionStorage.setItem(storageKey, 'true');
            }
        });
    }
});
</script>
