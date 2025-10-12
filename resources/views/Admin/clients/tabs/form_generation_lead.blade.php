            <!-- Form generation Tab Lead -->
            <div class="tab-pane" id="formgenerationsL-tab">
                <div class="card full-width forms-container">
                    <!-- Subtabs Navigation -->
                    <nav class="subtabs3">
                        <button class="subtab3-button active" data-subtab="costformL">Cost Assignment</button>
                    </nav>

                    <!-- Subtab Contents -->
                    <div class="subtab3-content" id="subtab3-content">
                        <!-- Cost Assignment Form Subtab Lead -->
                        <div class="subtab3-pane active" id="costformL-subtab">
                            <div class="form-header">
                                <h3 class="text-2xl font-semibold text-gray-800">Cost Assignment Form</h3>
                                <div class="form-actions">
                                    <button class="btn btn-primary btn-sm costAssignmentCreateFormLead inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
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
                        </div>
                    </div>
                </div>
            </div>
