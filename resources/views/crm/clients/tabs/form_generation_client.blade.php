           <!-- Form generation Tab Client -->
           <div class="tab-pane" id="formgenerations-tab">
                <div class="card full-width forms-container">
                    <!-- Subtabs Navigation -->
                    <nav class="subtabs3">
                        <button class="subtab3-button active" data-subtab="form956">Form 956</button>
                        <button class="subtab3-button" data-subtab="costform">Cost Assignment</button>
                        <button class="subtab3-button" data-subtab="createcostform">Create Cost Assignment</button>
                    </nav>

                    <!-- Subtab Contents -->
                    <div class="subtab3-content" id="subtab3-content">
                        <style>
                            .form956-table th, .form956-table td ,.costform-table th, .costform-table td {
                                color: #343a40 !important;
                            }
                            /* Add padding to Cost Assignment form container */
                            #costform-subtab .bg-white.shadow-lg {
                                padding: 2rem 3rem !important;
                            }
                            #costform-subtab .form-list1,
                            #costform-subtab .form-list2 {
                                padding: 0 1.5rem;
                            }
                            /* Button grid layout improvements */
                            #costform-subtab .form-list1 [style*="grid-template-columns"] {
                                display: grid !important;
                                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
                                gap: 1rem !important;
                                align-items: stretch !important;
                            }
                            #costform-subtab .form-list1 button {
                                width: 100% !important;
                                display: inline-flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                                font-weight: 500 !important;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
                            }
                            #costform-subtab .form-list1 button:hover {
                                transform: translateY(-1px) !important;
                                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
                            }
                            @media (max-width: 768px) {
                                #costform-subtab .bg-white.shadow-lg {
                                    padding: 1.5rem 1rem !important;
                                }
                                #costform-subtab .form-list1,
                                #costform-subtab .form-list2 {
                                    padding: 0 0.5rem;
                                }
                                #costform-subtab .form-list1 [style*="grid-template-columns"] {
                                    grid-template-columns: 1fr !important;
                                }
                                #costform-subtab .form-list1 button {
                                    white-space: normal !important;
                                    padding: 0.75rem 1rem !important;
                                }
                            }
                            @media (min-width: 769px) and (max-width: 1024px) {
                                #costform-subtab .form-list1 [style*="grid-template-columns"] {
                                    grid-template-columns: repeat(2, 1fr) !important;
                                }
                            }
                        </style>
                        <!-- form956 Subtab -->
                        <div class="subtab3-pane active" id="form956-subtab">
                            <div class="form-header">
                                <h3 class="text-2xl font-semibold text-gray-800">Form 956</h3>
                                <div class="form-actions">
                                    <button class="btn btn-primary btn-sm form956CreateForm inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200" id="new-form-btn">
                                        <i class="fas fa-plus mr-2"></i> Create Form 956
                                    </button>
                                </div>
                            </div>

                            <div class="form-list" id="form-list">
                                <?php
                                // Fetch Form 956 records for the given client
                                $formlists = collect(); // Always a Collection
                                if($id1)
                                { //if client unique reference id is present in url
                                    $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                                    if($matter_info_arr && $matter_info_arr->id){
                                        $formlists = \App\Models\Form956::where('client_id', $fetchedData->id)
                                        ->where('client_matter_id', $matter_info_arr->id)
                                        ->with(['client', 'agent']) // Eager load relationships
                                        ->orderBy('created_at', 'DESC')
                                        ->get(); //dd($formlists);
                                    }
                                }
                                else
                                {
                                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                                    //dd($matter_cnt);
                                    if($matter_cnt >0){
                                        $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                                        if($matter_info_arr && $matter_info_arr->id){
                                            $formlists = \App\Models\Form956::where('client_id', $fetchedData->id)
                                            ->where('client_matter_id', $matter_info_arr->id)
                                            ->with(['client', 'agent']) // Eager load relationships
                                            ->orderBy('created_at', 'DESC')
                                            ->get(); //dd($formlists);
                                        }
                                    }
                                }
                                ?>

                                @if($formlists->isEmpty())
                                    <p class="text-gray-600 text-center py-6">No Form 956 records found for this client.</p>
                                @else
                                    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-300">
                                        <div class="border-t border-gray-200">
                                            <table class="min-w-full form956-table border border-gray-300" style="/*width: 1227px !important;*/ width:100%;">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="p-4 text-center border">Client</th>
                                                        <th class="p-4 text-center border">Form Type</th>
                                                        <th class="p-4 text-center border">Agent</th>
                                                        <th class="p-4 text-center border">Agent Type</th>
                                                        <th class="p-4 text-center border">Assistance Type</th>
                                                        <th class="p-4 text-center border">Authorized Recipient</th>
                                                        <th class="p-4 text-center border">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white">
                                                    @foreach($formlists as $formlist)
                                                        <tr class="border-t border-gray-300 hover:bg-gray-50 transition duration-150">
                                                            <!-- Client -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{$formlist->client->first_name . ' ' . $formlist->client->last_name}}
                                                            </td>
                                                            <!-- Form Type -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{ $formlist->form_type === 'appointment' ? 'New Appointment' : 'Withdrawal' }}
                                                            </td>
                                                            <!-- Agent -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{ $formlist->agent->first_name. ' ' . $formlist->agent->last_name }} <br/> ({{ $formlist->agent->company_name }})
                                                            </td>
                                                            <!-- Agent Type -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                @if ($formlist->is_registered_migration_agent) Registered Migration Agent @endif
                                                                @if ($formlist->is_legal_practitioner) Legal Practitioner @endif
                                                                @if ($formlist->is_exempt_person) Exempt Person @endif
                                                            </td>
                                                            <!-- Assistance Type -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                @if ($formlist->assistance_visa_application) Visa Application<br> @endif
                                                                @if ($formlist->assistance_sponsorship) Sponsorship<br> @endif
                                                                @if ($formlist->assistance_nomination) Nomination<br> @endif
                                                                @if ($formlist->assistance_cancellation) Cancellation<br> @endif
                                                                @if ($formlist->assistance_ministerial_intervention) Ministerial Intervention<br> @endif
                                                                @if ($formlist->assistance_other) Other: {{ $formlist->assistance_other_details }} @endif
                                                            </td>
                                                            <!-- Authorized Recipient -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{ $formlist->is_authorized_recipient ? 'Yes' : 'No' }}
                                                            </td>
                                                            <!-- Actions -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                <a title="Preview PDF" href="{{ route('forms.preview', $formlist) }}" target="_blank" > <i class="fas fa-eye"></i></a><br/>
                                                                <a title="Download PDF" href="{{ route('forms.pdf', $formlist) }}" ><i class="fas fa-download"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        

                        <!-- Cost Assignment Form Subtab -->
                        <div class="subtab3-pane" id="costform-subtab" style="padding: 0 2rem;">
                            <div class="form-list1" id="form-list1" style="padding: 0;">
                                <?php
                                $formlists1 = collect(); // Always a Collection
                                // Fetch cost_assignment_forms for the given client
                                if($id1)
                                { //if client unique reference id is present in url
                                    $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                                    if($matter_info_arr && $matter_info_arr->id){
                                        $formlists1 = \App\Models\CostAssignmentForm::where('client_id', $fetchedData->id)
                                        ->where('client_matter_id', $matter_info_arr->id)
                                        ->with(['client', 'agent']) // Eager load relationships
                                        ->orderBy('created_at', 'DESC')
                                        ->get(); //dd($formlists1);
                                    }
                                }
                                else
                                {
                                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                                    if($matter_cnt >0){
                                        $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                                        if($matter_info_arr && $matter_info_arr->id){
                                            $formlists1 = \App\Models\CostAssignmentForm::where('client_id', $fetchedData->id)
                                            ->where('client_matter_id', $matter_info_arr->id)
                                            ->with(['client', 'agent']) // Eager load relationships
                                            ->orderBy('created_at', 'DESC')
                                            ->get(); //dd($formlists1);
                                        }
                                    }
                                } 
                                ?>

                                @if($formlists1->isEmpty())
                                    <p class="text-gray-600 text-center py-6">No Cost Assignment records found for this client.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($formlists1 as $formlist1)
                                            <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6 hover:shadow-xl transition duration-200">
                                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; align-items: stretch;">
                                                    <button class="btn btn-primary btn-sm costAssignmentCreateForm inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200" style="min-height: 44px; white-space: nowrap;">
                                                        <i class="fas fa-eye mr-2"></i> Preview Cost Assignment
                                                    </button>

                                                    <button class="btn btn-primary btn-sm visaAgreementCreateForm inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200" style="min-height: 44px; white-space: nowrap;">
                                                        <i class="fas fa-plus mr-2"></i> Create Visa Agreement
                                                    </button>

                                                    <button class="btn btn-primary btn-sm finalizeAgreementConvertToPdf inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200" style="min-height: 44px; white-space: nowrap;">
                                                        <i class="fas fa-lock mr-2"></i> Finalize Agreement and Upload PDF
                                                    </button>

                                                    @if(Auth::user()->role == 1 || Auth::user()->role == 12 || Auth::user()->role == 16)
                                                        <button class="btn btn-danger btn-sm deleteCostAgreement inline-flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200" style="min-height: 44px; white-space: nowrap;" data-id="{{ $formlist1->id }}" data-href="deletecostagreement">
                                                            <i class="fas fa-trash-alt mr-2"></i> Delete Cost Agreement
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="form-list2" id="form-list2" style="padding: 0 1.5rem;">
                                <?php
                                $formlists2 = collect(); // Always a Collection
                                // Fetch cost_assignment_forms for the given client
                                if($id1)
                                { //if client unique reference id is present in url
                                    $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                                    if($matter_info_arr && $matter_info_arr->id){
                                        $formlists2 = \App\Models\Document::where('client_id', $fetchedData->id)
                                        ->where('client_matter_id', $matter_info_arr->id)
                                        ->where('type', 'client')
                                        ->where('doc_type', 'agreement')
                                        ->orderBy('updated_at', 'DESC')
                                        ->get(); //dd($formlists2);
                                    }
                                }
                                else
                                {
                                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                                    if($matter_cnt >0){
                                        $matter_info_arr = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->orderBy('id', 'desc')->first();
                                        if($matter_info_arr && $matter_info_arr->id){
                                            $formlists2 = \App\Models\Document::where('client_id', $fetchedData->id)
                                            ->where('client_matter_id', $matter_info_arr->id)
                                            ->where('type', 'client')
                                            ->where('doc_type', 'agreement')
                                            ->orderBy('updated_at', 'DESC')
                                            ->get(); //dd($formlists2);
                                        }
                                    }
                                }
                                //echo "<pre>formlists2="; print_r($formlists2);
                                ?>
                                @if($formlists2->isEmpty())
                                    <h2 class="mb-4">List of Agreement</h2>
                                    <p class="text-gray-600 text-center py-6">No Agreement List records found for this client.</p>
                                @else
                                    <h2 class="mb-4">List of Agreement</h2>
                                    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-300" style="padding: 2rem 3rem;">
                                        <div class="border-t border-gray-200">
                                            <table class="min-w-full costform-table border border-gray-300" style="/*width: 1227px !important;*/ width:100%;">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="p-4 text-center border">File Name</th>
                                                        <th class="p-4 text-center border">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white">
                                                    <?php //echo "<pre>formlists2="; print_r($formlists2); ?>
                                                    @foreach($formlists2 as $formlistkey2=>$formlist2)
                                                    <tr class="border-t border-gray-300 hover:bg-gray-50 transition duration-150">
                                                        <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                            <a target="_blank" class="dropdown-item" href="{{$formlist2->myfile}}">
                                                            {{$formlist2->file_name.'.'.$formlist2->filetype}}
                                                            </a>
                                                        </td>
                                                        <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                            @if (strtolower($formlist2->filetype) === 'pdf')
                                                                @if ($formlist2->status === 'draft')
                                                                    <a target="_blank" href="{{ route('documents.edit', $formlist2->id) }}" class="btn btn-primary">Create Signature Link</a>
                                                                @endif

                                                                @if($formlist2->status === 'sent')
                                                                    <a target="_blank" href="{{ route('signatures.show', $formlist2->id) }}" class="btn btn-primary">Check To Signature</a>
                                                                @endif

                                                                @if($formlist2->status === 'signed')
                                                                    <a target="_blank" href="{{ route('documents.download.signed', $formlist2->id) }}" class="btn btn-primary">Download Signed</a>
                                                                @endif

                                                                <a target="_blank" href="{{ route('signatures.show', $formlist2->id) }}" class="btn btn-primary">Go To Document</a>


                                                                
                                                                
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>

                        <!-- Create Cost Assignment Form Subtab -->
                        <div class="subtab3-pane" id="createcostform-subtab" style="padding: 2rem;">
                            <form method="POST" action="{{route('clients.savecostassignment')}}" name="costAssignmentform" id="costAssignmentform" autocomplete="off">
                                @csrf
                                <!-- Hidden Fields for Client and Client Matter ID -->
                                <input type="hidden" name="client_id" id="cost_assignment_client_id">
                                <input type="hidden" name="client_matter_id" id="cost_assignment_client_matter_id">
                                <input type="hidden" name="agent_id" id="costassign_agent_id">
                                <!-- Error Message Container -->
                                <div class="custom-error-msg"></div>

                                <!-- Agent Details (Read-only, assuming agent is pre-fetched) -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="font-medium text-gray-900">Agent Details</h6>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="text-sm font-medium text-gray-700">Agent Name - <span id="costassign_agent_name_label"></span></label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="text-sm font-medium text-gray-700">Business Name - <span id="costassign_business_name_label"></span></label>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label class="text-sm font-medium text-gray-700">Client Matter Name - <span id="costassign_client_matter_name_label"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="primary_info">

                                    <div style="margin-bottom: 15px;">
                                        <h4>Block Fee</h4>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="Block_1_Ex_Tax">Block 1 Incl. Tax</label>
                                                {!! html()->text('Block_1_Ex_Tax')->class('form-control')->id('Block_1_Ex_Tax')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Block 1 Incl. Tax' ) !!}
                                                @if ($errors->has('Block_1_Ex_Tax'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Block_1_Ex_Tax') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="Block_2_Ex_Tax">Block 2 Incl. Tax</label>
                                                {!! html()->text('Block_2_Ex_Tax')->class('form-control')->id('Block_2_Ex_Tax')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Block 2 Incl. Tax' ) !!}
                                                @if ($errors->has('Block_2_Ex_Tax'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Block_2_Ex_Tax') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="Block_3_Ex_Tax">Block 3 Incl. Tax</label>
                                                {!! html()->text('Block_3_Ex_Tax')->class('form-control')->id('Block_3_Ex_Tax')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Block 3 Incl. Tax' ) !!}
                                                @if ($errors->has('Block_3_Ex_Tax'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Block_3_Ex_Tax') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="TotalBLOCKFEE">Total Block Fee</label>
                                                {!! html()->text('TotalBLOCKFEE')->class('form-control')->id('TotalBLOCKFEE')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Total Block Fee')->attribute('readonly', 'readonly' ) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div style="margin-bottom: 15px;">
                                        <h4>Department Fee</h4>
                                        <div class="col-3">
                                            <label for="surcharge">Surcharge</label>
                                            <select class="form-control" name="surcharge" id="surcharge">
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <label for="Dept_Base_Application_Charge">Dept Base Application Charge</label>
                                                        {!! html()->text('Dept_Base_Application_Charge')->class('form-control')->id('Dept_Base_Application_Charge')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Base Application Charge' ) !!}
                                                    </div>
                                                    <div class="col-3">
                                                        <label for="Dept_Base_Application_Charge_no_of_person">Person</label>
                                                        <input type="number" name="Dept_Base_Application_Charge_no_of_person" id="Dept_Base_Application_Charge_no_of_person"
                                                            class="form-control" placeholder="1" value="1" min="0" step="any" />
                                                    </div>
                                                </div>

                                                @if ($errors->has('Dept_Base_Application_Charge'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Dept_Base_Application_Charge') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <label for="Dept_Non_Internet_Application_Charge">Dept Non Internet Application Charge</label>
                                                        {!! html()->text('Dept_Non_Internet_Application_Charge')->class('form-control')->id('Dept_Non_Internet_Application_Charge')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Non Internet Application Charge' ) !!}
                                                    </div>
                                                    <div class="col-3">
                                                        <label for="Dept_Non_Internet_Application_Charge_no_of_person">Person</label>
                                                        <input type="number" name="Dept_Non_Internet_Application_Charge_no_of_person" id="Dept_Non_Internet_Application_Charge_no_of_person"
                                                            class="form-control" placeholder="1" value="1" min="0" step="any" />
                                                    </div>
                                                </div>
                                                @if ($errors->has('Dept_Non_Internet_Application_Charge'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Dept_Non_Internet_Application_Charge') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <label for="Dept_Additional_Applicant_Charge_18_Plus">Dept Additional Applicant Charge 18 +</label>
                                                        {!! html()->text('Dept_Additional_Applicant_Charge_18_Plus')->class('form-control')->id('Dept_Additional_Applicant_Charge_18_Plus')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Additional Applicant Charge 18 Plus' ) !!}
                                                    </div>
                                                    <div class="col-3">
                                                        <label for="Dept_Additional_Applicant_Charge_18_Plus_no_of_person">Person</label>
                                                        <input type="number" name="Dept_Additional_Applicant_Charge_18_Plus_no_of_person" id="Dept_Additional_Applicant_Charge_18_Plus_no_of_person"
                                                            class="form-control" placeholder="1" value="1" min="0" step="any" />
                                                    </div>
                                                </div>
                                                @if ($errors->has('Dept_Additional_Applicant_Charge_18_Plus'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Dept_Additional_Applicant_Charge_18_Plus') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <label for="Dept_Additional_Applicant_Charge_Under_18">Dept Add. Applicant Charge Under 18</label>
                                                        {!! html()->text('Dept_Additional_Applicant_Charge_Under_18')->class('form-control')->id('Dept_Additional_Applicant_Charge_Under_18')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Additional Applicant Charge Under 18' ) !!}
                                                    </div>
                                                    <div class="col-3">
                                                        <label for="Dept_Additional_Applicant_Charge_Under_18_no_of_person">Person</label>
                                                        <input type="number" name="Dept_Additional_Applicant_Charge_Under_18_no_of_person" id="Dept_Additional_Applicant_Charge_Under_18_no_of_person"
                                                            class="form-control" placeholder="1" value="1" min="0" step="any" />
                                                    </div>
                                                </div>
                                                @if ($errors->has('Dept_Additional_Applicant_Charge_Under_18'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Dept_Additional_Applicant_Charge_Under_18') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <label for="Dept_Subsequent_Temp_Application_Charge">Dept Subsequent Temp App Charge</label>
                                                        {!! html()->text('Dept_Subsequent_Temp_Application_Charge')->class('form-control')->id('Dept_Subsequent_Temp_Application_Charge')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Subsequent Temp Application Charge' ) !!}
                                                    </div>
                                                    <div class="col-3">
                                                        <label for="Dept_Subsequent_Temp_Application_Charge_no_of_person">Person</label>
                                                        <input type="number" name="Dept_Subsequent_Temp_Application_Charge_no_of_person" id="Dept_Subsequent_Temp_Application_Charge_no_of_person"
                                                            class="form-control" placeholder="1" value="1" min="0" step="any" />
                                                    </div>
                                                </div>
                                                @if ($errors->has('Dept_Subsequent_Temp_Application_Charge'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Dept_Subsequent_Temp_Application_Charge') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <label for="Dept_Second_VAC_Instalment_Charge_18_Plus">Dept Second VAC Instalment 18+</label>
                                                        {!! html()->text('Dept_Second_VAC_Instalment_Charge_18_Plus')->class('form-control')->id('Dept_Second_VAC_Instalment_Charge_18_Plus')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Second VAC Instalment Charge 18 Plus' ) !!}
                                                    </div>
                                                    <div class="col-3">
                                                        <label for="Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person">Person</label>
                                                        <input type="number" name="Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person" id="Dept_Second_VAC_Instalment_Charge_18_Plus_no_of_person"
                                                            class="form-control" placeholder="1" value="1" min="0" step="any" />
                                                    </div>
                                                </div>
                                                @if ($errors->has('Dept_Second_VAC_Instalment_Charge_18_Plus'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Dept_Second_VAC_Instalment_Charge_18_Plus') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <label for="Dept_Second_VAC_Instalment_Under_18">Dept Second VAC Instalment Under 18</label>
                                                        {!! html()->text('Dept_Second_VAC_Instalment_Under_18')->class('form-control')->id('Dept_Second_VAC_Instalment_Under_18')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Second VAC Instalment Under 18' ) !!}
                                                    </div>
                                                    <div class="col-3">
                                                        <label for="Dept_Second_VAC_Instalment_Under_18_no_of_person">Person</label>
                                                        <input type="number" name="Dept_Second_VAC_Instalment_Under_18_no_of_person" id="Dept_Second_VAC_Instalment_Under_18_no_of_person"
                                                            class="form-control" placeholder="1" value="1" min="0" step="any" />
                                                    </div>
                                                </div>
                                                @if ($errors->has('Dept_Second_VAC_Instalment_Under_18'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Dept_Second_VAC_Instalment_Under_18') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="Dept_Nomination_Application_Charge">Dept Nomination Application Charge</label>
                                                {!! html()->text('Dept_Nomination_Application_Charge')->class('form-control')->id('Dept_Nomination_Application_Charge')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Nomination Application Charge' ) !!}
                                                @if ($errors->has('Dept_Nomination_Application_Charge'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Dept_Nomination_Application_Charge') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="Dept_Sponsorship_Application_Charge">Dept Sponsorship Application Charge</label>
                                                {!! html()->text('Dept_Sponsorship_Application_Charge')->class('form-control')->id('Dept_Sponsorship_Application_Charge')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Dept Sponsorship Application Charge' ) !!}
                                                @if ($errors->has('Dept_Sponsorship_Application_Charge'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('Dept_Sponsorship_Application_Charge') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="TotalDoHACharges">Total DoHA Charges</label>
                                                {!! html()->text('TotalDoHACharges')->class('form-control')->id('TotalDoHACharges')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Total DoHA Charges')->attribute('readonly', 'readonly' ) !!}
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="TotalDoHASurcharges">Total DoHA Surcharges</label>
                                                {!! html()->text('TotalDoHASurcharges')->class('form-control')->id('TotalDoHASurcharges')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Total DoHA Surcharges' )->attribute('readonly', 'readonly') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div style="margin-bottom: 15px;">
                                        <h4>Additional Fee</h4>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="additional_fee_1">Additional Fee1</label>
                                                {!! html()->text('additional_fee_1')->class('form-control')->id('additional_fee_1')->attribute('autocomplete', 'off')->attribute('placeholder', 'Enter Additional Fee' ) !!}
                                                @if ($errors->has('additional_fee_1'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('additional_fee_1') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Submit Button -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Save Cost Assignment</button>
                                        <button type="button" class="btn btn-secondary ml-2" onclick="switchToCostAssignmentList()">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

