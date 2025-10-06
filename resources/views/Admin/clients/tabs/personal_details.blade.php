<div class="tab-pane active" id="personaldetails-tab">
                <div class="content-grid">
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3><i class="fas fa-user"></i> Personal Information</h3>
                        </div>
                        <div class="field-group">
                            <span class="field-label">Age / Date of Birth</span>
                            <span class="field-value">
                                <?php
                                if ( isset($fetchedData->age) && $fetchedData->age != '') {
                                    $verifiedDob = \App\Models\Admin::where('id',$fetchedData->id)->whereNotNull('dob_verified_date')->first();
                                    if ( $verifiedDob) {
                                        $verifiedDobTick = '<i class="fas fa-check-circle verified-icon fa-lg"></i>';
                                    } else {
                                        $verifiedDobTick = '<i class="far fa-circle unverified-icon fa-lg"></i>';
                                    }
                                    
                                    // Format DOB for display
                                    $formattedDob = 'N/A';
                                    if (isset($fetchedData->dob) && $fetchedData->dob != '') {
                                        try {
                                            $dobDate = \Carbon\Carbon::parse($fetchedData->dob);
                                            $formattedDob = $dobDate->format('d M Y'); // e.g., "15 Jan 2001"
                                        } catch (\Exception $e) {
                                            $formattedDob = 'N/A';
                                        }
                                    }
                                    ?>
                                    <span id="ageDobToggle" style="cursor: pointer;" 
                                          data-age="<?php echo htmlspecialchars($fetchedData->age); ?>" 
                                          data-dob="<?php echo htmlspecialchars($formattedDob); ?>">
                                        <span class="display-age"><?php echo $fetchedData->age; ?></span>
                                        <span class="display-dob" style="display: none;"><?php echo $formattedDob; ?></span>
                                        <?php echo $verifiedDobTick; ?>
                                    </span>
                                <?php
                                } else {
                                    echo 'N/A';
                                } ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Gender</span>
                            <span class="field-value">
                                <?php
                                if ( isset($fetchedData->gender) && $fetchedData->gender != '') {
                                    echo $fetchedData->gender;
                                } else {
                                    echo 'N/A';
                                } ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Marital Status</span>
                            <span class="field-value">
                                <?php
                                if ( isset($fetchedData->martial_status) && $fetchedData->martial_status != '') {
                                    echo $fetchedData->martial_status;
                                } else {
                                    echo 'N/A';
                                } ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Client Email</span>
                            <span class="field-value">
                                <?php
                                if( \App\Models\ClientEmail::where('client_id', $fetchedData->id)->exists()) {
                                    $clientEmails = \App\Models\ClientEmail::select('email','email_type','is_verified','verified_at')->where('client_id', $fetchedData->id)->get();
                                } else {
                                    if( \App\Models\Admin::where('id', $fetchedData->id)->exists()){
                                        $clientEmails = \App\Models\Admin::select('email','email_type')->where('id', $fetchedData->id)->get();
                                    } else {
                                        $clientEmails = array();
                                    }
                                } //dd($clientEmails);
                                if( !empty($clientEmails) && count($clientEmails)>0 ){
                                    $emailStr = "";
                                    foreach($clientEmails as $emailKey=>$emailVal){

                                        //Check email is verified or not
                                        $check_verified_email = $emailVal->email_type."".$emailVal->email;
                                        if( isset($emailVal->email_type) && $emailVal->email_type != "" ){
                                            // Show verification status for ALL email types
                                            if ( $emailVal->is_verified ) {
                                                $emailStr .= $emailVal->email.' <i class="fas fa-check-circle verified-icon fa-lg" style="color: #28a745;" title="Verified on ' . ($emailVal->verified_at ? $emailVal->verified_at->format('M j, Y g:i A') : 'Unknown') . '"></i> <br/>';
                                            } else {
                                                $emailStr .= $emailVal->email.' <i class="far fa-circle unverified-icon fa-lg" style="color: #6c757d;" title="Not verified"></i> <br/>';
                                            }
                                        } else {
                                            // For emails without type, still show verification status if available
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

                        <div class="field-group">
                            <span class="field-label">Client Phone</span>
                            <span class="field-value">
                                <?php
                                if( \App\Models\ClientContact::where('client_id', $fetchedData->id)->exists()) {
                                    $clientContacts = \App\Models\ClientContact::select('phone','country_code','contact_type','is_verified','verified_at')->where('client_id', $fetchedData->id)->where('contact_type', '!=', 'Not In Use')->get();
                                } else {
                                    if( \App\Models\Admin::where('id', $fetchedData->id)->exists()){
                                        $clientContacts = \App\Models\Admin::select('phone','country_code','contact_type')->where('id', $fetchedData->id)->get();
                                    } else {
                                        $clientContacts = array();
                                    }
                                } //dd($clientContacts);
                                if( !empty($clientContacts) && count($clientContacts)>0 ){
                                    $phonenoStr = "";
                                    foreach($clientContacts as $conKey=>$conVal){
                                        //Check phone is verified or not
                                        $check_verified_phoneno = $conVal->country_code."".$conVal->phone;
                                        if( isset($conVal->country_code) && $conVal->country_code != "" ){
                                            $country_code = $conVal->country_code;
                                        } else {
                                            $country_code = "";
                                        }

                                        if( isset($conVal->contact_type) && $conVal->contact_type != "" ){
                                            // Show verification status for ALL contact types
                                            if ( $conVal->is_verified ) {
                                                $phonenoStr .= $country_code."".$conVal->phone.' <i class="fas fa-check-circle verified-icon fa-lg" style="color: #28a745;" title="Verified on ' . ($conVal->verified_at ? $conVal->verified_at->format('M j, Y g:i A') : 'Unknown') . '"></i> <br/>';
                                            } else {
                                                $phonenoStr .= $country_code."".$conVal->phone.' <i class="far fa-circle unverified-icon fa-lg" style="color: #6c757d;" title="Not verified"></i> <br/>';
                                            }
                                        } else {
                                            // For phones without type, still show verification status if available
                                            if ( isset($conVal->is_verified) && $conVal->is_verified ) {
                                                $phonenoStr .= $country_code."".$conVal->phone.' <i class="fas fa-check-circle verified-icon fa-lg" style="color: #28a745;" title="Verified on ' . ($conVal->verified_at ? $conVal->verified_at->format('M j, Y g:i A') : 'Unknown') . '"></i> <br/>';
                                            } else {
                                                $phonenoStr .= $country_code."".$conVal->phone.' <i class="far fa-circle unverified-icon fa-lg" style="color: #6c757d;" title="Not verified"></i> <br/>';
                                            }
                                        }
                                    }
                                    echo $phonenoStr;
                                } else {
                                    echo "N/A";
                                }?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Residential Address</span>
                            <span class="field-value">
                                <?php
                                $postcode_Info = App\Models\ClientAddress::select('zip','address')->where('client_id', $fetchedData->id)->latest('id')->first();
                                if( $postcode_Info && $postcode_Info->zip != "" ){ echo $postcode_Info->zip; } else { echo 'N/A'; }
                                ?>

                                <?php
                                if( $postcode_Info && $postcode_Info->address != "" ){ echo ' / '.$postcode_Info->address; } else { echo ' / '.'N/A'; }
                                ?>
                            </span>
                        </div>
                    </div>


                    <div class="card">
                        <h3><i class="fas fa-passport"></i>Visa</h3>
                        <?php
                        $visa_Info = App\Models\ClientVisaCountry::select('visa_country','visa_type','visa_expiry_date','visa_grant_date','visa_description')->where('client_id', $fetchedData->id)->orderBy('visa_expiry_date', 'desc')->first();
                        ?>
                        <div class="field-group">
                            <span class="field-label">Visa Type</span>
                            <span class="field-value">
                                <?php
                                if( $visa_Info && $visa_Info->visa_type != "" ){
                                    $Matter_get = App\Models\Matter::select('id','title','nick_name')->where('id',$visa_Info->visa_type)->first();
                                    if(!empty($Matter_get)){
                                        echo $Matter_get->title.'('.$Matter_get->nick_name.')';
                                    } else {
                                        echo 'N/A';
                                    }
                                } else { echo 'N/A'; }
                                ?>
                            </span>
                        </div>
                        <div class="field-group">
                            <span class="field-label">Visa Expiry Date</span>
                            <span class="field-value">
                                <?php
                                if( $visa_Info && $visa_Info->visa_expiry_date != "" ){
                                    if( $visa_Info->visa_expiry_date == '0000-00-00'){
                                        echo 'N/A';
                                    } else {
                                        $verifiedVisa = \App\Models\Admin::where('id',$fetchedData->id)->whereNotNull('visa_expiry_verified_at')->first();
                                        if ( $verifiedVisa) {
                                            $verifiedVisaTick = '<i class="fas fa-check-circle verified-icon fa-lg"></i>';
                                        } else {
                                            $verifiedVisaTick = '<i class="far fa-circle unverified-icon fa-lg"></i>';
                                        }
                                        
                                        // Check if visa is expiring within 7 days
                                        $expiryDate = \Carbon\Carbon::parse($visa_Info->visa_expiry_date);
                                        $today = \Carbon\Carbon::now();
                                        $daysUntilExpiry = $today->diffInDays($expiryDate, false);
                                        
                                        $expiryClass = '';
                                        $expiryWarning = '';
                                        if ($daysUntilExpiry <= 7 && $daysUntilExpiry >= 0) {
                                            $expiryClass = ' style="color: #dc3545; font-weight: bold;"';
                                            $expiryWarning = ' data-expiry-warning="true" data-days-left="' . $daysUntilExpiry . '"';
                                        }
                                        
                                        echo '<span' . $expiryClass . $expiryWarning . '>' . $expiryDate->format('d/m/Y') . '</span> ' . $verifiedVisaTick;
                                    }
                                } else { echo 'N/A'; }
                                ?>
                            </span>
                        </div>
                        @if($visa_Info && $visa_Info->visa_description != "")
                        <div class="field-group">
                            <span class="field-label">Visa Description</span>
                            <span class="field-value">
                                <?php echo $visa_Info->visa_description; ?>
                            </span>
                        </div>
                        @endif
                        <div class="field-group">
                            <span class="field-label">Country Of Passport</span>
                            <span class="field-value">
                                <?php
                                if( $visa_Info && $visa_Info->visa_country != "" ){ echo $visa_Info->visa_country; } else { echo 'N/A'; }
                                ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">Nomi Occupation / Code / Assessing Authority</span>
                            <span class="field-value">
                                <?php
                                $clientOccupation_Info = App\Models\ClientOccupation::select('skill_assessment','nomi_occupation','occupation_code','list','visa_subclass','dates')->where('client_id', $fetchedData->id)->latest('id')->first();
                                if( $clientOccupation_Info && $clientOccupation_Info->nomi_occupation != "" ){ echo $clientOccupation_Info->nomi_occupation; } else { echo 'N/A'; }
                                ?>
                                <?php
                                if( $clientOccupation_Info && $clientOccupation_Info->occupation_code != "" ){ echo ' / '.$clientOccupation_Info->occupation_code; } else { echo ' / '.'N/A'; }
                                ?>
                                <?php
                                if( $clientOccupation_Info && $clientOccupation_Info->list != "" ){ echo ' / '.$clientOccupation_Info->list; } else { echo ' / '.'N/A'; }
                                ?>
                            </span>
                        </div>

                        <div class="field-group">
                            <span class="field-label">English Test Score</span>
                            <span class="field-value">
                                <?php
                                $clientTest_Info = App\Models\ClientTestScore::select('test_type','listening','reading','writing','speaking','overall_score','test_date')->where('client_id', $fetchedData->id)->latest('id')->first();
                                if( $clientTest_Info && $clientTest_Info->test_type != "" ){ echo $clientTest_Info->test_type.": "; } else { echo 'N/A'; }
                                ?>


                                <?php
                                if( $clientTest_Info && $clientTest_Info->listening != "" ){ echo "L".$clientTest_Info->listening; } else { echo 'N/A'; }
                                ?>
                                <?php
                                if( $clientTest_Info && $clientTest_Info->reading != "" ){ echo " R".$clientTest_Info->reading; } else { echo 'N/A'; }
                                ?>
                                <?php
                                if( $clientTest_Info && $clientTest_Info->writing != "" ){ echo " W".$clientTest_Info->writing; } else { echo 'N/A'; }
                                ?>

                                <?php
                                if( $clientTest_Info && $clientTest_Info->speaking != "" ){ echo " S".$clientTest_Info->speaking; } else { echo 'N/A'; }
                                ?>

                                <?php
                                if( $clientTest_Info && $clientTest_Info->overall_score != "" ){ echo " O".$clientTest_Info->overall_score; } else { echo 'N/A'; }
                                ?>
                            </span>
                        </div>
                    </div>


                    <?php
                    $clientQualification_Info = App\Models\ClientQualification::select('level','name','qual_campus','finish_date')->where('client_id', $fetchedData->id)->orderBy('id','desc')->get();
                    ?>
                    @if(!empty($clientQualification_Info) && $clientQualification_Info->count() > 0)
                    <div class="card">
                        <div class="qualification-section">
                            <h3><i class="fas fa-info-circle"></i> Qualification</h3>
                            <div class="qualification-list" style="overflow: hidden;">
                                <table class="table eoi-table">
                                    <thead>
                                        <tr>
                                            <th>Level</th>
                                            <th>Name</th>
                                            <th>Campus</th>
                                            <th>End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clientQualification_Info as $qualification)
                                            <tr>
                                                <td>{{ $qualification->level ?: 'N/A' }}</td>
                                                <td>{{ $qualification->name ?: 'N/A' }}</td>
                                                <td>{{ $qualification->qual_campus ?: 'N/A' }}</td>
                                                <td>{{ $qualification->finish_date ? \Carbon\Carbon::parse($qualification->finish_date)->format('d/m/Y') : 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <style>
                        /*.qualification-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        .qualification-table th, .qualification-table td {
                            padding: 10px;
                            border-bottom: 1px solid #dee2e6;
                            text-align: left;
                        }
                        .qualification-table th {
                            background-color: #f8f9fa;
                            font-weight: 600;
                            color: #6c757d !important;
                        }
                        .qualification-table tbody tr:hover {
                            background-color: #f1f5f9;
                        }
                        .qualification-table td {
                            color: #212529;
                        }*/
                    </style>


                    <?php
                    $clientExperience_Info = App\Models\ClientExperience::select('job_title','job_country','job_start_date','job_finish_date')->where('client_id', $fetchedData->id)->orderBy('id','desc')->get();
                    ?>
                    @if(!empty($clientExperience_Info) && $clientExperience_Info->count() > 0)
                    <div class="card">
                        <div class="experience-section">
                            <h3><i class="fas fa-info-circle"></i> Work Experience</h3>
                            <div class="experience-list" style="overflow: hidden;">
                                <table class="table eoi-table">
                                    <thead>
                                        <tr>
                                            <th>Job Title</th>
                                            <th>Country</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clientExperience_Info as $experience)
                                            <tr>
                                                <td>{{ $experience->job_title ?: 'N/A' }}</td>
                                                <td>{{ $experience->job_country ?: 'N/A' }}</td>
                                                <td>{{ $experience->job_start_date ? \Carbon\Carbon::parse($experience->job_start_date)->format('d/m/Y') : 'N/A' }}</td>
                                                <td>{{ $experience->job_finish_date ? \Carbon\Carbon::parse($experience->job_finish_date)->format('d/m/Y') : 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <style>
                       /* .experience-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        .experience-table th, .experience-table td {
                            padding: 10px;
                            border-bottom: 1px solid #dee2e6;
                            text-align: left;
                        }
                        .experience-table th {
                            background-color: #f8f9fa;
                            font-weight: 600;
                            color: #6c757d !important;
                        }
                        .experience-table tbody tr:hover {
                            background-color: #f1f5f9;
                        }
                        .experience-table td {
                            color: #212529;
                        }*/
                    </style>



                    @if(!empty($clientFamilyDetails) && $clientFamilyDetails->count() > 0)
                    <div class="card">
                        <div class="relationship-section">
                            <h3><i class="fas fa-address-card"></i> Relationships</h3>
                            <div class="relationship-list" style="max-height: 300px; overflow-y: auto;">
                                <table class="table relationship-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Relation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clientFamilyDetails as $relationship)
                                            <?php
                                            //dd($relationship->related_client_id);
                                            if(isset($relationship->related_client_id) && $relationship->related_client_id != "")
                                            { //Existing Client
                                                $relatedClientInfo = App\Models\Admin::select('client_id','first_name','last_name')->where('id', $relationship->related_client_id)->first();
                                                //dd($relatedClientInfo);
                                                if($relatedClientInfo){
                                                    $relatedClientId = $relatedClientInfo->client_id;
                                                    $relatedClientFullName = $relatedClientInfo->first_name.' '.$relatedClientInfo->last_name."<br/>";
                                                    $relatedClientFullName .= $relatedClientId;
                                                } else {
                                                    $relatedClientId = 'NA';
                                                    $relatedClientFullName = 'NA';
                                                }
                                            }  else { //New Client
                                                $relatedClientId = 'NA';
                                                $relatedClientFullName = $relationship->first_name . ' ' . $relationship->last_name;
                                            }?>
                                            <tr>
                                                <td style="color: #6c757d;">
                                                    <?php
                                                    if(isset($relationship->related_client_id) && $relationship->related_client_id != "")
                                                    { ?>
                                                        <a href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$relationship->related_client_id)))}}"><?php echo $relatedClientFullName;?> </a>
                                                    <?php
                                                    }  else {
                                                        echo $relatedClientFullName;
                                                    } ?>
                                                </td>
                                                <td style="color: #6c757d;">{{ $relationship->relationship_type ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <style>
                        .relationship-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        .relationship-table th, .relationship-table td {
                            padding: 10px;
                            border-bottom: 1px solid #dee2e6;
                            text-align: left;
                        }
                        .relationship-table th {
                            background-color: #f8f9fa;
                            font-weight: 600;
                            color: #6c757d !important;
                        }
                        .relationship-table tbody tr:hover {
                            background-color: #f1f5f9;
                        }
                    </style>


                    <?php
                    if($fetchedData->related_files != '')
                    { ?>
                    <div class="card">
                        <h3><i class="fas fa-address-card"></i> Related Files</h3>
                        <div class="field-group">
                            <ul style="margin-left: 15px;">
                                <?php
                                //if($fetchedData->related_files != '')
                                //{
                                    $exploder = explode(',', $fetchedData->related_files);
                                    foreach($exploder AS $EXP)
                                    {
                                        $relatedclients = \App\Models\Admin::where('id', $EXP)->first();
                                        ?>
                                        <li><a target="_blank" href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$relatedclients->id)))}}">{{$relatedclients->first_name}} {{$relatedclients->last_name}}</a></li>
                                    <?php
                                    }
                                //} ?>
                            </ul>
                        </div>
                    </div>
                    <?php
                    } ?>

                    <?php
                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                    //dd($matter_cnt);
                    if($matter_cnt >0)
                    {
                        //Display reference values
                        $matter_dis_ref_info_arr = array(); // Always a Collection
                        if($id1)
                        { //if client unique reference id is present in url
                            $matter_dis_ref_info_arr = \App\Models\ClientMatter::select('department_reference','other_reference')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                        }
                        else
                        {
                            $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                            //dd($matter_cnt);
                            if($matter_cnt >0){
                                $matter_dis_ref_info_arr = \App\Models\ClientMatter::select('department_reference','other_reference')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                            }
                        } //dd($matter_dis_ref_info_arr);


                        if(
                            ( isset($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->department_reference != '' )
                            ||
                            ( isset($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->other_reference != '' )
                        )
                        { ?>
                            <div class="card">
                                <h3><i class="fas fa-user"></i> Reference Information</h3>
                                <div class="field-group">
                                    <span class="field-label">Department Reference</span>
                                    <span class="field-value">
                                        <?php
                                        if( isset($matter_dis_ref_info_arr) && !empty($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->department_reference != '') {
                                            echo $matter_dis_ref_info_arr->department_reference;
                                        } else {
                                            echo 'N/A';
                                        }?>

                                    </span>
                                </div>
                                <div class="field-group">
                                    <span class="field-label">Other Reference</span>
                                    <span class="field-value">
                                        <?php
                                        if( isset($matter_dis_ref_info_arr) && !empty($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->other_reference != ''){
                                            echo $matter_dis_ref_info_arr->other_reference;
                                        } else {
                                            echo 'N/A';
                                        } ?>
                                    </span>
                                </div>
                            </div>
                        <?php
                        }
                    }
                    ?>

                    <?php
                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                    //dd($matter_cnt);
                    if($matter_cnt >0)
                    {
                    ?>
                        <div class="card">
                            <h3><i class="fas fa-user"></i> Matter Assignee  <a style="margin-left: 110px;" class="changeMatterAssignee" href="javascript:;" role="button">Change Assignee</a></h3>

                            <?php
                            //Display reference values
                            $matter_dis_ref_info_arr = array(); // Always a Collection
                            if($id1)
                            { //if client unique reference id is present in url
                                $matter_dis_ref_info_arr = \App\Models\ClientMatter::select('sel_migration_agent','sel_person_responsible','sel_person_assisting')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                            }
                            else
                            {
                                $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                                //dd($matter_cnt);
                                if($matter_cnt >0){
                                    $matter_dis_ref_info_arr = \App\Models\ClientMatter::select('sel_migration_agent','sel_person_responsible','sel_person_assisting')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                                }
                            } //dd($matter_dis_ref_info_arr);
                            ?>

                            <div class="field-group">
                                <span class="field-label">Migration Agent</span>
                                <span class="field-value">
                                    <?php
                                    if( isset($matter_dis_ref_info_arr) && !empty($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->sel_migration_agent != '') {
                                        $mig_agent_info_arr = \App\Models\Admin::select('first_name','last_name')->where('id', $matter_dis_ref_info_arr->sel_migration_agent)->first();
                                        if($mig_agent_info_arr){
                                            echo $mig_agent_info_arr->first_name.' '.$mig_agent_info_arr->last_name;
                                        }
                                    } else {
                                        echo 'N/A';
                                    }?>

                                </span>
                            </div>
                            <div class="field-group">
                                <span class="field-label">Person Responsible</span>
                                <span class="field-value">
                                    <?php
                                    if( isset($matter_dis_ref_info_arr) && !empty($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->sel_person_responsible != ''){
                                        $sel_person_responsible_info_arr = \App\Models\Admin::select('first_name','last_name')->where('id', $matter_dis_ref_info_arr->sel_person_responsible)->first();
                                        if($sel_person_responsible_info_arr){
                                            echo $sel_person_responsible_info_arr->first_name.' '.$sel_person_responsible_info_arr->last_name;
                                        }
                                    } else {
                                        echo 'N/A';
                                    } ?>
                                </span>
                            </div>

                            <div class="field-group">
                                <span class="field-label">Person Assisting</span>
                                <span class="field-value">
                                    <?php
                                    if( isset($matter_dis_ref_info_arr) && !empty($matter_dis_ref_info_arr) && $matter_dis_ref_info_arr->sel_person_assisting != ''){
                                        $sel_person_assisting_info_arr = \App\Models\Admin::select('first_name','last_name')->where('id', $matter_dis_ref_info_arr->sel_person_assisting)->first();
                                        if($sel_person_assisting_info_arr){
                                            echo $sel_person_assisting_info_arr->first_name.' '.$sel_person_assisting_info_arr->last_name;
                                        }
                                    } else {
                                        echo 'N/A';
                                    } ?>
                                </span>
                            </div>
                        </div>
                    <?php
                    } ?>


                    <?php
                    $clientEoi_Info = App\Models\ClientEoiReference::where('client_id', $fetchedData->id)->orderBy('id','desc')->get();
                    ?>
                    @if(!empty($clientEoi_Info) && $clientEoi_Info->count() > 0)
                    <div class="card">
                        <div class="eoi-section">
                            <h3><i class="fas fa-file-alt"></i> EOI Reference Information</h3>
                            <div class="eoi-list" style="overflow: hidden;/*max-height: 300px; overflow-y: auto;*/">
                                <table class="table eoi-table">
                                    <thead>
                                        <tr>
                                            <th>Subclass</th>
                                            <th>Occupation</th>
                                            <th>Point</th>
                                            <th>State</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clientEoi_Info as $Eoi_Info)
                                            <tr>
                                                <td>{{ $Eoi_Info->EOI_subclass ?: 'N/A' }}</td>
                                                <td>{{ $Eoi_Info->EOI_occupation ?: 'N/A' }}</td>
                                                <td>{{ $Eoi_Info->EOI_point ?: 'N/A' }}</td>
                                                <td>{{ $Eoi_Info->EOI_state ?: 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <style>
                        .eoi-table{
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                            table-layout: fixed;
                        }
                        .eoi-table th, .eoi-table td {
                            padding: 10px;
                            border-bottom: 1px solid #dee2e6;
                            text-align: left;
                            word-wrap: break-word;
                            overflow-wrap: break-word;
                        }
                        .eoi-table th {
                            background-color: #f8f9fa;
                            font-weight: 600;
                            color: #6c757d !important;
                        }
                        .eoi-table tbody tr:hover {
                            background-color: #f1f5f9;
                        }
                        .eoi-table td {
                            color: #212529;
                        }
                    </style>



                    <div class="card">
                        <h3><i class="fas fa-address-card"></i> Tag(s):   
                            <span class="float-right text-muted" style="margin-left:180px;">
                            <a href="javascript:;" data-id="{{$fetchedData->id}}" class="btn btn-primary opentagspopup btn-sm">  Add</a>
                            </span>
                        </h3>
                       

                        <div class="">
                            <?php 
                            $tags = '';
                            if($fetchedData->tagname != ''){
                                $rs = explode(',', $fetchedData->tagname);
                                foreach($rs as $key=>$r){
                                    $stagd = \App\Models\Tag::where('id','=',$r)->first();
                                    if($stagd)
                                    { ?>
                                        <span class="ui label ag-flex ag-align-center ag-space-between" style="display: inline-flex;">
                                            <span class="col-hr-1" style="font-size: 12px;">{{@$stagd->name}} <!--<a href="{{--URL::to('/admin/clients/removetag?rem_id='.$key.'&c='.$fetchedData->id)--}}" class="removetag" ><i class="fa fa-times"></i></a>--></span>
                                        </span>
                                    <?php
                                    }
                                }
                            }
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
                        .ag-align-center {
                            align-items: center;
                        }
                        .ag-space-between {
                            justify-content: space-between;
                        }
                        .col-hr-1 {
                            margin-right: 5px !important;
                        }

                    </style>

                </div>
            </div>

            <!-- Age/DOB Toggle JavaScript -->
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ageDobToggle = document.getElementById('ageDobToggle');
                if (ageDobToggle) {
                    ageDobToggle.addEventListener('click', function() {
                        const ageSpan = this.querySelector('.display-age');
                        const dobSpan = this.querySelector('.display-dob');
                        
                        if (ageSpan && dobSpan) {
                            if (ageSpan.style.display === 'none') {
                                // Currently showing DOB, switch to Age
                                ageSpan.style.display = 'inline';
                                dobSpan.style.display = 'none';
                            } else {
                                // Currently showing Age, switch to DOB
                                ageSpan.style.display = 'none';
                                dobSpan.style.display = 'inline';
                            }
                        }
                    });
                }
                
                // Visa Expiry Warning Check
                const visaExpiryElement = document.querySelector('[data-expiry-warning="true"]');
                if (visaExpiryElement) {
                    const daysLeft = visaExpiryElement.getAttribute('data-days-left');
                    const expiryDate = visaExpiryElement.textContent;
                    
                    let message = '⚠️ VISA EXPIRY WARNING ⚠️\n\n';
                    if (daysLeft == 0) {
                        message += 'This visa expires TODAY (' + expiryDate + ')!\n\n';
                    } else if (daysLeft == 1) {
                        message += 'This visa expires TOMORROW (' + expiryDate + ')!\n\n';
                    } else {
                        message += 'This visa expires in ' + daysLeft + ' days (' + expiryDate + ')!\n\n';
                    }
                    message += 'Please take immediate action to renew or extend this visa.\n\nClick OK to continue viewing the client details.';
                    
                    alert(message);
                }
            });
            </script>
