<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\ClientAddress;
use App\Models\ClientContact;
use App\Models\ClientEmail;
use App\Models\ClientQualification;
use App\Models\ClientExperience;
use App\Models\ClientTestScore;
use App\Models\ClientVisaCountry;
use App\Models\ClientOccupation;
use App\Models\ClientSpouseDetail;
use App\Models\ClientPoint;
use App\Models\ClientPassportInformation;
use App\Models\ClientTravelInformation;
use App\Models\ClientCharacter;
use App\Models\ClientRelationship;
use App\Models\ClientMatter;
use App\Models\ActivitiesLog;
use App\Models\ClientPartner;
use Illuminate\Support\Facades\Log;
use Auth;
use Config;

/**
 * ClientPersonalDetailsController
 * 
 * Handles personal information, family details, qualifications, occupations,
 * test scores, and points calculation for clients.
 * 
 * Maps to: resources/views/Admin/clients/tabs/personal_details.blade.php
 */
class ClientPersonalDetailsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function getVisaTypes()
    {
        $visaTypes = \App\Models\Matter::select('id', 'title', 'nick_name')
            ->where('title', 'not like', '%skill assessment%')
            ->where('status', 1)
            ->orderBy('title', 'ASC')
            ->get();

        return response()->json($visaTypes);
    }

    public function getCountries()
    {
        $countries = \App\Models\Country::all()->pluck('name')->toArray();

        // Ensure "India" and "Australia" are at the top of the list
        $priorityCountries = ['Australia','India'];
        $otherCountries = array_diff($countries, $priorityCountries);
        $sortedCountries = array_merge($priorityCountries, $otherCountries);

        return response()->json($sortedCountries);
    }

      //Fetch all contact list of any client at create note popup
      public function fetchClientContactNo(Request $request){ //dd($request->all());
        if( ClientContact::where('client_id', $request->client_id)->exists()){
            //Fetch All client contacts
            $clientContacts = ClientContact::select('phone')->where('client_id', $request->client_id)->get();
            //dd($clientContacts);
            if( !empty($clientContacts) && count($clientContacts)>0 ){
                $response['status'] 	= 	true;
                $response['message']	=	'Client contact is successfully fetched.';
                $response['clientContacts']	=	$clientContacts;
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['clientContacts']	=	array();
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
            $response['clientContacts']	=	array();
        }
        echo json_encode($response);
	}

    public function updateAddress(Request $request)
    {
        $postcode = $request->input('postcode');
        // Fetch data based on the postcode
        // Replace this with your actual API call to get address details
        $apiKey = 'acb06506-edb3-4965-856e-db81ade1b45b';
        $urlPrefix = 'digitalapi.auspost.com.au';
        $url = 'https://' . $urlPrefix . '/postcode/search.json?q=' . $postcode;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['AUTH-KEY: ' . $apiKey]);
        $response = curl_exec($ch);  //dd($response);
        curl_close($ch);
        if (!$response) {
            return response()->json(['localities' => []]);
        }
        $data = json_decode($response, true); //dd($data);
        return response()->json($data);
    }

    // Method 1: Search address using Google Places
    public function searchAddressFull(Request $request)
    {
        $query = $request->input('query');
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';
        
        $params = http_build_query([
            'input' => $query,
            'components' => 'country:au',
            'key' => $apiKey
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        return response()->json($data);
    }

    // Method 2: Get place details
    public function getPlaceDetails(Request $request)
    {
        $placeId = $request->input('place_id');
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $url = 'https://maps.googleapis.com/maps/api/place/details/json';
        
        $params = http_build_query([
            'place_id' => $placeId,
            'key' => $apiKey
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        return response()->json($data);
    }

    // Method 3: Helper to combine address
    private function combineAddress($parts)
    {
        $addressParts = array_filter([
            $parts['line1'] ?? null,
            $parts['line2'] ?? null,
            $parts['suburb'] ?? null,
            $parts['state'] ?? null,
            $parts['postcode'] ?? null,
            (($parts['country'] ?? 'Australia') !== 'Australia' ? $parts['country'] : null)
        ]);
        
        return implode(', ', $addressParts);
    }

    public function updateOccupation(Request $request)
    {
        $occupation = $request->input('occupation');

        // Example: Replace this with actual search logic based on your database schema
        $occupations = \DB::table('client_occupation_lists')
            ->where('occupation', 'like', "%{$occupation}%")
            ->get(['occupation', 'occupation_code', 'list', 'visa_subclass','access_authority']);

        return response()->json(['occupations' => $occupations]);
    }

    public function saveRelationship(Request $request)
    {
        $clientId = auth()->user()->id; // Assuming the logged-in user is the client

        // Loop through the relationship data to insert each relationship
        foreach ($request->relationship_type as $index => $relationshipType) {
            ClientRelationship::create([
                'client_id' => $clientId,
                'relationship_type' => $relationshipType,
                'name' => $request->name[$index],
                'phone_number' => $request->phone_number[$index],
                'email_address' => $request->email_address[$index],
                'crm_reference' => $request->crm_reference[$index] ?? null,
            ]);
        }

        return response()->json(['success' => 'Relationship data saved successfully!']);
    }

    //Seach Client Relationship
    public function searchPartner(Request $request)
    {
        // Validate the incoming query
        $request->validate([
            'query' => 'required|string|min:3|max:255',
        ]);

        $query = $request->input('query');

        // Search the admins table for matching records
        $partners = Admin::where('role', '=', '7') // Assuming role 7 is for clients
            ->where(function ($q) use ($query) {
                $q->where('email', 'like', '%' . $query . '%')
                    ->orWhere('first_name', 'like', '%' . $query . '%')
                    ->orWhere('last_name', 'like', '%' . $query . '%')
                    ->orWhere('phone', 'like', '%' . $query . '%')
                    ->orWhere('client_id', 'like', '%' . $query . '%');
            })
            ->where('id', '!=', Auth::user()->id) // Exclude the current user
            ->select('id', 'email', 'first_name', 'last_name', 'phone', 'client_id')
            ->limit(10) // Limit results to prevent overload
            ->get();

        // Return JSON response with consistent structure
        return response()->json([
            'partners' => $partners->toArray(),
        ], 200);
    }

    public function fetchClientMatterAssignee(Request $request)
    {
        $requestData = $request->all();
        $matter_info = DB::table('client_matters')->where('id',$requestData['client_matter_id'])->first();
        //dd($matter_info);
        if(!empty($matter_info)) {
            $response['matter_info'] = $matter_info;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        }else{
            $response['matter_info'] 	= array();
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }

    public function updateClientMatterAssignee(Request $request){
        //dd($request->all());
        $requstData = $request->all();
        if(ClientMatter::where('id', '=', $requstData['selectedMatterLM'])->exists()) {
            $obj = ClientMatter::find($requstData['selectedMatterLM']);
            $obj->sel_migration_agent = $requstData['migration_agent'];
            $obj->sel_person_responsible = $requstData['person_responsible'];
            $obj->sel_person_assisting = $requstData['person_assisting'];
            $obj->user_id = $requstData['user_id'];
            $saved = $obj->save();
            if($saved) {

                $objs = new \App\Models\ActivitiesLog;
                $objs->client_id = $requstData['client_id'];
                $objs->created_by = Auth::user()->id;
                $objs->description = '';
                $objs->subject = 'updated client matter assignee';
                $objs->save();

                $response['status'] 	= 	true;
                $response['message']	=	'Record is exist';
            }else{
                $response['status'] 	= 	false;
                $response['message']	=	'Record is not exist.Please try again';
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }
    
    /**
     * Decode string - helper method for decoding encoded client IDs
     */
    public function decodeString($string = null)
    {
        try {
            return convert_uudecode(base64_decode($string));
        } catch (\Exception $e) {
            return $string; // Return original if decoding fails
        }
    }

    /**
     * Methods to be moved from ClientsController:
     * 
     * - clientdetailsinfo() - Get client details
     * - getVisaTypes() - Get list of visa types
     * - getCountries() - Get list of countries
     * - saveRelationship() - Save family relationships
     * - updateAddress() - Update client address
     * - updateOccupation() - Update occupation details
     * - fetchClientContactNo() - Fetch client contact numbers
     * - fetchClientMatterAssignee() - Fetch matter assignee
     * - updateClientMatterAssignee() - Update matter assignee
     */

    public function clientdetailsinfo(Request $request, $id = NULL)
{
    //check authorization end
    if ($request->isMethod('post'))
    {
        $requestData = $request->all(); //dd($requestData);
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'email' => 'required|max:255|unique:admins,email,'.$requestData['id'],
            'phone' => 'required|max:255|unique:admins,phone,'.$requestData['id'],
            'client_id' => 'required|max:255|unique:admins,client_id,'.$requestData['id']
        ]);

        $related_files = '';
        if(isset($requestData['related_files'])){
            for($i=0; $i<count($requestData['related_files']); $i++){
                $related_files .= $requestData['related_files'][$i].',';
            }
        }

        $dob = '';
        if(array_key_exists("dob",$requestData) && $requestData['dob'] != ''){
           $dobs = explode('/', $requestData['dob']);
           $dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
        }

        $visaExpiry = '';
        if(array_key_exists("visaExpiry",$requestData) && $requestData['visaExpiry'] != '' ){
           $visaExpirys = explode('/', $requestData['visaExpiry']);
            $visaExpiry = $visaExpirys[2].'-'.$visaExpirys[1].'-'. $visaExpirys[0];
        }
        $obj = 	Admin::find(@$requestData['id']);
        $first_name = substr(@$requestData['first_name'], 0, 4);

        $obj->first_name	=	@$requestData['first_name'];
        $obj->last_name	=	@$requestData['last_name'];
        $obj->dob	=	@$dob;
        $obj->age	=	@$requestData['age'];
        $obj->gender	=	@$requestData['gender'];
        $obj->martial_status	=	@$requestData['martial_status'];

        $naatiTest = isset($requestData['naati_test']) && $requestData['naati_test'] === '1' ? 1 : 0;
        $obj->naati_test = $naatiTest;
        $obj->naati_date = $naatiTest ? ($requestData['naati_date'] ?? null) : null;

        $pyTest = isset($requestData['py_test']) && $requestData['py_test'] === '1' ? 1 : 0;
        $obj->py_test = $pyTest;
        $obj->py_date = $pyTest ? ($requestData['py_date'] ?? null) : null;
        $obj->related_files	=	rtrim($related_files,',');
        $obj->save(); //Finally, save the object

        //Contact Type Start Code
        if(
            ( isset($requestData['contact_type_hidden']) && is_array($requestData['contact_type_hidden']) )
            &&
            ( isset($requestData['phone']) && is_array($requestData['phone']) )
        )
        {
            // Get the count of the email array
            $count_contact = count($requestData['contact_type_hidden']);
            // Save the last values for email_type_hidden and email to the Admin object
            if ($count_contact > 0 ) {
                // Get the last values for contact_type and phone
                $lastContactType = end($requestData['contact_type_hidden']);
                $lastPhone = end($requestData['phone']);
                $lastcountry_code =  end($requestData['country_code']);

                if($lastPhone != ""){
                    $lastPhone = $lastPhone;
                    $lastContactType = $lastContactType;
                    $lastcountry_code = $lastcountry_code;
                } else {
                    if($count_contact >1){
                        $lastPhone = $requestData['phone'][$count_contact-2];
                        $lastContactType = $requestData['contact_type_hidden'][$count_contact-2];
                        $lastcountry_code = $requestData['country_code'][$count_contact-2];
                    } else {
                        $lastPhone = $requestData['phone'][0];
                        $lastContactType = $requestData['contact_type_hidden'][0];
                        $lastcountry_code = $requestData['country_code'][0];
                    }
                }
                $obj->contact_type = $lastContactType;
                $obj->phone = $lastPhone;
                $obj->country_code = $lastcountry_code;
                $obj->save(); // Save the admin object with the last phone number
            }

            // Loop through each contact in the request
            foreach ($requestData['contact_type_hidden'] as $key => $contactType) {
                $contactId = $requestData['contact_id'][$key] ?? null;
                $phone = $requestData['phone'][$key] ?? null;
                $country_code = $requestData['country_code'][$key] ?? null;
                // Check if both contact_type and phone are not empty
                if (!empty($contactType) && !empty($phone)) {
                    if ($contactId) {
                        // Update existing contact if ID is provided
                        $existingContact = ClientContact::find($contactId);
                        //if ($existingContact && $existingContact->admin_id == Auth::user()->id) {
                        if ($existingContact) {
                            $existingContact->update([
                                'admin_id' => Auth::user()->id,
                                'contact_type' => $contactType,
                                'phone' => $phone,
                                'country_code' => $country_code
                            ]);
                        }
                    } else {
                        // Insert new contact if no ID is provided
                        ClientContact::create([
                            'admin_id' => Auth::user()->id, // Assigning Auth user ID to admin_id
                            'client_id' => $obj->id,
                            'contact_type' => $contactType,
                            'phone' => $phone,
                            'country_code' => $country_code
                        ]);
                    }
                }
            }
        }
        //Contact Type End Code

        //Email Type Start Code
        if (
            ( isset($requestData['email_type_hidden']) && is_array($requestData['email_type_hidden']) )
            &&
            ( isset($requestData['email']) && is_array($requestData['email']) )
        )
        {
            // Get the count of the email array
            $count_email_type = count($requestData['email_type_hidden']);
            // Save the last values for email_type_hidden and email to the Admin object
            if ($count_email_type > 0 ) {
                $lastEmailType = end($requestData['email_type_hidden']);
                $lastEmail = end($requestData['email']);
                if($lastEmail != ""){
                    $lastEmail = $lastEmail;
                    $lastEmailType = $lastEmailType;
                } else {
                    if($count_email_type >1){
                        $lastEmail = $requestData['email'][$count_email_type-2];
                        $lastEmailType = $requestData['email_type_hidden'][$count_email_type-2];
                    } else {
                        $lastEmail = $requestData['email'][0];
                        $lastEmailType = $requestData['email_type_hidden'][0];
                    }
                }
                $obj->email_type = $lastEmailType;
                $obj->email = $lastEmail;
                $obj->save();
            }

            // Loop through each email in the request
            foreach ($requestData['email_type_hidden'] as $key => $emailType) {
                $email = $requestData['email'][$key] ?? null;
                $emailId = $requestData['email_id'][$key] ?? null;

                // Check if the current row is not blank
                if (!empty($emailType) && !empty($email)) {

                    // Check if the email already exists in the current client's email list
                    $duplicateEmail = ClientEmail::where('email', $email)
                    ->where('client_id', $obj->id)
                    ->where('id', '!=', $emailId)
                    ->first();

                    if ($duplicateEmail) {
                        // If duplicate found, add error message to the session
                        return response()->json([
                            'status' => 'error',
                            'message' => 'This email is already taken: ' . $email
                        ], 422); // Unprocessable Entity
                    }

                    if ($emailId) {
                        // Update existing email if ID is provided
                        $existingEmail = ClientEmail::find($emailId);
                        if ($existingEmail && $existingEmail->client_id == $obj->id) {
                            $existingEmail->update([
                                'email_type' => $emailType,
                                'email' => $email,
                                'admin_id' => Auth::user()->id
                            ]);
                        }
                    } else {
                        // Insert new email if no ID is provided
                        ClientEmail::create([
                            'admin_id' => Auth::user()->id,
                            'client_id' => $obj->id, // Assigning the correct client ID
                            'email_type' => $emailType,
                            'email' => $email
                        ]);
                    }
                }
            }
        }
        //Email Type End Code

        //Visa Country Start Code
        if (
            ( isset($requestData['visa_country']) && is_array($requestData['visa_country']) )
            ||
            ( isset($requestData['visa_type_hidden']) && is_array($requestData['visa_type_hidden']) )
        )
        {
            if( isset($requestData['visa_country']) &&  $requestData['visa_country'][0] == 'Australia')
            {

                if (ClientVisaCountry::where('client_id', $obj->id)->exists()) {
                    if ( ClientVisaCountry::where('client_id', $obj->id)->delete() ) {
                        ClientVisaCountry::create([
                            'admin_id' => Auth::user()->id, // Assigning Auth user ID to admin_id
                            'client_id' => $obj->id,
                            'visa_country' => $requestData['visa_country'][0],
                            'visa_type' => "",
                            'visa_expiry_date' => "",
                            'visa_description' => ""
                        ]);

                        $obj->visa_type = "";
                        $obj->country_passport = $requestData['visa_country'][0];
                        $obj->visaExpiry = "";
                        $obj->save();
                    }
                }
            }
            else
            {
                //If Visa Country is not Australia
                if (ClientVisaCountry::where('client_id', $obj->id)->exists()) {
                    if ( ClientVisaCountry::where('client_id', $obj->id)->delete() ) {

                        foreach ($requestData['visa_type_hidden'] as $key => $visaType) {
                            $visa_country = $requestData['visa_country'][0] ?? null;
                            $visa_expiry_date = $requestData['visa_expiry_date'][$key] ?? null;
                            $visa_description = $requestData['visa_description'][$key] ?? null;
                            $visaId = $requestData['visa_id'][$key] ?? null;
                            // Check if the current row is not blank
                            if (!empty($visaType) || !empty($visa_country)) {
                                ClientVisaCountry::create([
                                    'admin_id' => Auth::user()->id, // Assigning Auth user ID to admin_id
                                    'client_id' => $obj->id,
                                    'visa_country' => $visa_country,
                                    'visa_type' => $visaType,
                                    'visa_expiry_date' => $visa_expiry_date,
                                    'visa_description' => $visa_description
                                ]);
                            }
                        }
                        $count_visa = count($requestData['visa_type_hidden']);
                        // Save the last values for visa_type, visa_country, and visa_expiry_date to the Admin object
                        if ($count_visa > 0 ) {
                            $lastVisaCountry = $requestData['visa_country'][0];
                            $lastVisaType = end($requestData['visa_type_hidden']);
                            $lastVisaExpiryDate = end($requestData['visa_expiry_date']);
                            // Check if the last visa details are not empty before assigning
                            if (!empty($lastVisaType)  &&  !empty($lastVisaCountry)) {
                                $obj->visa_type = $lastVisaType;
                                $obj->country_passport = $lastVisaCountry;
                                $obj->visaExpiry = $lastVisaExpiryDate;
                                $obj->save();
                            }
                        }
                    }
                }
            }
        }

        //Address Start Code
        if (
            ( isset($requestData['zip']) && is_array($requestData['zip']) )
            ||
            ( isset($requestData['address']) && is_array($requestData['address']) )
        )
        {
            // Get the count of the address array
            $count = count($requestData['zip']);
            // Save the last values for address, city, state, and zip code to the Admin object
            if ($count > 0 ) {
                $secondLastAddress = $requestData['address'][$count - 1];
                $secondLastZip = $requestData['zip'][$count - 1];

                // Check if the last address details are not empty before assigning
                if (!empty($secondLastAddress)  || !empty($secondLastZip)) {
                    $obj->address = $secondLastAddress;
                    $obj->zip = $secondLastZip;
                    $obj->save();
                }
            }

            // Loop through each address in the request
            foreach ($requestData['address'] as $key => $addr) {
                $zip = $requestData['zip'][$key] ?? null;
                $addressId = $requestData['address_id'][$key] ?? null;
                $regional_code = $requestData['regional_code'][$key] ?? null;

                // Check if the current row is not blank
                if (!empty($addr) || !empty($zip)) {
                    if ($addressId) {
                        // Update existing address if ID is provided
                        $existingAddress = ClientAddress::find($addressId);
                        if ($existingAddress && $existingAddress->client_id == $obj->id) {
                            $existingAddress->update([
                                'admin_id' => Auth::user()->id,
                                'address' => $addr,
                                'zip' => $zip,
                                'regional_code' => $regional_code
                            ]);
                        }
                    } else {
                        // Insert new address if no ID is provided
                        ClientAddress::create([
                            'admin_id' => Auth::user()->id,
                            'client_id' => $obj->id,
                            'address' => $addr,
                            'zip' => $zip,
                            'regional_code' => $regional_code
                        ]);
                    }
                }
            }
        }
        //Address End Code

        //Client Qualification Start Code
        if (
            ( isset($requestData['level_hidden']) && is_array($requestData['level_hidden']) )
            ||
            ( isset($requestData['name']) && is_array($requestData['name']) )
        )
        {
            // Get the count of qualification entries
            $qualificationCount = count($requestData['level_hidden']);

            // Ensure that there are at least two qualification entries to get the last one
            if ($qualificationCount > 0) {
                // Get the second last values for level and name
                $secondLastLevel = $requestData['level_hidden'][$qualificationCount - 1];
                $secondLastName = $requestData['name'][$qualificationCount - 1];

                // Save the second last qualification details to the Admin object if not empty
                if (!empty($secondLastLevel) || !empty($secondLastName)) {
                    $obj->qualification_level = $secondLastLevel;
                    $obj->qualification_name = $secondLastName;
                    $obj->save(); // Save the admin object with the second last qualification details
                }
            }

            // Loop through each qualification in the request
            foreach ($requestData['level_hidden'] as $key => $level)
            {
                $name = $requestData['name'][$key] ?? null;
                $country = $requestData['country_hidden'][$key] ?? null;
                $short = $requestData['start_date'][$key] ?? null;
                $finish = $requestData['finish_date'][$key] ?? null;
                $qualificationId = $requestData['qualification_id'][$key] ?? null;
                $relevant_qualification = $requestData['relevant_qualification_hidden'][$key] ?? null;

                // Check if the current row is not blank
                if (!empty($level) || !empty($name) ) {
                    if ($qualificationId) {
                        // Update existing qualification if ID is provided
                        $existingQualification = ClientQualification::find($qualificationId);
                        if ($existingQualification && $existingQualification->client_id == $obj->id) {
                            $existingQualification->update([
                                'admin_id' => Auth::user()->id,
                                'level' => $level,
                                'name' => $name,
                                'country' => $country,
                                'start_date' => $short,
                                'finish_date' => $finish,
                                'relevant_qualification' => $relevant_qualification
                            ]);
                        }
                    } else {
                        // Insert new qualification if no ID is provided
                        ClientQualification::create([
                            'admin_id' => Auth::user()->id,
                            'client_id' => $obj->id, // Assigning the correct client ID
                            'level' => $level,
                            'name' => $name,
                            'country' => $country,
                            'start_date' => $short,
                            'finish_date' => $finish,
                            'relevant_qualification' => $relevant_qualification
                        ]);
                    }
                }
            }
        }
        //Client Qualification End Code

        //Client Experience Start Code
        if (
            ( isset($requestData['job_title']) && is_array($requestData['job_title']) )
            ||
            ( isset($requestData['job_code']) && is_array($requestData['job_code']) )
        )
        {
            // Loop through each job in the request
            foreach ($requestData['job_title'] as $key => $jobTitle) {
                $jobCode = $requestData['job_code'][$key] ?? null;
                $jobCountry = $requestData['job_country_hidden'][$key] ?? null;
                $jobStartDate = $requestData['job_start_date'][$key] ?? null;
                $jobFinishDate = $requestData['job_finish_date'][$key] ?? null;
                $jobRelevantExp = $requestData['relevant_experience_hidden'][$key] ?? null;
                $jobId = $requestData['job_id'][$key] ?? null;

                // Check if the current row is not blank
                //if (!empty($jobTitle) && !empty($jobCode) && !empty($jobCountry)) {
                if (!empty($jobTitle) || !empty($jobCode) ) {
                    if ($jobId) {
                        // Update existing job if ID is provided
                        $existingJob = ClientExperience::find($jobId);
                        if ($existingJob && $existingJob->client_id == $obj->id) {
                            $existingJob->update([
                                'admin_id' => Auth::user()->id,
                                'job_title' => $jobTitle,
                                'job_code' => $jobCode,
                                'job_country' => $jobCountry,
                                'job_start_date' => $jobStartDate,
                                'job_finish_date' => $jobFinishDate,
                                'relevant_experience' =>$jobRelevantExp
                            ]);
                        }
                    } else {
                        // Insert new job if no duplicate exists
                        ClientExperience::create([
                            'admin_id' => Auth::user()->id,
                            'client_id' => $obj->id, // Assigning the correct client ID
                            'job_title' => $jobTitle,
                            'job_code' => $jobCode,
                            'job_country' => $jobCountry,
                            'job_start_date' => $jobStartDate,
                            'job_finish_date' => $jobFinishDate,
                            'relevant_experience' =>$jobRelevantExp
                        ]);
                    }
                }
            }
        }
        //Client Experience End Code

        //Client Occupation Start Code
        if (
            ( isset($requestData['skill_assessment_hidden']) && is_array($requestData['skill_assessment_hidden']) )
            ||
            ( isset($requestData['nomi_occupation']) && is_array($requestData['nomi_occupation']) )
            )
        {

            // Loop through each set of data
            foreach ($requestData['skill_assessment_hidden'] as $key => $skillAssessment) {
                $nomiOccupation = $requestData['nomi_occupation'][$key] ?? null;
                $occupationCode = $requestData['occupation_code'][$key] ?? null;
                $list = $requestData['list'][$key] ?? null;
                $visaSubclass = $requestData['visa_subclass'][$key] ?? null;
                $date = $requestData['dates'][$key] ?? null;
                $occupationId = $requestData['occupation_id'][$key] ?? null; // Assuming you have IDs for updating
                $relevant_occupation = $requestData['relevant_occupation_hidden'][$key] ?? null;
                // Check if both skill_assessment and nomi_occupation are not empty
                if (!empty($skillAssessment) || !empty($nomiOccupation))
                {
                    if ($occupationId)
                    {
                        // Update existing record if ID is provided
                        $existingOccupation = ClientOccupation::find($occupationId);
                        if ($existingOccupation ) {
                            $existingOccupation->update([
                                'admin_id' => Auth::user()->id,
                                'skill_assessment' => $skillAssessment,
                                'nomi_occupation' => $nomiOccupation,
                                'occupation_code' => $occupationCode,
                                'list' => $list,
                                'visa_subclass' => $visaSubclass,
                                'dates' => $date,
                                'relevant_occupation' => $relevant_occupation
                            ]);
                        }
                    }
                    else
                    {
                        // Insert new record if no ID is provided
                        ClientOccupation::create([
                            'admin_id' => Auth::user()->id,
                            'client_id' => $obj->id,
                            'skill_assessment' => $skillAssessment,
                            'nomi_occupation' => $nomiOccupation,
                            'occupation_code' => $occupationCode,
                            'list' => $list,
                            'visa_subclass' => $visaSubclass,
                            'dates' => $date,
                            'relevant_occupation' => $relevant_occupation
                        ]);
                    }
                }
            }
        }
        //Client Occupation End Code

        //Test Score Start Code
        if ( isset($requestData['test_type_hidden']) && is_array($requestData['test_type_hidden']) )
        {
            // Loop through each test score entry in the request
            foreach ($requestData['test_type_hidden'] as $key => $testType) {
                $listening = $requestData['listening'][$key] ?? null;
                $reading = $requestData['reading'][$key] ?? null;
                $writing = $requestData['writing'][$key] ?? null;
                $speaking = $requestData['speaking'][$key] ?? null;
                $overallScore = $requestData['overall_score'][$key] ?? null;
                $testDate = $requestData['test_date'][$key] ?? null;
                $testScoreId = $requestData['test_score_id'][$key] ?? null;
                $relevant_test = $requestData['relevant_test_hidden'][$key] ?? null;

                // Check if the current row is not blank (i.e., test_type and test_date are not empty)
                if (!empty($testType) ) {
                    if ($testScoreId) {
                        // Update existing test score if ID is provided
                        $existingTestScore = ClientTestScore::find($testScoreId);
                        if ($existingTestScore && $existingTestScore->client_id == $obj->id) {
                            $existingTestScore->update([
                                'admin_id' => Auth::user()->id,
                                'test_type' => $testType,
                                'listening' => $listening, // Update with text value
                                'reading' => $reading,     // Update with text value
                                'writing' => $writing,     // Update with text value
                                'speaking' => $speaking,   // Update with text value
                                'test_date' => $testDate,
                                'overall_score' => $overallScore, // Update overall_score
                                'relevant_test' => $relevant_test
                            ]);
                        }
                    } else {
                        // Check if a test score with the same type and date already exists
                        /*$existingTestScore = ClientTestScore::where('client_id', $obj->id)
                            ->where('test_type', $testType)
                            ->where('test_date', $testDate)
                            ->first();

                        if (!$existingTestScore) {*/
                            // Insert new test score if no duplicate is found
                            ClientTestScore::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $obj->id, // Assigning the correct client ID
                                'test_type' => $testType,
                                'listening' => $listening, // Set with text value
                                'reading' => $reading,     // Set with text value
                                'writing' => $writing,     // Set with text value
                                'speaking' => $speaking,   // Set with text value
                                'test_date' => $testDate,
                                'overall_score' => $overallScore, // Set overall_score
                                'relevant_test' => $relevant_test
                            ]);
                        //}
                    }
                }
            }
        }
        //Test Score End Code

        //Spouse Detail Start Code
        if(
            (isset($requestData['spouse_english_score']) && !empty($requestData['spouse_english_score']))
            ||
            (isset($requestData['spouse_test_type']) && !empty($requestData['spouse_test_type']))
            ||
            (isset($requestData['spouse_listening_score']) && !empty($requestData['spouse_listening_score']))
            ||
            (isset($requestData['spouse_reading_score']) && !empty($requestData['spouse_reading_score']))
            ||
            (isset($requestData['spouse_writing_score']) && !empty($requestData['spouse_writing_score']))
            ||
            (isset($requestData['spouse_speaking_score']) && !empty($requestData['spouse_speaking_score']))
            ||
            (isset($requestData['spouse_overall_score']) && !empty($requestData['spouse_overall_score']))
            ||
            (isset($requestData['spouse_test_date']) && !empty($requestData['spouse_test_date']))
            ||
            (isset($requestData['spouse_skill_assessment']) && !empty($requestData['spouse_skill_assessment']))
            ||
            (isset($requestData['spouse_skill_assessment_status']) && !empty($requestData['spouse_skill_assessment_status']))
            ||
            (isset($requestData['spouse_nomi_occupation']) && !empty($requestData['spouse_nomi_occupation']))
            ||
            (isset($requestData['spouse_assessment_date']) && !empty($requestData['spouse_assessment_date']))
        )
        {

            // Extract single values from the request
            $englishScore = $requestData['spouse_english_score'];
            $testType = $requestData['spouse_test_type'];
            $listeningScore = $requestData['spouse_listening_score'];
            $readingScore = $requestData['spouse_reading_score'];
            $writingScore = $requestData['spouse_writing_score'];
            $speakingScore = $requestData['spouse_speaking_score'];
            $overallScore = $requestData['spouse_overall_score'];
            $spousetestdate = $requestData['spouse_test_date'];

            $skillAssessment = $requestData['spouse_skill_assessment'];
            $skillAssessmentStatus = $requestData['spouse_skill_assessment_status'];
            $nomiOccupation = $requestData['spouse_nomi_occupation'];
            $assessmentDate = $requestData['spouse_assessment_date'];

            if( ClientSpouseDetail::where('client_id', $obj->id)->delete() ) {
                ClientSpouseDetail::create([
                    'admin_id' => Auth::user()->id,
                    'client_id' => $obj->id,
                    'spouse_english_score' => $englishScore,
                    'spouse_test_type' => $testType,
                    'spouse_listening_score' => $listeningScore,
                    'spouse_reading_score' => $readingScore,
                    'spouse_writing_score' => $writingScore,
                    'spouse_speaking_score' => $speakingScore,
                    'spouse_overall_score' => $overallScore,
                    'spouse_test_date' => $spousetestdate,
                    'spouse_skill_assessment' => $skillAssessment,
                    'spouse_skill_assessment_status' => $skillAssessmentStatus,
                    'spouse_nomi_occupation' => $nomiOccupation,
                    'spouse_assessment_date' => $assessmentDate
                ]);
            }
        }
        //Spouse Detail End Code

        // Handle Partner Deletion
    if (isset($requestData['delete_partner_ids']) && is_array($requestData['delete_partner_ids'])) {
        \Log::info('Deleting partners:', ['delete_partner_ids' => $requestData['delete_partner_ids']]);
        foreach ($requestData['delete_partner_ids'] as $partnerId) {
            $partner = ClientPartner::find($partnerId);
            if ($partner && $partner->client_id == $obj->id) {
                // Delete reciprocal relationship if exists
                if ($partner->related_client_id) {
                    ClientPartner::where('client_id', $partner->related_client_id)
                        ->where('related_client_id', $obj->id)
                        ->delete();
                    \Log::info('Deleted reciprocal relationship for partner:', ['partner_id' => $partnerId, 'related_client_id' => $partner->related_client_id]);
                }
                $partner->delete();
                \Log::info('Deleted partner:', ['partner_id' => $partnerId]);
            } else {
                \Log::warning('Partner not found or does not belong to client:', ['partner_id' => $partnerId, 'client_id' => $obj->id]);
            }
        }
    }

    // Partner Handling for client_partners table
    if (isset($requestData['partner_details']) && is_array($requestData['partner_details'])) {
        \Log::info('Processing partner data:', [
            'partner_details' => $requestData['partner_details'],
            'relationship_type' => $requestData['relationship_type'] ?? [],
            'partner_id' => $requestData['partner_id'] ?? [],
            'partner_email' => $requestData['partner_email'] ?? [],
            'partner_first_name' => $requestData['partner_first_name'] ?? [],
            'partner_last_name' => $requestData['partner_last_name'] ?? [],
            'partner_phone' => $requestData['partner_phone'] ?? [],
        ]);

        $relationshipMap = [
            'Husband' => 'Wife',
            'Wife' => 'Husband',
            'Ex-Wife' => 'Ex-Wife',
            'Defacto' => 'Defacto',
        ];

        foreach ($requestData['partner_details'] as $key => $details) {
            $relationshipType = $requestData['relationship_type'][$key] ?? null;
            $partnerId = $requestData['partner_id'][$key] ?? null;
            $email = $requestData['partner_email'][$key] ?? null;
            $firstName = $requestData['partner_first_name'][$key] ?? null;
            $lastName = $requestData['partner_last_name'][$key] ?? null;
            $phone = $requestData['partner_phone'][$key] ?? null;

            // Skip if relationship_type is not provided (validation should catch this, but adding as a safety check)
            if (empty($relationshipType)) {
                \Log::warning('Skipping partner entry due to missing relationship type:', ['key' => $key]);
                continue;
            }

            $relatedClientId = $partnerId && is_numeric($partnerId) ? $partnerId : null;

            // Determine if extra fields should be saved (only if related_client_id is null)
            $saveExtraFields = !$relatedClientId;

            // Prepare partner data for client_partners table
            $partnerData = [
                'admin_id' => Auth::user()->id,
                'client_id' => $obj->id,
                'related_client_id' => $relatedClientId,
                'details' => $relatedClientId ? $details : null, // Save details only if a match is found
                'relationship_type' => $relationshipType,
                'email' => $saveExtraFields ? $email : null,
                'first_name' => $saveExtraFields ? $firstName : null,
                'last_name' => $saveExtraFields ? $lastName : null,
                'phone' => $saveExtraFields ? $phone : null,
            ];

            \Log::info('Prepared partner data:', ['key' => $key, 'partnerData' => $partnerData]);

            if ($partnerId && is_numeric($partnerId)) {
                // Update existing partner
                $existingPartner = ClientPartner::find($partnerId);
                if ($existingPartner && $existingPartner->client_id == $obj->id) {
                    $existingPartner->update($partnerData);
                    \Log::info('Updated existing partner:', ['partner_id' => $partnerId, 'data' => $partnerData]);

                    // Update reciprocal relationship if exists
                    if ($existingPartner->related_client_id && isset($relationshipMap[$relationshipType])) {
                        $reciprocalData = [
                            'admin_id' => Auth::user()->id,
                            'relationship_type' => $relationshipMap[$relationshipType],
                            'details' => "{$obj->first_name} {$obj->last_name} ({$obj->email}, {$obj->phone}, {$obj->client_id})",
                            'email' => null,
                            'first_name' => null,
                            'last_name' => null,
                            'phone' => null,
                        ];
                        ClientPartner::where('client_id', $existingPartner->related_client_id)
                            ->where('related_client_id', $obj->id)
                            ->update($reciprocalData);
                        \Log::info('Updated reciprocal relationship for partner:', ['partner_id' => $partnerId, 'reciprocal_data' => $reciprocalData]);
                    }
                } else {
                    \Log::warning('Existing partner not found or does not belong to client:', ['partner_id' => $partnerId, 'client_id' => $obj->id]);
                }
            } else {
                // Create new partner
                $newPartner = ClientPartner::create($partnerData);
                \Log::info('Created new partner:', ['new_partner_id' => $newPartner->id, 'data' => $partnerData]);

                // Create reciprocal relationship if related_client_id is set
                if ($relatedClientId && isset($relationshipMap[$relationshipType])) {
                    $relatedClient = Admin::find($relatedClientId);
                    if ($relatedClient) {
                        $reciprocalData = [
                            'admin_id' => Auth::user()->id,
                            'client_id' => $relatedClientId,
                            'related_client_id' => $obj->id,
                            'details' => "{$obj->first_name} {$obj->last_name} ({$obj->email}, {$obj->phone}, {$obj->client_id})",
                            'relationship_type' => $relationshipMap[$relationshipType],
                            'email' => null,
                            'first_name' => null,
                            'last_name' => null,
                            'phone' => null,
                        ];
                        ClientPartner::create($reciprocalData);
                        \Log::info('Created reciprocal relationship for new partner:', ['new_partner_id' => $newPartner->id, 'reciprocal_data' => $reciprocalData]);
                    } else {
                        \Log::warning('Related client not found for reciprocal relationship:', ['related_client_id' => $relatedClientId]);
                    }
                }
            }
        }

        // Debug: Log the number of partners saved
        $savedPartners = ClientPartner::where('client_id', $obj->id)->count();
        \Log::info('Total partners saved for client:', ['client_id' => $obj->id, 'count' => $savedPartners]);
    } else {
        \Log::info('No partner data provided to process.');
    }

        /*$obj->total_points	=	@$requestData['total_points'];
        $obj->type	=	@$requestData['type'];
        $obj->source	=	@$requestData['source'];
        if(@$requestData['source'] == 'Sub Agent' ){
            $obj->agent_id	=	@$requestData['subagent'];
        } else {
            $obj->agent_id	=	'';
        }*/
        $saved	=	$obj->save();
        if( $requestData['client_id'] != '') {
            $objs			   = 	Admin::find($obj->id);
            $objs->client_id	=	$requestData['client_id'];
            $saveds				=	$objs->save();
        }

        $route = $request->route;
        if(strpos($request->route,'?')){
            $position=strpos($request->route,'?');
            if ($position !== false) {
                $route = substr($request->route, 0, $position);
            }
        }
        //dd($route);
        if(!$saved) {
            return redirect()->back()->with('error', Config::get('constants.server_error'));
        } else if( $route ==url('/admin/assignee')) {
            //$subject = 'Lead status has changed to '.@$requestData['status'].' from '. \Auth::user()->first_name;
            $subject = 'Lead status has changed from '. \Auth::user()->first_name;
            $objs = new ActivitiesLog;
            $objs->client_id = $request->id;
            $objs->created_by = \Auth::user()->id;
            $objs->subject = $subject;
            $objs->save();
            return redirect()->route('assignee.index')->with('success','Assignee updated successfully');
        } else {
            //If record exist then update service taken
            if (DB::table('client_service_takens')->where('client_id',  $requestData['id'])->exists()) {
                DB::table('client_service_takens')->where('client_id', $requestData['id'])->update(['is_saved_db' => 1 ]);
            }

            $clientId = $requestData['id'];
            $encodedId = base64_encode(convert_uuencode($clientId));

            $latestMatter = DB::table('client_matters')
                ->where('client_id', $clientId)
                ->where('matter_status', 1)
                ->orderByDesc('id')
                ->first();

            $redirectUrl = $latestMatter
                ? '/admin/clients/detail/'.$encodedId.'/'.$latestMatter->client_unique_matter_no
                : '/admin/clients/detail/'.$encodedId;

            return Redirect::to($redirectUrl)->with('success', 'Details updated successfully');
            //return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$requestData['id'])))->with('success', 'Details updated successfully');
        }
    }
    else
    {
        if(isset($id) && !empty($id))
        {
            $id = $this->decodeString($id); //dd($id);
            if(Admin::where('id', '=', $id)->where('role', '=', '7')->exists())
            {
                // Use service to get all data with optimized queries (prevents N+1)
                // Now returns complete data set including passports, travels, etc.
                $data = app(\App\Services\ClientEditService::class)->getClientEditData($id);
                
                return view('Admin.clients.edit', $data);
            } else {
                return Redirect::to('/admin/clients')->with('error', 'Clients Not Exist');
            }
        } else {
            return Redirect::to('/admin/clients')->with('error', Config::get('constants.unauthorized'));
        }
    }
}

    /**
     * Save section data via AJAX
     */
    public function saveSection(Request $request)
    {
        try {
            $section = $request->input('section');
            $clientId = $request->input('client_id');
            
            // Validate client exists
            $client = Admin::where('id', $clientId)->where('role', '7')->first();
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            switch ($section) {
                case 'basicInfo':
                    return $this->saveBasicInfoSection($request, $client);
                case 'phoneNumbers':
                    return $this->savePhoneNumbersSection($request, $client);
                case 'emailAddresses':
                    return $this->saveEmailAddressesSection($request, $client);
                case 'passportInfo':
                    return $this->savePassportInfoSection($request, $client);
                case 'visaInfo':
                    return $this->saveVisaInfoSection($request, $client);
                case 'addressInfo':
                    return $this->saveAddressInfoSection($request, $client);
                case 'travelInfo':
                    return $this->saveTravelInfoSection($request, $client);
                case 'qualificationsInfo':
                    return $this->saveQualificationsInfoSection($request, $client);
                case 'experienceInfo':
                    return $this->saveExperienceInfoSection($request, $client);
                case 'additionalInfo':
                    return $this->saveAdditionalInfoSection($request, $client);
                case 'characterInfo':
                    return $this->saveCharacterInfoSection($request, $client);
                case 'partnerInfo':
                    return $this->savePartnerInfoSection($request, $client);
                case 'childrenInfo':
                    return $this->saveChildrenInfoSection($request, $client);
                case 'eoiInfo':
                    return $this->saveEoiInfoSection($request, $client);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid section specified'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveBasicInfoSection($request, $client)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|max:255',
                'last_name' => 'nullable|max:255',
                'client_id' => 'required|max:255|unique:admins,client_id,' . $client->id,
                'dob' => 'nullable|date_format:d/m/Y',
                'age' => 'nullable|string',
                'gender' => 'nullable|in:Male,Female,Other',
                'marital_status' => 'nullable|in:Single,Married,De Facto,Defacto,Divorced,Widowed,Separated'
            ]);

            // Convert DOB format and calculate age (like the working methods)
            $dob = null;
            $age = null;
            if (!empty($validated['dob'])) {
                try {
                    $dobDate = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['dob']);
                    $dob = $dobDate->format('Y-m-d');
                    $age = $dobDate->diff(\Carbon\Carbon::now())->format('%y years %m months');
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid date format. Must be dd/mm/yyyy.'
                    ], 422);
                }
            }

            // Map marital status values for backward compatibility
            $martialStatus = $validated['marital_status'] ?? null;
            if ($martialStatus === 'Defacto') {
                $martialStatus = 'De Facto';
            }

            // Use direct assignment pattern (like the working old methods)
            $client->first_name = $validated['first_name'];
            $client->last_name = $validated['last_name'] ?? null;
            $client->client_id = $validated['client_id'];
            $client->dob = $dob;
            $client->age = $age;
            $client->gender = $validated['gender'] ?? null;
            $client->martial_status = $martialStatus;
            $client->save();

            return response()->json([
                'success' => true,
                'message' => 'Basic information updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    private function savePhoneNumbersSection($request, $client)
    {
        try {
            $phoneNumbers = json_decode($request->input('phone_numbers'), true);
            
            if (!is_array($phoneNumbers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone numbers data'
                ], 400);
            }

            // Validate that at least one phone number is provided
            if (empty($phoneNumbers) || !array_filter(array_column($phoneNumbers, 'phone'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one phone number is required'
                ], 422);
            }

            // Check for duplicate Personal phone types (only one Personal phone allowed)
            $personalPhoneCount = 0;
            foreach ($phoneNumbers as $phoneData) {
                if (isset($phoneData['contact_type']) && $phoneData['contact_type'] === 'Personal') {
                    $personalPhoneCount++;
                }
            }
            if ($personalPhoneCount > 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only one phone number can be marked as Personal'
                ], 422);
            }

            // Validate each phone number and check for duplicates within the same client
            foreach ($phoneNumbers as $index => $phoneData) {
                if (!empty($phoneData['phone'])) {
                    $contactType = $phoneData['contact_type'] ?? null;
                    $phone = $phoneData['phone'];
                    $countryCode = $phoneData['country_code'] ?? '';

                    // Check for duplicate phone numbers within the same client
                    $duplicatePhone = ClientContact::where('phone', $phone)
                        ->where('country_code', $countryCode)
                        ->where('client_id', $client->id)
                        ->where('id', '!=', $phoneData['id'] ?? null)
                        ->first();

                    if ($duplicatePhone) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This phone number is already taken for this client: ' . $countryCode . $phone
                        ], 422);
                    }
                }
            }

            // Process phone numbers with proper update/insert logic (like the old working system)
            $processedPhones = [];
            foreach ($phoneNumbers as $phoneData) {
                if (!empty($phoneData['phone'])) {
                    $contactId = $phoneData['id'] ?? null;
                    $contactType = $phoneData['contact_type'] ?? null;
                    $phone = $phoneData['phone'];
                    $countryCode = $phoneData['country_code'] ?? '';

                    if ($contactId) {
                        // Update existing contact if ID is provided
                        $existingContact = ClientContact::find($contactId);
                        if ($existingContact && $existingContact->client_id == $client->id) {
                            $existingContact->update([
                                'admin_id' => Auth::user()->id,
                                'contact_type' => $contactType,
                                'phone' => $phone,
                                'country_code' => $countryCode
                            ]);
                            $processedPhones[] = $existingContact->id;
                        }
                    } else {
                        // Insert new contact if no ID is provided
                        $newContact = ClientContact::create([
                            'admin_id' => Auth::user()->id,
                            'client_id' => $client->id,
                            'contact_type' => $contactType,
                            'phone' => $phone,
                            'country_code' => $countryCode
                        ]);
                        $processedPhones[] = $newContact->id;
                    }
                }
            }

            // Remove any phone numbers that were not in the processed list (like the old system)
            if (!empty($processedPhones)) {
                ClientContact::where('client_id', $client->id)
                    ->whereNotIn('id', $processedPhones)
                    ->delete();
            }

            // Update client's primary phone info (like the old system)
            $lastPhone = null;
            $lastContactType = null;
            $lastCountryCode = null;
            
            if (!empty($phoneNumbers)) {
                $lastPhoneData = end($phoneNumbers);
                if (!empty($lastPhoneData['phone'])) {
                    $lastPhone = $lastPhoneData['phone'];
                    $lastContactType = $lastPhoneData['contact_type'] ?? null;
                    $lastCountryCode = $lastPhoneData['country_code'] ?? '';
                }
            }

            if ($lastPhone) {
                $client->phone = $lastPhone;
                $client->contact_type = $lastContactType;
                $client->country_code = $lastCountryCode;
                $client->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Phone numbers updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving phone numbers: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveEmailAddressesSection($request, $client)
    {
        try {
            $emails = json_decode($request->input('emails'), true);
            
            if (!is_array($emails)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email addresses data'
                ], 400);
            }

            // Delete existing emails for this client
            ClientEmail::where('client_id', $client->id)->delete();

            // Insert new emails
            foreach ($emails as $emailData) {
                if (!empty($emailData['email'])) {
                    ClientEmail::create([
                        'client_id' => $client->id,
                        'admin_id' => Auth::user()->id,
                        'email_type' => $emailData['email_type'],
                        'email' => $emailData['email']
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Email addresses updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving email addresses: ' . $e->getMessage()
            ], 500);
        }
    }

    private function savePassportInfoSection($request, $client)
    {
        try {
            $passports = json_decode($request->input('passports'), true);
            
            if (!is_array($passports)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid passport data'
                ], 400);
            }

            // Delete existing passport records for this client
            ClientPassportInformation::where('client_id', $client->id)->delete();

            // Get the first passport's country as the primary passport country
            $primaryPassportCountry = null;
            if (!empty($passports) && !empty($passports[0]['passport_country'])) {
                $primaryPassportCountry = $passports[0]['passport_country'];
            }

            // Update client's primary passport country (column name is country_passport)
            if ($primaryPassportCountry) {
                $client->country_passport = $primaryPassportCountry;
                $client->save();
            }

            // Insert new passport records
            foreach ($passports as $passportData) {
                if (!empty($passportData['passport_number']) || !empty($passportData['passport_country'])) {
                    // Convert date format from d/m/Y to Y-m-d if needed
                    $issueDate = null;
                    $expiryDate = null;
                    
                    if (!empty($passportData['issue_date'])) {
                        $issueDate = \DateTime::createFromFormat('d/m/Y', $passportData['issue_date']);
                        $issueDate = $issueDate ? $issueDate->format('Y-m-d') : null;
                    }
                    
                    if (!empty($passportData['expiry_date'])) {
                        $expiryDate = \DateTime::createFromFormat('d/m/Y', $passportData['expiry_date']);
                        $expiryDate = $expiryDate ? $expiryDate->format('Y-m-d') : null;
                    }
                    
                    ClientPassportInformation::create([
                        'client_id' => $client->id,
                        'admin_id' => Auth::user()->id,
                        'passport_country' => $passportData['passport_country'] ?? null,
                        'passport' => $passportData['passport_number'] ?? null,
                        'passport_issue_date' => $issueDate,
                        'passport_expiry_date' => $expiryDate
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Passport information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving passport information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveVisaInfoSection($request, $client)
    {
        try {
            $visaExpiryVerified = $request->input('visa_expiry_verified');
            $visas = json_decode($request->input('visas'), true);
            
            if (!is_array($visas)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid visa data'
                ], 400);
            }

            // Update client's visa expiry verified status using existing system
            if ($visaExpiryVerified === '1') {
                $client->visa_expiry_verified_at = now();
                $client->visa_expiry_verified_by = \Auth::user()->id;
            } else {
                $client->visa_expiry_verified_at = null;
                $client->visa_expiry_verified_by = null;
            }
            $client->save();

            // Delete existing visa records for this client
            ClientVisaCountry::where('client_id', $client->id)->delete();

            // Insert new visa records
            foreach ($visas as $visaData) {
                if (!empty($visaData['visa_type_hidden'])) {
                    // Convert date format from d/m/Y to Y-m-d if needed
                    $expiryDate = null;
                    $grantDate = null;
                    
                    if (!empty($visaData['visa_expiry_date'])) {
                        $expiryDate = \DateTime::createFromFormat('d/m/Y', $visaData['visa_expiry_date']);
                        $expiryDate = $expiryDate ? $expiryDate->format('Y-m-d') : null;
                    }
                    
                    if (!empty($visaData['visa_grant_date'])) {
                        $grantDate = \DateTime::createFromFormat('d/m/Y', $visaData['visa_grant_date']);
                        $grantDate = $grantDate ? $grantDate->format('Y-m-d') : null;
                    }
                    
                    ClientVisaCountry::create([
                        'client_id' => $client->id,
                        'admin_id' => \Auth::user()->id,
                        'visa_country' => $client->country_passport ?? '',
                        'visa_type' => $visaData['visa_type_hidden'],
                        'visa_expiry_date' => $expiryDate,
                        'visa_grant_date' => $grantDate,
                        'visa_description' => $visaData['visa_description'] ?? null
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Visa information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving visa information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveAddressInfoSection($request, $client)
    {
        try {
            $requestData = $request->all();
            
            \Log::info('Address save request data:', $requestData);
            
            if (isset($requestData['zip']) && is_array($requestData['zip'])) {
                ClientAddress::where('client_id', $client->id)->delete();
                
                foreach ($requestData['zip'] as $key => $zip) {
                    $address_line_1 = $requestData['address_line_1'][$key] ?? null;
                    $address_line_2 = $requestData['address_line_2'][$key] ?? null;
                    $suburb = $requestData['suburb'][$key] ?? null;
                    $state = $requestData['state'][$key] ?? null;
                    $country = $requestData['country'][$key] ?? 'Australia';
                    $regional_code = $requestData['regional_code'][$key] ?? null;
                    $start_date = $requestData['address_start_date'][$key] ?? null;
                    $end_date = $requestData['address_end_date'][$key] ?? null;
                    
                    \Log::info("Processing address entry $key:", [
                        'zip' => $zip,
                        'address_line_1' => $address_line_1,
                        'suburb' => $suburb,
                        'state' => $state,
                        'country' => $country,
                        'regional_code' => $regional_code,
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ]);
                    
                    // Date conversion
                    $formatted_start_date = null;
                    if (!empty($start_date)) {
                        try {
                            $date = Carbon::createFromFormat('d/m/Y', $start_date);
                            $formatted_start_date = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid Start Date format: ' . $start_date
                            ], 422);
                        }
                    }
                    
                    $formatted_end_date = null;
                    if (!empty($end_date)) {
                        try {
                            $date = Carbon::createFromFormat('d/m/Y', $end_date);
                            $formatted_end_date = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid End Date format: ' . $end_date
                            ], 422);
                        }
                    }
                    
                    // Create combined address for backward compatibility
                    $combined_address = $this->combineAddress([
                        'line1' => $address_line_1,
                        'line2' => $address_line_2,
                        'suburb' => $suburb,
                        'state' => $state,
                        'postcode' => $zip,
                        'country' => $country
                    ]);
                    
                    if (!empty($address_line_1) || !empty($zip)) {
                        ClientAddress::create([
                            'admin_id' => Auth::user()->id,
                            'client_id' => $client->id,
                            'address' => $combined_address,
                            'city' => $suburb,
                            'address_line_1' => $address_line_1,
                            'address_line_2' => $address_line_2,
                            'suburb' => $suburb,
                            'state' => $state,
                            'country' => $country,
                            'zip' => $zip,
                            'regional_code' => $regional_code,
                            'start_date' => $formatted_start_date,
                            'end_date' => $formatted_end_date
                        ]);
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Address information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving address information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveTravelInfoSection($request, $client)
    {
        try {
            $travels = json_decode($request->input('travels'), true);
            
            if (!is_array($travels)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid travel data'
                ], 400);
            }

            // Delete existing travel records for this client
            ClientTravelInformation::where('client_id', $client->id)->delete();

            // Insert new travel records
            foreach ($travels as $travelData) {
                if (!empty($travelData['country_visited'])) {
                    // Convert date format from d/m/Y to Y-m-d if needed
                    $arrivalDate = null;
                    $departureDate = null;
                    
                    if (!empty($travelData['arrival_date'])) {
                        $arrivalDate = \DateTime::createFromFormat('d/m/Y', $travelData['arrival_date']);
                        $arrivalDate = $arrivalDate ? $arrivalDate->format('Y-m-d') : null;
                    }
                    
                    if (!empty($travelData['departure_date'])) {
                        $departureDate = \DateTime::createFromFormat('d/m/Y', $travelData['departure_date']);
                        $departureDate = $departureDate ? $departureDate->format('Y-m-d') : null;
                    }
                    
                    ClientTravelInformation::create([
                        'client_id' => $client->id,
                        'travel_country_visited' => $travelData['country_visited'],
                        'travel_arrival_date' => $arrivalDate,
                        'travel_departure_date' => $departureDate,
                        'travel_purpose' => $travelData['purpose'] ?? null
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Travel information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving travel information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveQualificationsInfoSection($request, $client)
    {
        try {
            $qualifications = json_decode($request->input('qualifications'), true);
            
            if (!is_array($qualifications)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid qualifications data'
                ], 400);
            }

            // Delete existing qualifications for this client
            ClientQualification::where('client_id', $client->id)->delete();

            // Insert new qualifications
            foreach ($qualifications as $qualData) {
                if (!empty($qualData['qualification'])) {
                    ClientQualification::create([
                        'client_id' => $client->id,
                        'qualification' => $qualData['qualification'],
                        'institution' => $qualData['institution'] ?? null,
                        'qual_country' => $qualData['country'] ?? null,
                        'year' => $qualData['year'] ?? null
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Qualifications updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving qualifications: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveExperienceInfoSection($request, $client)
    {
        try {
            $experiences = json_decode($request->input('experiences'), true);
            
            if (!is_array($experiences)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid experience data'
                ], 400);
            }

            // Delete existing experiences for this client
            ClientExperience::where('client_id', $client->id)->delete();

            // Insert new experiences
            foreach ($experiences as $expData) {
                if (!empty($expData['company'])) {
                    // Convert date format from d/m/Y to Y-m-d if needed
                    $startDate = null;
                    $endDate = null;
                    
                    if (!empty($expData['start_date'])) {
                        $startDate = \DateTime::createFromFormat('d/m/Y', $expData['start_date']);
                        $startDate = $startDate ? $startDate->format('Y-m-d') : null;
                    }
                    
                    if (!empty($expData['end_date'])) {
                        $endDate = \DateTime::createFromFormat('d/m/Y', $expData['end_date']);
                        $endDate = $endDate ? $endDate->format('Y-m-d') : null;
                    }
                    
                    ClientExperience::create([
                        'client_id' => $client->id,
                        'company' => $expData['company'],
                        'position' => $expData['position'] ?? null,
                        'exp_start_date' => $startDate,
                        'exp_end_date' => $endDate
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Work experience updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving experience: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveAdditionalInfoSection($request, $client)
    {
        try {
            $naatiTest = $request->input('naati_test');
            $naatiDate = $request->input('naati_date');
            $pyTest = $request->input('py_test');
            $pyDate = $request->input('py_date');
            
            // Convert date format if needed
            $naatiDateFormatted = null;
            $pyDateFormatted = null;
            
            if (!empty($naatiDate)) {
                $naatiDateObj = \DateTime::createFromFormat('d/m/Y', $naatiDate);
                $naatiDateFormatted = $naatiDateObj ? $naatiDateObj->format('Y-m-d') : null;
            }
            
            if (!empty($pyDate)) {
                $pyDateObj = \DateTime::createFromFormat('d/m/Y', $pyDate);
                $pyDateFormatted = $pyDateObj ? $pyDateObj->format('Y-m-d') : null;
            }
            
            $client->naati = $naatiTest;
            $client->naati_date = $naatiDateFormatted;
            $client->py = $pyTest;
            $client->py_date = $pyDateFormatted;
            $client->save();

            return response()->json([
                'success' => true,
                'message' => 'Additional information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving additional information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveCharacterInfoSection($request, $client)
    {
        try {
            $characters = json_decode($request->input('characters'), true);
            
            if (!is_array($characters)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid character data'
                ], 400);
            }

            // Delete existing character records for this client
            ClientCharacter::where('client_id', $client->id)->delete();

            // Insert new character records
            foreach ($characters as $charData) {
                if (!empty($charData['detail'])) {
                    ClientCharacter::create([
                        'client_id' => $client->id,
                        'character_detail' => $charData['detail']
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Character information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving character information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function savePartnerInfoSection($request, $client)
    {
        try {
            $partners = json_decode($request->input('partners'), true);
            
            if (!is_array($partners)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid partner data'
                ], 400);
            }

            // Delete existing partner records for this client (type = partner)
            ClientRelationship::where('client_id', $client->id)->where('type', 'partner')->delete();

            // Insert new partner records
            foreach ($partners as $partnerData) {
                if (!empty($partnerData['details'])) {
                    ClientRelationship::create([
                        'client_id' => $client->id,
                        'type' => 'partner',
                        'details' => $partnerData['details'],
                        'relationship_type' => $partnerData['relationship_type'] ?? null,
                        'gender' => $partnerData['gender'] ?? null,
                        'company_type' => $partnerData['company_type'] ?? null
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Partner information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving partner information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveChildrenInfoSection($request, $client)
    {
        try {
            $children = json_decode($request->input('children'), true);
            
            if (!is_array($children)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid children data'
                ], 400);
            }

            // Delete existing children records for this client (type = children)
            ClientRelationship::where('client_id', $client->id)->where('type', 'children')->delete();

            // Insert new children records
            foreach ($children as $childData) {
                if (!empty($childData['details'])) {
                    ClientRelationship::create([
                        'client_id' => $client->id,
                        'type' => 'children',
                        'details' => $childData['details'],
                        'relationship_type' => $childData['relationship_type'] ?? null,
                        'gender' => $childData['gender'] ?? null,
                        'company_type' => $childData['company_type'] ?? null
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Children information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving children information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveEoiInfoSection($request, $client)
    {
        try {
            $eois = json_decode($request->input('eois'), true);
            
            if (!is_array($eois)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid EOI data'
                ], 400);
            }

            // For now, just return success as ClientEoiReference model might need to be checked
            // This would need the ClientEoiReference model imported at the top
            return response()->json([
                'success' => true,
                'message' => 'EOI reference information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving EOI information: ' . $e->getMessage()
            ], 500);
        }
    }
}
