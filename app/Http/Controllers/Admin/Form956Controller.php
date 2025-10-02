<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreForm956Request;
//use App\Models\AgentDetails;
use App\Models\Admin;
use App\Models\Form956;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use mikehaertl\pdftk\Pdf;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\JsonResponse;
use Exception;

class Form956Controller extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the forms.
     */
    public function index(): View
    {
        $forms = Form956::with(['client', 'agent'])->latest()->paginate(10);

        return view('forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new form.
     */
    public function create(Request $request): View
    {
        // Get default agent
        $agent = AgentDetails::first();

        // Get client if client_id is provided
        $client = null;
        if ($request->has('client_id')) {
            $client = DB::table('admins')
                ->where('role', 7)
                ->where('id', $request->query('client_id'))
                ->first();

            // If no client is found, you might want to handle this case
            if (!$client) {
                abort(404, 'Client not found.');
            }
        }

        // Get all clients (admins with role = 7) for dropdown
        $clients = DB::table('admins')
            ->where('role', 7)
            ->orderBy('last_name')
            ->get();

        return view('forms.create', compact('agent', 'client', 'clients'));
    }

    public function store(StoreForm956Request $request): JsonResponse|RedirectResponse
    {
        try {
            // Create the form
            $form = Form956::create($request->validated());

            // Check if the request is AJAX (from the modal)
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Form 956 created successfully.',
                    'redirect' => route('forms.show', $form)
                ], 200);
            }

            // For non-AJAX requests, redirect as before
            return redirect()->route('forms.show', $form)->with('success', 'Form 956 created successfully.');
        } catch (\Exception $e) {
            // For AJAX requests, return error response
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => ['Failed to create Form 956: ' . $e->getMessage()]]
                ], 422);
            }

            // For non-AJAX requests, redirect back with error
            return redirect()->back()->with('error', 'Failed to create Form 956.');
        }
    }

    /**
     * Display the specified form.
     */
    public function show(Form956 $form): View
    {
        $form->load(['client', 'agent']); //dd($form);

        return view('forms.show', compact('form'));
    }

    /**
     * Extract field names from the Form 956 PDF template.
     */
    public function extractFieldNames()
    {
        $templatePath = storage_path('app/public/form956_template.pdf');

        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'PDF template not found.'], 404);
        }

        try {
            $pdf = new Pdf($templatePath);
            $fields = $pdf->getDataFields();

            $fieldNames = [];
            foreach ($fields as $field) {
                if (isset($field['FieldName'])) {
                    $fieldNames[] = $field['FieldName'];
                }
            }

            return response()->json(['fields' => $fieldNames]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error extracting fields: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate PDF for the form.
     */
    public function generatePdf(Form956 $form)
    {
        $form->load(['client', 'agent']);
        $templatePath = storage_path('app/public/form956_template.pdf');

        if (!file_exists($templatePath)) {
            return back()->with('error', 'PDF template not found. Please contact the administrator.');
        }

        try {
            $pdf = new Pdf($templatePath);
            // Client address split
            $client_address_parts_line1 = "";
            $client_address_parts_line2 = "";
            $client_address_parts_line3 = "";
            $client_address_parts_postcode = "";

            if( $this->getClientAddress($form->client->id) != '') {
                $client_address = $this->getClientAddress($form->client->id);
                if($client_address !=  ''){
                    $client_address_parts = $this->formatAddressForPDFClient($client_address); //dd($client_address_parts);
                    if(!empty($client_address_parts)){
                        $client_address_parts_line1 = $client_address_parts['line1'];
                        $client_address_parts_line2 = $client_address_parts['line2'];
                        $client_address_parts_line3 = $client_address_parts['line3'];
                        $client_address_parts_postcode = $client_address_parts['postcode'];
                    }
                }
            }

            $dobFormated = 'NA';
            if($form->client->dob != ''){
                $dobArr = explode('-',$form->client->dob);
                if(!empty($dobArr)){
                    $dobFormated = $dobArr[2].'/'.$dobArr[1].'/'.$dobArr[0];
                } else{
                    $dobFormated = 'NA';
                }
            }

            $agentDeclarationDateFormated = 'NA';
            if($form->agent_declaration_date != ''){
                $agentDecArr = explode('-',$form->agent_declaration_date);
                if(!empty($agentDecArr)){
                    $agentDeclarationDateFormated = $agentDecArr[2].'/'.$agentDecArr[1].'/'.$agentDecArr[0];
                } else{
                    $agentDeclarationDateFormated = 'NA';
                }
            }

            $clientDeclarationDateFormated = 'NA';
            if($form->client_declaration_date != ''){
                $clientDecArr = explode('-',$form->client_declaration_date);
                if(!empty($clientDecArr)){
                    $clientDeclarationDateFormated = $clientDecArr[2].'/'.$clientDecArr[1].'/'.$clientDecArr[0];
                } else{
                    $clientDeclarationDateFormated = 'NA';
                }
            }
             // Agent address split
            $agent_address_parts_line1 = "";
            $agent_address_parts_line2 = "";
            $agent_address_parts_line3 = "";
            $agent_address_parts_postcode = "";

            $agent_address = $form->agent->address;
            if($agent_address !=  ''){
                $agent_address_parts = $this->formatAddressForPDFAgent($agent_address);
                //dd($agent_address_parts);
                if(!empty($agent_address_parts)){
                    $agent_address_parts_line1 = $agent_address_parts['line1'];
                    $agent_address_parts_line2 = $agent_address_parts['line2'];
                    $agent_address_parts_line3 = $agent_address_parts['line3'];
                    $agent_address_parts_postcode = $agent_address_parts['postcode'];
                }
            }

            $date_lodged_arr_formated = "";
            if($form->date_lodged != "") {
                $date_lodged_arr = explode("-",$form->date_lodged);
                if(!empty($date_lodged_arr)){
                    $date_lodged_arr_formated =$date_lodged_arr[2].' '.$date_lodged_arr[1].' '.$date_lodged_arr[0];
                }
            }
            //dd($date_lodged_arr_formated);

            $formData = [
                // Client details
                'cc.name fam' => $form->client->last_name,
                'cc.name giv' => $form->client->first_name,
                'cc.dob' =>  $dobFormated,

                 'cc.resadd str' => $client_address_parts_line1,
                'cc.resadd sub' => $client_address_parts_line2,
                'cc.resadd cntry' => $client_address_parts_line3,
                'cc.resadd pc' => $client_address_parts_postcode,

                'cc.mob' => $form->client->phone ?? $form->client->phone ?? '',
                'cc.diac id' => $form->client->client_id ?? '',
                'cc.org name' => $form->client->company_name ? $form->client->company_name : 'NA',

                // Agent details
                'mg.name fam' => $form->agent->last_name,
                'mg.name giv' => $form->agent->first_name,
                'mg.org name' => $form->agent->company_name,
                'mg.marn' => $form->agent->marn_number ?? '',
                'mg.lpn' => $form->agent->legal_practitioner_number ?? '',
                'mg.email' => $form->agent->email ?? '',
                'mg.email agree' => 'on',
                'mg.comm' => 'Yes',

                'mg.resadd str' => $agent_address_parts_line1,
                'mg.resadd sub' => $agent_address_parts_line2,
                'mg.resadd cntry' => $agent_address_parts_line3,
                'mg.resadd pc' =>  $agent_address_parts_postcode,

                'mg.postal str' => 'AS ABOVE',

                'mg.mob' => $form->agent->business_mobile ?? $form->agent->phone ?? '',

                 // Form type
                'mg.app' => $form->form_type === 'appointment' ? 'No' : 'Yes',

                'mg.title' => $form->agent->gender === 'Male' ? 'mr' : 'ms',
                'mg.title' => $form->agent->gender === 'Female' ? 'ms' : 'mr',

                // Question 12: Person receiving immigration assistance
                'cc.person rec' => $form->assistance_visa_application == 1 ? 'visa' :
                                 ($form->assistance_sponsorship == 1 ? 'sponsor' :
                                 ($form->assistance_nomination == 1 ? 'nom' : 'visa')),

                // Agent type
                'mg.prov assist' => $form->is_registered_migration_agent ? 'reg' : ($form->is_legal_practitioner ? 'Legal' : ($form->is_exempt_person ? 'exampt' : 'Off')),

                // Exempt person reason
                'mg.reason ex' => $form->agent->exempt_person_reason ?? 'Off', // Options: close, sponsor, nominator, diplom, parlia, public

                // Question 10: Is there another registered migration agent or legal practitioner
                'mg.oth mig' => 'No',

                 // Question 15: Application Date lodged,Not yet lodged
                'ta.lodged' => $date_lodged_arr_formated ?? '',
                'ta.not yet' => $form->not_lodged == '1' ? 'IAAAS' : 'Off',

                // Assistance type
                'ta.assist' => $form->assistance_visa_application ? 'Application' : ($form->assistance_cancellation ? 'Cancellation' : ($form->assistance_other ? 'Specific' : 'Off')),
                'ta.specific matter' => $form->assistance_other_details ?? '',

                // Authorized recipient
                'ar.also' => $form->is_authorized_recipient ? 'Yes' : 'No',
                'mg.ending ar' => $form->withdraw_authorized_recipient ? 'Yes' : 'No',

                // Declarations
                'mg.dec 1' => $form->agent_declared ? 'on' : 'Off', // Appointment declaration
                'mg.dec 2' => $form->is_authorized_recipient && $form->agent_declared ? 'on' : 'Off', // Authorized recipient declaration
                'cc.dec 1' => $form->client_declared ? 'on' : 'Off', // Client appointment declaration
                'cc.dec 2' => $form->is_authorized_recipient && $form->client_declared ? 'on' : 'Off', // Client authorized recipient declaration
                'mg.dec date' => $form->agent_declaration_date ? $agentDeclarationDateFormated : '',
                'cc.dec date' => $form->client_declaration_date ? $clientDeclarationDateFormated : '',
            ];

            //dd($formData);

            // Handle ending appointment declarations if form_type is withdrawal
            if ($form->form_type === 'withdrawal') {
                $formData['mg.dec 3'] = $form->agent_declared ? 'on' : 'Off'; // Ending appointment
                $formData['mg.dec 4'] = $form->withdraw_authorized_recipient && $form->agent_declared ? 'on' : 'Off'; // Withdrawal of authorized recipient
                $formData['cc.dec 3'] = $form->client_declared ? 'on' : 'Off'; // Client ending appointment
                $formData['cc.dec 4'] = $form->withdraw_authorized_recipient && $form->client_declared ? 'on' : 'Off'; // Client withdrawal of authorized recipient
            }

            $pdf->fillForm($formData)->needAppearances();

            $filename = 'form956_' . $form->client->family_name . '_' . date('Y-m-d') . '.pdf';
            return response()->streamDownload(
                fn () => $pdf->saveAs('php://output'),
                $filename
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    public function getClientAddress($clientId)
    {
        // Try to find the address where is_current = 1 for the given client_id
        $addressRecord = DB::table('client_addresses')
            ->where('client_id', $clientId)
            ->where('is_current', 1)
            ->first();

        // If a record with is_current = 1 is found, return its address
        if ($addressRecord) {
            return $addressRecord->address;
        } else {
            // If no record with is_current = 1 is found, get the latest record by created_at
            $latestAddressRecord = DB::table('client_addresses')
                ->where('client_id', $clientId)
                ->orderBy('created_at', 'desc')
                ->first();

            // Return the address from the latest record, or null if no records exist
            return $latestAddressRecord ? $latestAddressRecord->address : null;
        }
    }


    //Split Agent address
    public function formatAddressForPDFAgent($fullAddress) {
        // Extract postcode (4-digit at the end)
        preg_match('/\s*(\d{4})$/', $fullAddress, $matches);
        $postcode = $matches[1] ?? '';

        // Remove postcode
        $withoutPostcode = trim(preg_replace('/\s*\d{4}$/', '', $fullAddress));

        $line1 = $line2 = $line3 = '';

        // Split by comma
        if (strpos($withoutPostcode, ',') !== false) {
            $parts = array_map('trim', explode(',', $withoutPostcode));

            $line1 = $parts[0] ?? ''; // "123 Immigration Street"

            // Handle "Melbourne VIC" in second part
            if (!empty($parts[1])) {
                $secondPart = explode(' ', $parts[1]);

                // First word: city
                $line2 = $secondPart[0] ?? '';

                // Remaining: state
                $line3 = isset($secondPart[1]) ? implode(' ', array_slice($secondPart, 1)) : '';
            }
        }

        return [
            'line1' => $line1,   // 123 Immigration Street
            'line2' => $line2,   // Melbourne
            'line3' => $line3,   // VIC
            'postcode' => $postcode // 3000
        ];
    }


    //Split client address
    public function formatAddressForPDFClient($fullAddress) {
        // Extract postcode (4 digits at any position)
        preg_match('/\b(\d{4})\b/', $fullAddress, $matches);
        $postcode = $matches[1] ?? '';

        // Remove postcode from original address
        $cleaned = trim(preg_replace('/\b\d{4}\b/', '', $fullAddress));

        // Split by comma
        $parts = array_map('trim', explode(',', $cleaned));

        // Remove any empty or irrelevant parts (like country, if it exists)
        $parts = array_filter($parts, function ($part) {
            return !empty($part) && !preg_match('/\b(?:Australia|AU)\b/i', $part);
        });
        $parts = array_values($parts); // re-index after filter

        // Initialize
        $line1 = $line2 = $line3 = '';

        if (isset($parts[0])) {
            $line1 = $parts[0]; // Street address
        }

        if (isset($parts[1])) {
            // Try to split into city and state
            $subParts = preg_split('/\s+/', trim($parts[1]));
            $line2 = $subParts[0] ?? ''; // City
            $line3 = isset($subParts[1]) ? implode(' ', array_slice($subParts, 1)) : ''; // State
        }

        return [
            'line1' => $line1,
            'line2' => $line2,
            'line3' => $line3,
            'postcode' => $postcode
        ];
    }


    /**
     * Preview the PDF in browser.
    */
    public function previewPdf(Form956 $form)
    {
        $form->load(['client', 'agent']);  //dd($form->client);
        $templatePath = storage_path('app/public/form956_template.pdf');

        if (!file_exists($templatePath)) {
            return back()->with('error', 'PDF template not found. Please contact the administrator.');
        }

        try {

            //dd($form->agent->gender );
            $pdf = new Pdf($templatePath);

            // Client address split
            $client_address_parts_line1 = "";
            $client_address_parts_line2 = "";
            $client_address_parts_line3 = "";
            $client_address_parts_postcode = "";

            if( $this->getClientAddress($form->client->id) != '') {
                $client_address = $this->getClientAddress($form->client->id);
                if($client_address !=  ''){
                    $client_address_parts = $this->formatAddressForPDFClient($client_address); //dd($client_address_parts);
                    if(!empty($client_address_parts)){
                        $client_address_parts_line1 = $client_address_parts['line1'];
                        $client_address_parts_line2 = $client_address_parts['line2'];
                        $client_address_parts_line3 = $client_address_parts['line3'];
                        $client_address_parts_postcode = $client_address_parts['postcode'];
                    }
                }
            }



            $dobFormated = 'NA';
            if($form->client->dob != ''){
                $dobArr = explode('-',$form->client->dob);
                if(!empty($dobArr)){
                    $dobFormated = $dobArr[2].'/'.$dobArr[1].'/'.$dobArr[0];
                } else{
                    $dobFormated = 'NA';
                }
            }

            $agentDeclarationDateFormated = 'NA';
            if($form->agent_declaration_date != ''){
                $agentDecArr = explode('-',$form->agent_declaration_date);
                if(!empty($agentDecArr)){
                    $agentDeclarationDateFormated = $agentDecArr[2].'/'.$agentDecArr[1].'/'.$agentDecArr[0];
                } else{
                    $agentDeclarationDateFormated = 'NA';
                }
            }

            $clientDeclarationDateFormated = 'NA';
            if($form->client_declaration_date != ''){
                $clientDecArr = explode('-',$form->client_declaration_date);
                if(!empty($clientDecArr)){
                    $clientDeclarationDateFormated = $clientDecArr[2].'/'.$clientDecArr[1].'/'.$clientDecArr[0];
                } else{
                    $clientDeclarationDateFormated = 'NA';
                }
            }

            // Agent address split
            $agent_address_parts_line1 = "";
            $agent_address_parts_line2 = "";
            $agent_address_parts_line3 = "";
            $agent_address_parts_postcode = "";

            $agent_address = $form->agent->address;
            if($agent_address !=  ''){
                $agent_address_parts = $this->formatAddressForPDFAgent($agent_address); //dd($agent_address_parts);
                if(!empty($agent_address_parts)){
                    $agent_address_parts_line1 = $agent_address_parts['line1'];
                    $agent_address_parts_line2 = $agent_address_parts['line2'];
                    $agent_address_parts_line3 = $agent_address_parts['line3'];
                    $agent_address_parts_postcode = $agent_address_parts['postcode'];
                }
            }

            $date_lodged_arr_formated = "";
            if($form->date_lodged != "") {
                $date_lodged_arr = explode("-",$form->date_lodged);
                if(!empty($date_lodged_arr)){
                    $date_lodged_arr_formated =$date_lodged_arr[2].' '.$date_lodged_arr[1].' '.$date_lodged_arr[0];
                }
            }
            //dd($date_lodged_arr_formated);
            // Pass to PDF/blade
            $formData = [
                // Client details
                'cc.name fam' => $form->client->last_name, //$form->client->family_name
                'cc.name giv' => $form->client->first_name, //$form->client->given_names
                'cc.dob' =>  $dobFormated,

                'cc.resadd str' => $client_address_parts_line1,
                'cc.resadd sub' => $client_address_parts_line2,
                'cc.resadd cntry' => $client_address_parts_line3,
                'cc.resadd pc' => $client_address_parts_postcode,

                'cc.mob' => $form->client->phone ?? $form->client->phone ?? '',
                'cc.diac id' => $form->client->client_id ?? '',
                'cc.org name' => $form->client->company_name ? $form->client->company_name : 'NA',

                // Agent details
                'mg.name fam' => $form->agent->last_name, // Split if family/given names separate
                'mg.name giv' => $form->agent->first_name,
                'mg.org name' => $form->agent->company_name,
                'mg.marn' => $form->agent->marn_number ?? '',
                'mg.lpn' => $form->agent->legal_practitioner_number ?? '',
                'mg.email' => $form->agent->email ?? '',
                'mg.email agree' => 'on',
                'mg.comm' => 'Yes',

                'mg.resadd str' => $agent_address_parts_line1,
                'mg.resadd sub' => $agent_address_parts_line2,
                'mg.resadd cntry' => $agent_address_parts_line3,
                'mg.resadd pc' =>  $agent_address_parts_postcode,

                'mg.postal str' => 'AS ABOVE',
                'mg.mob' => $form->agent->business_mobile ?? $form->agent->phone ?? '',

                // Form type
                'mg.app' => $form->form_type === 'appointment' ? 'No' : 'Yes',

                'mg.title' => $form->agent->gender === 'Male' ? 'mr' : 'ms',
                'mg.title' => $form->agent->gender === 'Female' ? 'ms' : 'mr',

                // Question 12: Person receiving immigration assistance
                'cc.person rec' => $form->assistance_visa_application == 1 ? 'visa' :
                                 ($form->assistance_sponsorship == 1 ? 'sponsor' :
                                 ($form->assistance_nomination == 1 ? 'nom' : 'visa')),

                // Agent type
                'mg.prov assist' => $form->is_registered_migration_agent ? 'reg' : ($form->is_legal_practitioner ? 'Legal' : ($form->is_exempt_person ? 'exampt' : 'Off')),

                // Exempt person reason
                'mg.reason ex' => $form->agent->exempt_person_reason ?? 'Off', // Options: close, sponsor, nominator, diplom, parlia, public

                // Question 10: Is there another registered migration agent or legal practitioner
                'mg.oth mig' => 'No',

                 // Question 15: Application Date lodged,Not yet lodged
                'ta.lodged' => $date_lodged_arr_formated ?? '',
                'ta.not yet' => $form->not_lodged == '1' ? 'IAAAS' : 'Off',

                // Assistance type
                'ta.assist' => $form->assistance_visa_application ? 'Application' : ($form->assistance_cancellation ? 'Cancellation' : ($form->assistance_other ? 'Specific' : 'Off')),
                'ta.specific matter' => $form->assistance_other_details ?? '',

                // Authorized recipient
                'ar.also' => $form->is_authorized_recipient ? 'Yes' : 'No',
                'mg.ending ar' => $form->withdraw_authorized_recipient ? 'Yes' : 'No',

                // Declarations
                'mg.dec 1' => $form->agent_declared ? 'on' : 'Off', // Appointment declaration
                'mg.dec 2' => $form->is_authorized_recipient && $form->agent_declared ? 'on' : 'Off', // Authorized recipient declaration
                'cc.dec 1' => $form->client_declared ? 'on' : 'Off', // Client appointment declaration
                'cc.dec 2' => $form->is_authorized_recipient && $form->client_declared ? 'on' : 'Off', // Client authorized recipient declaration
                'mg.dec date' => $form->agent_declaration_date ? $agentDeclarationDateFormated : '',
                'cc.dec date' => $form->client_declaration_date ? $clientDeclarationDateFormated : '',
            ];

            //dd($formData);

            // Handle ending appointment declarations if form_type is withdrawal
            if ($form->form_type === 'withdrawal') {
                $formData['mg.dec 3'] = $form->agent_declared ? 'on' : 'Off'; // Ending appointment
                $formData['mg.dec 4'] = $form->withdraw_authorized_recipient && $form->agent_declared ? 'on' : 'Off'; // Withdrawal of authorized recipient
                $formData['cc.dec 3'] = $form->client_declared ? 'on' : 'Off'; // Client ending appointment
                $formData['cc.dec 4'] = $form->withdraw_authorized_recipient && $form->client_declared ? 'on' : 'Off'; // Client withdrawal of authorized recipient
            }
            //dd($formData);
            $pdf->fillForm($formData)->needAppearances();

            return response()->stream(
                fn () => $pdf->saveAs('php://output'),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="form956_preview.pdf"'
                ]
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified form.
     */
    public function edit(Form956 $form): View
    {
        $form->load(['client', 'agent']);
        $clients = Client::orderBy('family_name')->get();

        return view('forms.edit', compact('form', 'clients'));
    }

    /**
     * Update the specified form in storage.
     */
    public function update(StoreForm956Request $request, Form956 $form): RedirectResponse
    {
        $form->update($request->validated());

        return redirect()->route('forms.show', $form)
            ->with('success', 'Form 956 updated successfully.');
    }

    /**
     * Remove the specified form from storage.
     */
    public function destroy(Form956 $form): RedirectResponse
    {
        $form->delete();

        return redirect()->route('forms.index')
            ->with('success', 'Form 956 deleted successfully.');
    }
}
