           <!-- Form generation Tab Client -->
           <div class="tab-pane" id="formgenerations-tab">
                <div class="card full-width forms-container">
                    <!-- Subtabs Navigation -->
                    <nav class="subtabs3">
                        <button class="subtab3-button active" data-subtab="form956">Form 956</button>
                        <button class="subtab3-button" data-subtab="costform">Cost Assignment</button>
                    </nav>

                    <!-- Subtab Contents -->
                    <div class="subtab3-content" id="subtab3-content">
                        <style>
                            .form956-table th, .form956-table td ,.costform-table th, .costform-table td {
                                color: #343a40 !important;
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
                        <div class="subtab3-pane" id="costform-subtab">
                            <div class="form-header">
                                <h3 class="text-2xl font-semibold text-gray-800">Cost Assignment Form</h3>
                                <div class="form-actions">
                                    <button class="btn btn-primary btn-sm costAssignmentCreateForm inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                        <i class="fas fa-plus mr-2"></i> Create Cost Assignment
                                    </button>
                                </div>
                            </div>

                            <div class="form-list1" id="form-list1">
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
                                    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-300">
                                        <div class="border-t border-gray-200">
                                            <table class="min-w-full costform-table border border-gray-300" style="/*width: 1227px !important;*/ width:100%;">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="p-4 text-center border">Client</th>
                                                        <th class="p-4 text-center border">Agent</th>
                                                        <th class="p-4 text-center border">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white">
                                                    @foreach($formlists1 as $formlist1)
                                                        <tr class="border-t border-gray-300 hover:bg-gray-50 transition duration-150">
                                                            <!-- Client -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{$formlist1->client->first_name . ' ' . $formlist1->client->last_name}}
                                                            </td>

                                                            <!-- Agent -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                {{ $formlist1->agent->first_name. ' ' . $formlist1->agent->last_name }} <br/> ({{ $formlist1->agent->company_name }})
                                                            </td>

                                                            <!-- Actions -->
                                                            <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                                <button class="btn btn-primary btn-sm costAssignmentCreateForm inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                                                    <i class="fas fa-eye"></i> Preview Cost Assignment
                                                                </button>

                                                                <button class="btn btn-primary btn-sm visaAgreementCreateForm inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                                                    <i class="fas fa-plus mr-2"></i> Create Visa Agreement
                                                                </button>

                                                                <button class="btn btn-primary btn-sm finalizeAgreementConvertToPdf inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                                                    <i class="fas fa-lock"></i> Finalize Agreement and Upload PDF
                                                                </button>

                                                                @if(Auth::user()->role == 1 || Auth::user()->role == 12 || Auth::user()->role == 16)
                                                                    <button class="btn btn-danger btn-sm deleteCostAgreement inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200" data-id="{{ $formlist1->id }}" data-href="deletecostagreement">
                                                                        <i class="fas fa-trash-alt"></i> Delete Cost Agreement
                                                                    </button>
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

                            <div class="form-list2" id="form-list2">
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
                                    <h2>List of Agreement</h2>
                                    <p class="text-gray-600 text-center py-6">No Agreement List records found for this client.</p>
                                @else
                                    <h2>List of Agreement</h2>
                                    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-300">
                                        <div class="border-t border-gray-200">
                                            <table class="min-w-full costform-table border border-gray-300" style="/*width: 1227px !important;*/ width:100%;">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="p-4 text-center border">SNo.</th>
                                                        <th class="p-4 text-center border">Added By</th>
                                                        <th class="p-4 text-center border">File Name</th>
                                                        <th class="p-4 text-center border">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white">
                                                    <?php //echo "<pre>formlists2="; print_r($formlists2); ?>
                                                    @foreach($formlists2 as $formlistkey2=>$formlist2)
                                                    <tr class="border-t border-gray-300 hover:bg-gray-50 transition duration-150">
                                                        <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                            <?= $formlistkey2 + 1 ?>
                                                        </td>
                                                        <td class="p-4 whitespace-nowrap text-sm text-gray-700 border border-gray-300">
                                                            <?php
                                                            $admin = \App\Models\Admin::where('id', $formlist2->user_id)->first();
                                                            ?>
                                                            <?= htmlspecialchars($admin->first_name ?? 'NA') ?><br>
                                                            <?= date('d/m/Y', strtotime($formlist2->created_at)) ?>
                                                        </td>
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
                                                                    <a target="_blank" href="{{ route('documents.index', $formlist2->id) }}" class="btn btn-primary">Check To Signature</a>
                                                                @endif

                                                                @if($formlist2->status === 'signed')
                                                                    <a target="_blank" href="{{ route('download.signed', $formlist2->id) }}" class="btn btn-primary">Download Signed</a>
                                                                @endif

                                                                <a target="_blank" href="{{ route('documents.index', $formlist2->id) }}" class="btn btn-primary">Go To Document</a>


                                                                
                                                                
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

                    </div>
                </div>
            </div>

