<?php

namespace App\Http\Controllers\CRM;

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
use App\Models\ClientEoiReference;
use App\Models\ClientMatter;
use App\Models\ActivitiesLog;
use App\Models\ClientPartner;
use Illuminate\Support\Facades\Log;
use App\Traits\LogsClientActivity;
use Auth;

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
    use LogsClientActivity;
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
            $clientContacts = ClientContact::select('phone', 'country_code', 'contact_type')->where('client_id', $request->client_id)->get();
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

    // Method 1: Search address using Google Places with fallback
    public function searchAddressFull(Request $request)
    {
        $query = $request->input('query');
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        
        // Try Google Places API first
        if ($apiKey) {
            $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';
            
            // Determine the best search strategy based on the query
            $searchParams = $this->getOptimalSearchParams($query);
            
            $params = http_build_query([
                'input' => $query,
                'key' => $apiKey,
                'types' => $searchParams['types'],
                'components' => 'country:au', // Restrict to Australia
                'fields' => 'place_id,description,structured_formatting'
            ]);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $data = json_decode($response, true);
            
            // Check if Google API is working
            if ($httpCode === 200 && isset($data['status']) && $data['status'] !== 'REQUEST_DENIED') {
                // Post-process the results to ensure house numbers are preserved
                $data = $this->postProcessAddressResults($data, $query);
                return response()->json($data);
            }
        }
        
        // Fallback: Use free geocoding service for basic suggestions
        return $this->getFallbackAddressSuggestions($query);
    }
    
    /**
     * Get optimal search parameters based on query content
     */
    private function getOptimalSearchParams($query)
    {
        // Check if query contains a house number (starts with digits)
        if (preg_match('/^\d+[A-Za-z]?\s+/', $query)) {
            // Query has house number with street name, use geocode for better results
            return [
                'types' => 'geocode'
            ];
        } elseif (preg_match('/^\d+[A-Za-z]?$/', $query)) {
            // Only house number is typed, use establishment type to get more relevant results
            return [
                'types' => 'establishment'
            ];
        } else {
            // No house number, use address type
            return [
                'types' => 'address'
            ];
        }
    }
    
    /**
     * Post-process address results to ensure house numbers are preserved and match the query
     */
    private function postProcessAddressResults($data, $originalQuery)
    {
        if (!isset($data['predictions']) || !is_array($data['predictions'])) {
            return $data;
        }
        
        $filteredPredictions = [];
        $queryLower = strtolower(trim($originalQuery));
        
        foreach ($data['predictions'] as $prediction) {
            $description = $prediction['description'];
            $descriptionLower = strtolower($description);
            
            // Check if the prediction starts with or contains the query text
            if (strpos($descriptionLower, $queryLower) === 0 || 
                strpos($descriptionLower, $queryLower) !== false) {
                
                // Extract house number from original query if present
                preg_match('/^(\d+[A-Za-z]?)\s*(.*)/', $originalQuery, $matches);
                if (count($matches) >= 2) {
                    $houseNumber = $matches[1];
                    
                    // If the description doesn't start with the house number, prepend it
                    if (!preg_match('/^' . preg_quote($houseNumber, '/') . '/i', $description)) {
                        $prediction['description'] = $houseNumber . ' ' . $description;
                    }
                }
                
                $filteredPredictions[] = $prediction;
            }
        }
        
        // If no matching results, try a more flexible approach
        if (empty($filteredPredictions)) {
            foreach ($data['predictions'] as $prediction) {
                $description = $prediction['description'];
                
                // Extract house number from original query if present
                preg_match('/^(\d+[A-Za-z]?)\s*(.*)/', $originalQuery, $matches);
                if (count($matches) >= 2) {
                    $houseNumber = $matches[1];
                    $streetName = trim($matches[2]);
                    
                    // If we have a street name, check if the description contains it
                    if (!empty($streetName) && stripos($description, $streetName) !== false) {
                        // Ensure the description starts with the house number
                        if (!preg_match('/^' . preg_quote($houseNumber, '/') . '/i', $description)) {
                            $prediction['description'] = $houseNumber . ' ' . $description;
                        }
                        $filteredPredictions[] = $prediction;
                    } elseif (empty($streetName)) {
                        // If only house number is typed, prepend it to any address
                        if (!preg_match('/^' . preg_quote($houseNumber, '/') . '/i', $description)) {
                            $prediction['description'] = $houseNumber . ' ' . $description;
                        }
                        $filteredPredictions[] = $prediction;
                    }
                }
            }
        }
        
        // Limit to 5 results and use filtered results if available
        if (!empty($filteredPredictions)) {
            $data['predictions'] = array_slice($filteredPredictions, 0, 5);
        }
        
        return $data;
    }
    
    /**
     * Fallback address suggestions using free service
     */
    private function getFallbackAddressSuggestions($query)
    {
        try {
            // Use OpenStreetMap Nominatim API (free)
            $url = 'https://nominatim.openstreetmap.org/search';
            $params = http_build_query([
                'q' => $query . ', Australia',
                'format' => 'json',
                'limit' => 5,
                'addressdetails' => 1,
                'countrycodes' => 'au'
            ]);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Migration Manager CRM');
            $response = curl_exec($ch);
            curl_close($ch);
            
            $results = json_decode($response, true);
            
            // Convert to Google Places API format for compatibility
            $predictions = [];
            if (is_array($results)) {
                foreach ($results as $result) {
                    // Format the display name to be more consistent with Google Places format
                    $formattedDescription = $this->formatFallbackAddress($result, $query);
                    
                    $predictions[] = [
                        'place_id' => 'fallback_' . md5($result['display_name']),
                        'description' => $formattedDescription,
                        'formatted_address' => $formattedDescription
                    ];
                }
            }
            
            // Post-process fallback results to ensure house numbers are preserved
            $predictions = $this->postProcessFallbackResults($predictions, $query);
            
            return response()->json([
                'status' => 'OK',
                'predictions' => $predictions
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'error_message' => 'Address service temporarily unavailable. Please enter address manually.',
                'predictions' => []
            ]);
        }
    }
    
    /**
     * Format fallback address to match Google Places format
     */
    private function formatFallbackAddress($result, $originalQuery)
    {
        $address = $result['address'] ?? [];
        $displayName = $result['display_name'] ?? '';
        
        // Extract house number from original query if present
        preg_match('/^(\d+[A-Za-z]?)\s+(.+)/', $originalQuery, $matches);
        
        if (count($matches) >= 3) {
            $houseNumber = $matches[1];
            $streetName = trim($matches[2], ', ');
            
            // Build a cleaner address format
            $parts = [];
            
            // Add house number and street
            $parts[] = $houseNumber . ' ' . $streetName;
            
            // Add suburb if available
            if (isset($address['suburb'])) {
                $parts[] = $address['suburb'];
            } elseif (isset($address['village'])) {
                $parts[] = $address['village'];
            }
            
            // Add state abbreviation
            if (isset($address['state'])) {
                $state = $address['state'];
                // Convert full state names to abbreviations
                $stateAbbr = $this->getStateAbbreviation($state);
                $parts[] = $stateAbbr;
            }
            
            // Add country
            $parts[] = 'Australia';
            
            return implode(', ', $parts);
        }
        
        return $displayName;
    }
    
    /**
     * Get state abbreviation from full state name
     */
    private function getStateAbbreviation($state)
    {
        $stateMap = [
            'New South Wales' => 'NSW',
            'Victoria' => 'VIC',
            'Queensland' => 'QLD',
            'South Australia' => 'SA',
            'Western Australia' => 'WA',
            'Tasmania' => 'TAS',
            'Northern Territory' => 'NT',
            'Australian Capital Territory' => 'ACT'
        ];
        
        return $stateMap[$state] ?? $state;
    }
    
    /**
     * Post-process fallback results to ensure house numbers are preserved and match the query
     */
    private function postProcessFallbackResults($predictions, $originalQuery)
    {
        $filteredPredictions = [];
        $queryLower = strtolower(trim($originalQuery));
        
        // Extract house number from original query if present
        preg_match('/^(\d+[A-Za-z]?)\s*(.*)/', $originalQuery, $matches);
        if (count($matches) >= 2) {
            $houseNumber = $matches[1];
            $streetName = trim($matches[2]);
            
            foreach ($predictions as $prediction) {
                $description = $prediction['description'];
                $descriptionLower = strtolower($description);
                
                // Check if the prediction starts with or contains the query text
                if (strpos($descriptionLower, $queryLower) === 0 || 
                    strpos($descriptionLower, $queryLower) !== false) {
                    
                    // Ensure the prediction starts with the house number
                    if (!preg_match('/^' . preg_quote($houseNumber, '/') . '/i', $description)) {
                        $prediction['description'] = $houseNumber . ' ' . $description;
                    }
                    
                    $filteredPredictions[] = $prediction;
                }
            }
            
            // If no matching results, try to prepend house number to relevant addresses
            if (empty($filteredPredictions)) {
                foreach ($predictions as $prediction) {
                    $description = $prediction['description'];
                    
                    if (!empty($streetName) && stripos($description, $streetName) !== false) {
                        // Ensure the description starts with the house number
                        if (!preg_match('/^' . preg_quote($houseNumber, '/') . '/i', $description)) {
                            $prediction['description'] = $houseNumber . ' ' . $description;
                        }
                        $filteredPredictions[] = $prediction;
                    } elseif (empty($streetName)) {
                        // If only house number is typed, prepend it to any address
                        if (!preg_match('/^' . preg_quote($houseNumber, '/') . '/i', $description)) {
                            $prediction['description'] = $houseNumber . ' ' . $description;
                        }
                        $filteredPredictions[] = $prediction;
                    }
                }
            }
            
            return !empty($filteredPredictions) ? array_slice($filteredPredictions, 0, 5) : $predictions;
        }
        
        return $predictions;
    }

    // Method 2: Get place details with fallback
    public function getPlaceDetails(Request $request)
    {
        $placeId = $request->input('place_id');
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        
        // Handle fallback place IDs
        if (strpos($placeId, 'fallback_') === 0) {
            return $this->getFallbackPlaceDetails($request);
        }
        
        // Try Google Places API
        if ($apiKey) {
            $url = 'https://maps.googleapis.com/maps/api/place/details/json';
            
            $params = http_build_query([
                'place_id' => $placeId,
                'key' => $apiKey,
                'fields' => 'address_components,formatted_address'
            ]);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $data = json_decode($response, true);
            
            if ($httpCode === 200 && isset($data['status']) && $data['status'] !== 'REQUEST_DENIED') {
                return response()->json($data);
            }
        }
        
        // Fallback: Return basic structure for manual entry
        return response()->json([
            'status' => 'OK',
            'result' => [
                'formatted_address' => $request->input('description', ''),
                'address_components' => []
            ]
        ]);
    }
    
    /**
     * Handle fallback place details
     */
    private function getFallbackPlaceDetails($request)
    {
        $description = $request->input('description', '');
        
        // Try to extract basic address components from the description
        $parts = explode(', ', $description);
        $addressComponents = [];
        
        if (count($parts) >= 3) {
            // More intelligent parsing for Australian addresses
            $addressComponents[] = [
                'long_name' => $parts[0],
                'short_name' => $parts[0],
                'types' => ['establishment', 'point_of_interest'] // Mark as establishment for airports/POIs
            ];
            
            // Find suburb (usually one of the middle parts that's not a number)
            $suburb = '';
            for ($i = 1; $i < count($parts) - 2; $i++) {
                if (!is_numeric($parts[$i]) && !in_array($parts[$i], ['NSW', 'VIC', 'QLD', 'SA', 'WA', 'TAS', 'NT', 'ACT'])) {
                    $suburb = $parts[$i];
                    break;
                }
            }
            
            if ($suburb) {
                $addressComponents[] = [
                    'long_name' => $suburb,
                    'short_name' => $suburb,
                    'types' => ['locality']
                ];
            }
            
            // Find state
            $state = '';
            foreach ($parts as $part) {
                if (in_array($part, ['NSW', 'VIC', 'QLD', 'SA', 'WA', 'TAS', 'NT', 'ACT'])) {
                    $state = $part;
                    break;
                }
            }
            
            if ($state) {
                $addressComponents[] = [
                    'long_name' => $state,
                    'short_name' => $state,
                    'types' => ['administrative_area_level_1']
                ];
            }
            
            // Find postcode (usually a 4-digit number)
            $postcode = '';
            foreach ($parts as $part) {
                if (is_numeric($part) && strlen($part) == 4) {
                    $postcode = $part;
                    break;
                }
            }
            
            if ($postcode) {
                $addressComponents[] = [
                    'long_name' => $postcode,
                    'short_name' => $postcode,
                    'types' => ['postal_code']
                ];
            }
            
            $addressComponents[] = [
                'long_name' => 'Australia',
                'short_name' => 'AU',
                'types' => ['country']
            ];
        }
        
        return response()->json([
            'status' => 'OK',
            'result' => [
                'formatted_address' => $description,
                'address_components' => $addressComponents
            ]
        ]);
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
        try {
            // Validate the incoming query
            $request->validate([
                'query' => 'required|string|min:2|max:255',
            ]);

            $query = $request->input('query');
            $excludeClient = $request->input('exclude_client');

            // Simplified search - just get all clients first, then filter
            $allClients = Admin::where('role', '7')
                ->select('id', 'email', 'first_name', 'last_name', 'phone', 'client_id')
                ->get();

            // Filter results in PHP for better debugging
            $filteredClients = $allClients->filter(function($client) use ($query, $excludeClient) {
                // Check if client matches search query
                $matches = false;
                $searchTerm = strtolower($query);
                
                if (strpos(strtolower($client->first_name), $searchTerm) !== false ||
                    strpos(strtolower($client->last_name), $searchTerm) !== false ||
                    strpos(strtolower($client->email), $searchTerm) !== false ||
                    strpos(strtolower($client->phone), $searchTerm) !== false ||
                    strpos(strtolower($client->client_id), $searchTerm) !== false) {
                    $matches = true;
                }
                
                // Exclude current client if provided
                if ($excludeClient && $client->id == $excludeClient) {
                    $matches = false;
                }
                
                return $matches;
            });

            // Convert to array and limit results
            $partners = $filteredClients->take(20)->values()->toArray();

            // Return JSON response with consistent structure
            return response()->json([
                'partners' => $partners,
                'debug' => [
                    'query' => $query,
                    'exclude_client' => $excludeClient,
                    'total_clients' => $allClients->count(),
                    'filtered_count' => count($partners)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'partners' => [],
                'error' => $e->getMessage(),
                'debug' => [
                    'query' => $request->input('query', ''),
                    'exclude_client' => $request->input('exclude_client', '')
                ]
            ], 200);
        }
    }

    // Test method to debug search functionality
    public function searchPartnerTest(Request $request)
    {
        $query = $request->input('query', 'vip');
        
        // Get total clients count
        $totalClients = Admin::where('role', '7')->count();
        
        // Get sample clients
        $sampleClients = Admin::where('role', '7')
            ->select('id', 'first_name', 'last_name', 'email', 'phone', 'client_id')
            ->limit(5)
            ->get();
        
        // Test search
        $searchResults = Admin::where('role', '7')
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', '%' . $query . '%')
                  ->orWhere('last_name', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%');
            })
            ->select('id', 'first_name', 'last_name', 'email', 'phone', 'client_id')
            ->get();
        
        return response()->json([
            'total_clients' => $totalClients,
            'sample_clients' => $sampleClients->toArray(),
            'search_query' => $query,
            'search_results' => $searchResults->toArray(),
            'search_count' => $searchResults->count()
        ]);
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
        $obj->marital_status	=	@$requestData['marital_status'];

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
            'Ex-Husband' => 'Ex-Husband',
            'Ex-Wife' => 'Ex-Wife',
            'Mother-in-law' => 'Mother-in-law',
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
            return redirect()->back()->with('error', config('constants.server_error'));
        } else if( $route ==url('/assignee')) {
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

            // Log activity for personal information update
            $this->logClientActivity(
                $clientId,
                'updated personal information',
                'Updated personal details including name, DOB, gender, marital status, and contact information',
                'activity'
            );

            $redirectUrl = $latestMatter
                ? '/clients/detail/'.$encodedId.'/'.$latestMatter->client_unique_matter_no
                : '/clients/detail/'.$encodedId;

            return Redirect::to($redirectUrl)->with('success', 'Details updated successfully');
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
                
                return view('crm.clients.edit', $data);
            } else {
                return Redirect::to('/clients')->with('error', 'Clients Not Exist');
            }
        } else {
            return Redirect::to('/clients')->with('error', config('constants.unauthorized'));
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
            $clientId = $request->input('id'); // Use 'id' instead of 'client_id' - 'id' is the database ID
            
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
                case 'partnerEoiInfo':
                    return $this->savePartnerEoiInfoSection($request, $client);
                case 'childrenInfo':
                    return $this->saveChildrenInfoSection($request, $client);
                case 'parentsInfo':
                    return $this->saveParentsInfoSection($request, $client);
                case 'siblingsInfo':
                    return $this->saveSiblingsInfoSection($request, $client);
                case 'othersInfo':
                    return $this->saveOthersInfoSection($request, $client);
                case 'eoiInfo':
                    return $this->saveEoiInfoSection($request, $client);
                case 'occupation':
                    return $this->saveOccupationInfoSection($request, $client);
                case 'test_scores':
                    return $this->saveTestScoreInfoSection($request, $client);
                case 'relatedFilesInfo':
                    return $this->saveRelatedFilesInfoSection($request, $client);
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
            $maritalStatus = $validated['marital_status'] ?? null;
            if ($maritalStatus === 'Defacto') {
                $maritalStatus = 'De Facto';
            }

            // Track changed fields for activity log with old and new values
            $changedFields = [];
            $fieldLabels = [
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'client_id' => 'Client ID',
                'dob' => 'Date of Birth',
                'gender' => 'Gender',
                'marital_status' => 'Marital Status'
            ];

            // Compare and track changes with old and new values
            if ($client->first_name !== $validated['first_name']) {
                $changedFields[$fieldLabels['first_name']] = [
                    'old' => $client->first_name,
                    'new' => $validated['first_name']
                ];
            }
            if ($client->last_name !== ($validated['last_name'] ?? null)) {
                $changedFields[$fieldLabels['last_name']] = [
                    'old' => $client->last_name,
                    'new' => $validated['last_name'] ?? null
                ];
            }
            if ($client->client_id !== $validated['client_id']) {
                $changedFields[$fieldLabels['client_id']] = [
                    'old' => $client->client_id,
                    'new' => $validated['client_id']
                ];
            }
            if ($client->dob !== $dob) {
                $changedFields[$fieldLabels['dob']] = [
                    'old' => $client->dob,
                    'new' => $dob
                ];
            }
            if ($client->gender !== ($validated['gender'] ?? null)) {
                $changedFields[$fieldLabels['gender']] = [
                    'old' => $client->gender,
                    'new' => $validated['gender'] ?? null
                ];
            }
            if ($client->marital_status !== $maritalStatus) {
                $changedFields[$fieldLabels['marital_status']] = [
                    'old' => $client->marital_status,
                    'new' => $maritalStatus
                ];
            }

            // Use direct assignment pattern (like the working old methods)
            $client->first_name = $validated['first_name'];
            $client->last_name = $validated['last_name'] ?? null;
            $client->client_id = $validated['client_id'];
            $client->dob = $dob;
            $client->age = $age;
            $client->gender = $validated['gender'] ?? null;
            $client->marital_status = $maritalStatus;
            $client->save();

            // Log activity with specific changed fields
            if (!empty($changedFields)) {
                $this->logClientActivityWithChanges(
                    $client->id,
                    'updated basic information',
                    $changedFields,
                    'activity'
                );
            }

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

                    // Use centralized validation
                    $validation = \App\Helpers\PhoneValidationHelper::validatePhoneNumber($phone);
                    if (!$validation['valid']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Phone number ' . ($index + 1) . ': ' . $validation['message']
                        ], 422);
                    }

                    // Skip duplicate check for placeholder numbers
                    if (!$validation['is_placeholder']) {
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

            // Log activity for phone numbers update
            $phoneCount = count($processedPhones);
            $this->logClientActivity(
                $client->id,
                'updated phone numbers',
                "Updated {$phoneCount} phone number(s)",
                'activity'
            );

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

            // Insert new emails and update admins table
            $primaryEmail = null;
            $primaryEmailType = 'Personal';
            
            foreach ($emails as $emailData) {
                if (!empty($emailData['email'])) {
                    ClientEmail::create([
                        'client_id' => $client->id,
                        'admin_id' => Auth::user()->id,
                        'email_type' => $emailData['email_type'],
                        'email' => $emailData['email']
                    ]);
                    
                    // Set primary email for admins table update
                    if ($emailData['email_type'] === 'Personal' || empty($primaryEmail)) {
                        $primaryEmail = $emailData['email'];
                        $primaryEmailType = $emailData['email_type'];
                    }
                }
            }
            
            // Update admins table with primary email
            if (!empty($primaryEmail)) {
                $client->email = $primaryEmail;
                $client->email_type = $primaryEmailType;
                $client->save();
            }

            // Log activity for email addresses update
            $emailCount = count(array_filter($emails, function($e) { return !empty($e['email']); }));
            $this->logClientActivity(
                $client->id,
                'updated email addresses',
                "Updated {$emailCount} email address(es)",
                'activity'
            );

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
            // Always update the country_passport field - set to null if no passports exist
            $client->country_passport = $primaryPassportCountry;
            $client->save();

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

            // Log activity for passport information update
            $passportCount = count(array_filter($passports, function($p) { 
                return !empty($p['passport_number']) || !empty($p['passport_country']); 
            }));
            $this->logClientActivity(
                $client->id,
                'updated passport information',
                "Updated {$passportCount} passport record(s)",
                'activity'
            );

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

            // Log activity for visa information update
            $visaCount = count(array_filter($visas, function($v) { 
                return !empty($v['visa_type_hidden']); 
            }));
            $this->logClientActivity(
                $client->id,
                'updated visa information',
                "Updated {$visaCount} visa record(s)",
                'activity'
            );

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
            
            // Log activity for address information update
            $addressCount = isset($requestData['zip']) && is_array($requestData['zip']) 
                ? count(array_filter($requestData['zip'], function($zip) { return !empty($zip); }))
                : 0;
            $this->logClientActivity(
                $client->id,
                'updated address information',
                "Updated {$addressCount} address record(s)",
                'activity'
            );

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

            // Log activity for travel information update
            $travelCount = count(array_filter($travels, function($travel) { 
                return !empty($travel['country_visited']); 
            }));
            $this->logClientActivity(
                $client->id,
                'updated travel information',
                "Updated {$travelCount} travel record(s)",
                'activity'
            );

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
            $requestData = $request->all();
            
            // Handle qualification deletion
            if (isset($requestData['delete_qualification_ids']) && is_array($requestData['delete_qualification_ids'])) {
                foreach ($requestData['delete_qualification_ids'] as $qualificationId) {
                    $qualification = ClientQualification::find($qualificationId);
                    if ($qualification && $qualification->client_id == $client->id) {
                        $qualification->delete();
                    }
                }
            }

            // Handle qualification data
            if (isset($requestData['level']) && is_array($requestData['level'])) {
                foreach ($requestData['level'] as $key => $level) {
                    $name = $requestData['name'][$key] ?? null;
                    $qual_college_name = $requestData['qual_college_name'][$key] ?? null;
                    $qual_campus = $requestData['qual_campus'][$key] ?? null;
                    $country = $requestData['qual_country'][$key] ?? null;
                    $qual_state = $requestData['qual_state'][$key] ?? null;
                    $start = $requestData['start_date'][$key] ?? null;
                    $finish = $requestData['finish_date'][$key] ?? null;
                    $relevant_qualification = isset($requestData['relevant_qualification'][$key]) && $requestData['relevant_qualification'][$key] == 1 ? 1 : 0;
                    $qualificationId = $requestData['qualification_id'][$key] ?? null;

                    // Convert start_date from dd/mm/yyyy to Y-m-d for database storage
                    $formatted_start_date = null;
                    if (!empty($start)) {
                        try {
                            $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $start);
                            $formatted_start_date = $startDate->format('Y-m-d');
                        } catch (\Exception $e) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid Start Date format: ' . $start . '. Must be dd/mm/yyyy.'
                            ], 422);
                        }
                    }

                    // Convert finish_date from dd/mm/yyyy to Y-m-d for database storage
                    $formatted_finish_date = null;
                    if (!empty($finish)) {
                        try {
                            $finishDate = \Carbon\Carbon::createFromFormat('d/m/Y', $finish);
                            $formatted_finish_date = $finishDate->format('Y-m-d');
                        } catch (\Exception $e) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid Finish Date format: ' . $finish . '. Must be dd/mm/yyyy.'
                            ], 422);
                        }
                    }

                    // Only save if there's at least level or name
                    if (!empty($level) || !empty($name)) {
                        if ($qualificationId) {
                            // Update existing qualification
                            $existingQualification = ClientQualification::find($qualificationId);
                            if ($existingQualification && $existingQualification->client_id == $client->id) {
                                $existingQualification->update([
                                    'admin_id' => Auth::user()->id,
                                    'level' => $level,
                                    'name' => $name,
                                    'qual_college_name' => $qual_college_name,
                                    'qual_campus' => $qual_campus,
                                    'country' => $country,
                                    'qual_state' => $qual_state,
                                    'start_date' => $formatted_start_date,
                                    'finish_date' => $formatted_finish_date,
                                    'relevant_qualification' => $relevant_qualification
                                ]);
                            }
                        } else {
                            // Create new qualification
                            ClientQualification::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $client->id,
                                'level' => $level,
                                'name' => $name,
                                'qual_college_name' => $qual_college_name,
                                'qual_campus' => $qual_campus,
                                'country' => $country,
                                'qual_state' => $qual_state,
                                'start_date' => $formatted_start_date,
                                'finish_date' => $formatted_finish_date,
                                'relevant_qualification' => $relevant_qualification
                            ]);
                        }
                    }
                }
            }

            // Update client's qualification_level and qualification_name with the most recent qualification
            if (isset($requestData['level']) && is_array($requestData['level'])) {
                $qualificationCount = count($requestData['level']);
                if ($qualificationCount > 0) {
                    $levelArray = array_values($requestData['level']);
                    $nameArray = array_values($requestData['name']);
                    
                    $lastLevel = $levelArray[$qualificationCount - 1] ?? null;
                    $lastName = $nameArray[$qualificationCount - 1] ?? null;

                    if (!empty($lastLevel) || !empty($lastName)) {
                        $client->qualification_level = $lastLevel;
                        $client->qualification_name = $lastName;
                        $client->save();
                    }
                }
            }

            // Log activity for educational qualifications update
            $qualificationCount = 0;
            if (isset($requestData['level']) && is_array($requestData['level'])) {
                foreach ($requestData['level'] as $key => $level) {
                    $name = $requestData['name'][$key] ?? null;
                    if (!empty($level) || !empty($name)) {
                        $qualificationCount++;
                    }
                }
            }
            $this->logClientActivity(
                $client->id,
                'updated educational qualifications',
                "Updated {$qualificationCount} qualification record(s)",
                'activity'
            );

            return response()->json([
                'success' => true,
                'message' => 'Qualifications information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save qualifications: ' . $e->getMessage()
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
                if (!empty($expData['job_title']) || !empty($expData['job_code']) || !empty($expData['job_emp_name'])) {
                    // Convert date format from d/m/Y to Y-m-d if needed
                    $startDate = null;
                    $endDate = null;
                    
                    if (!empty($expData['job_start_date'])) {
                        $startDate = \DateTime::createFromFormat('d/m/Y', $expData['job_start_date']);
                        $startDate = $startDate ? $startDate->format('Y-m-d') : null;
                    }
                    
                    if (!empty($expData['job_finish_date'])) {
                        $endDate = \DateTime::createFromFormat('d/m/Y', $expData['job_finish_date']);
                        $endDate = $endDate ? $endDate->format('Y-m-d') : null;
                    }
                    
                    ClientExperience::create([
                        'client_id' => $client->id,
                        'admin_id' => Auth::user()->id,
                        'job_title' => $expData['job_title'] ?? null,
                        'job_code' => $expData['job_code'] ?? null,
                        'job_country' => $expData['job_country'] ?? null,
                        'job_start_date' => $startDate,
                        'job_finish_date' => $endDate,
                        'relevant_experience' => $expData['relevant_experience'] ?? 0,
                        'job_emp_name' => $expData['job_emp_name'] ?? null,
                        'job_state' => $expData['job_state'] ?? null,
                        'job_type' => $expData['job_type'] ?? null
                    ]);
                }
            }

            // Log activity for work experience update
            $experienceCount = count(array_filter($experiences, function($exp) { 
                return !empty($exp['job_title']) || !empty($exp['job_code']) || !empty($exp['job_emp_name']); 
            }));
            $this->logClientActivity(
                $client->id,
                'updated work experience',
                "Updated {$experienceCount} experience record(s)",
                'activity'
            );

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
            
            // New EOI qualification fields
            $australianStudy = $request->input('australian_study');
            $australianStudyDate = $request->input('australian_study_date');
            $specialistEducation = $request->input('specialist_education');
            $specialistEducationDate = $request->input('specialist_education_date');
            $regionalStudy = $request->input('regional_study');
            $regionalStudyDate = $request->input('regional_study_date');
            
            // Convert date format if needed
            $naatiDateFormatted = null;
            $pyDateFormatted = null;
            $australianStudyDateFormatted = null;
            $specialistEducationDateFormatted = null;
            $regionalStudyDateFormatted = null;
            
            if (!empty($naatiDate)) {
                $naatiDateObj = \DateTime::createFromFormat('d/m/Y', $naatiDate);
                $naatiDateFormatted = $naatiDateObj ? $naatiDateObj->format('Y-m-d') : null;
            }
            
            if (!empty($pyDate)) {
                $pyDateObj = \DateTime::createFromFormat('d/m/Y', $pyDate);
                $pyDateFormatted = $pyDateObj ? $pyDateObj->format('Y-m-d') : null;
            }
            
            if (!empty($australianStudyDate)) {
                $australianStudyDateObj = \DateTime::createFromFormat('d/m/Y', $australianStudyDate);
                $australianStudyDateFormatted = $australianStudyDateObj ? $australianStudyDateObj->format('Y-m-d') : null;
            }
            
            if (!empty($specialistEducationDate)) {
                $specialistEducationDateObj = \DateTime::createFromFormat('d/m/Y', $specialistEducationDate);
                $specialistEducationDateFormatted = $specialistEducationDateObj ? $specialistEducationDateObj->format('Y-m-d') : null;
            }
            
            if (!empty($regionalStudyDate)) {
                $regionalStudyDateObj = \DateTime::createFromFormat('d/m/Y', $regionalStudyDate);
                $regionalStudyDateFormatted = $regionalStudyDateObj ? $regionalStudyDateObj->format('Y-m-d') : null;
            }
            
            // Save all fields
            $client->naati_test = $naatiTest;
            $client->naati_date = $naatiDateFormatted;
            $client->py_test = $pyTest;
            $client->py_date = $pyDateFormatted;
            $client->australian_study = $australianStudy;
            $client->australian_study_date = $australianStudyDateFormatted;
            $client->specialist_education = $specialistEducation;
            $client->specialist_education_date = $specialistEducationDateFormatted;
            $client->regional_study = $regionalStudy;
            $client->regional_study_date = $regionalStudyDateFormatted;
            $client->save();

            // Clear points cache when EOI qualification data changes
            if (class_exists('\App\Services\PointsService')) {
                $pointsService = new \App\Services\PointsService();
                $pointsService->clearCache($client->id);
            }

            // Log activity for additional information update
            $updatedFields = [];
            if (!empty($naatiTest) || !empty($naatiDate)) $updatedFields[] = 'NAATI Test';
            if (!empty($pyTest) || !empty($pyDate)) $updatedFields[] = 'PY Test';
            if (!empty($australianStudy) || !empty($australianStudyDate)) $updatedFields[] = 'Australian Study';
            if (!empty($specialistEducation) || !empty($specialistEducationDate)) $updatedFields[] = 'Specialist Education';
            if (!empty($regionalStudy) || !empty($regionalStudyDate)) $updatedFields[] = 'Regional Study';
            
            if (!empty($updatedFields)) {
                $this->logClientActivity(
                    $client->id,
                    'updated additional information',
                    'Updated: ' . implode(', ', $updatedFields),
                    'activity'
                );
            }

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
            $characterCount = 0;
            foreach ($characters as $charData) {
                if (!empty($charData['detail']) && !empty($charData['type_of_character'])) {
                    ClientCharacter::create([
                        'client_id' => $client->id,
                        'admin_id' => auth()->id(),
                        'type_of_character' => $charData['type_of_character'],
                        'character_detail' => $charData['detail']
                    ]);
                    $characterCount++;
                }
            }

            // Log activity for character information update
            $this->logClientActivity(
                $client->id,
                'updated character information',
                "Updated {$characterCount} character record(s)",
                'activity'
            );

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

            // Delete existing partner records for this client (filter by partner relationship types)
            // First, get the related_client_ids that will be affected
            $existingRelationships = ClientRelationship::where('client_id', $client->id)
                ->whereIn('relationship_type', ['Husband', 'Wife', 'Ex-Wife', 'Defacto'])
                ->get();
            
            // Delete the main relationships
            ClientRelationship::where('client_id', $client->id)
                ->whereIn('relationship_type', ['Husband', 'Wife', 'Ex-Wife', 'Defacto'])
                ->delete();
            
            // Delete reciprocal relationships
            foreach ($existingRelationships as $relationship) {
                if ($relationship->related_client_id) {
                    ClientRelationship::where('client_id', $relationship->related_client_id)
                        ->where('related_client_id', $client->id)
                        ->whereIn('relationship_type', ['Husband', 'Wife', 'Ex-Wife', 'Defacto'])
                        ->delete();
                }
            }

            // Insert new partner records
            $partnerCount = 0;
            foreach ($partners as $partnerData) {
                if (!empty($partnerData['details']) || !empty($partnerData['relationship_type'])) {
                    // Convert DOB from dd/mm/yyyy to YYYY-mm-dd format
                    $dob = null;
                    if (!empty($partnerData['dob']) && $partnerData['dob'] !== 'dd/mm/yyyy') {
                        try {
                            $dobDate = \Carbon\Carbon::createFromFormat('d/m/Y', $partnerData['dob']);
                            $dob = $dobDate->format('Y-m-d');
                        } catch (\Exception $e) {
                            // If date format is invalid, set to null
                            $dob = null;
                        }
                    }
                    
                    // Create the main relationship entry
                    ClientRelationship::create([
                        'admin_id' => auth()->id(),
                        'client_id' => $client->id,
                        'related_client_id' => (!empty($partnerData['partner_id']) && $partnerData['partner_id'] !== '0') ? $partnerData['partner_id'] : null,
                        'details' => $partnerData['details'],
                        'relationship_type' => $partnerData['relationship_type'] ?? null,
                        'gender' => $partnerData['gender'] ?? null,
                        'company_type' => $partnerData['company_type'] ?? null,
                        'last_name' => $partnerData['last_name'] ?? null,
                        'dob' => $dob,
                        'email' => $partnerData['email'] ?? null,
                        'first_name' => $partnerData['first_name'] ?? null,
                        'phone' => $partnerData['phone'] ?? null
                    ]);
                    $partnerCount++;
                    
                    // Create reciprocal relationship entry if partner_id exists (existing client)
                    if (!empty($partnerData['partner_id'])) {
                        // Get the related client's details for the reciprocal entry
                        $relatedClient = Admin::where('id', $partnerData['partner_id'])->where('role', '7')->first();
                        
                        if ($relatedClient) {
                            // Determine reciprocal relationship type
                            $reciprocalRelationshipType = $this->getReciprocalRelationshipType($partnerData['relationship_type']);
                            
                            // Create reciprocal entry
                            ClientRelationship::create([
                                'admin_id' => auth()->id(),
                                'client_id' => $partnerData['partner_id'],
                                'related_client_id' => $client->id,
                                'details' => $client->first_name . ' ' . $client->last_name . ' (' . $client->email . ', ' . $client->phone . ', ' . $client->client_id . ')',
                                'relationship_type' => $reciprocalRelationshipType,
                                'gender' => $client->gender ?? null,
                                'company_type' => null, // Reciprocal entries should have null company_type
                                'last_name' => null,    // Reciprocal entries should have null last_name
                                'dob' => null,          // Reciprocal entries should have null dob
                                'email' => null,        // Reciprocal entries should have null email
                                'first_name' => null,   // Reciprocal entries should have null first_name
                                'phone' => null         // Reciprocal entries should have null phone
                            ]);
                        }
                    }
                }
            }

            // Log activity for partner information update
            $this->logClientActivity(
                $client->id,
                'updated partner information',
                "Updated {$partnerCount} partner/spouse record(s)",
                'activity'
            );

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

    /**
     * Get reciprocal relationship type
     */
    private function getReciprocalRelationshipType($relationshipType)
    {
        switch ($relationshipType) {
            case 'Husband':
                return 'Wife';
            case 'Wife':
                return 'Husband';
            case 'Ex-Husband':
                return 'Ex-Wife';
            case 'Ex-Wife':
                return 'Ex-Husband';
            case 'Mother-in-law':
                return 'Mother-in-law'; // No specific reciprocal
            case 'Defacto':
                return 'Defacto';
            default:
                return $relationshipType; // Return same type if no specific reciprocal
        }
    }

    private function savePartnerEoiInfoSection($request, $client)
    {
        try {
            $selectedPartnerId = $request->input('selected_partner_id');
            
            if (!$selectedPartnerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a partner for EOI calculation'
                ], 400);
            }

            // Get the selected partner's data from their actual profile
            $partnerClient = \App\Models\Admin::find($selectedPartnerId);
            if (!$partnerClient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected partner not found'
                ], 404);
            }

            // Get or create spouse details record
            $spouseDetail = \App\Models\ClientSpouseDetail::firstOrNew(['client_id' => $client->id]);
            
            // Auto-populate from partner's actual profile
            $spouseDetail->related_client_id = $selectedPartnerId;
            $spouseDetail->dob = $partnerClient->dob;
            
            // Check if partner is citizen/PR from their visa records
            $spouseDetail->is_citizen = 0; // Default
            $spouseDetail->has_pr = 0; // Default
            
            // Get partner's English test scores
            $partnerTestScore = $partnerClient->testScores()->latest()->first();
            if ($partnerTestScore) {
                $spouseDetail->spouse_has_english_score = 1;
                $spouseDetail->spouse_test_type = $partnerTestScore->test_type;
                $spouseDetail->spouse_listening_score = $partnerTestScore->listening;
                $spouseDetail->spouse_reading_score = $partnerTestScore->reading;
                $spouseDetail->spouse_writing_score = $partnerTestScore->writing;
                $spouseDetail->spouse_speaking_score = $partnerTestScore->speaking;
                $spouseDetail->spouse_overall_score = $partnerTestScore->overall_score;
                $spouseDetail->spouse_test_date = $partnerTestScore->test_date;
            } else {
                $spouseDetail->spouse_has_english_score = 0;
                $spouseDetail->spouse_test_type = null;
                $spouseDetail->spouse_listening_score = null;
                $spouseDetail->spouse_reading_score = null;
                $spouseDetail->spouse_writing_score = null;
                $spouseDetail->spouse_speaking_score = null;
                $spouseDetail->spouse_overall_score = null;
                $spouseDetail->spouse_test_date = null;
            }

            // Get partner's skills assessment
            $partnerOccupation = $partnerClient->occupations()->latest()->first();
            if ($partnerOccupation) {
                $spouseDetail->spouse_has_skill_assessment = 1;
                $spouseDetail->spouse_nomi_occupation = $partnerOccupation->nomi_occupation;
                $spouseDetail->spouse_assessment_date = $partnerOccupation->dates;
                $spouseDetail->spouse_skill_assessment_status = 'Valid'; // Default status
            } else {
                $spouseDetail->spouse_has_skill_assessment = 0;
                $spouseDetail->spouse_nomi_occupation = null;
                $spouseDetail->spouse_assessment_date = null;
                $spouseDetail->spouse_skill_assessment_status = null;
            }

            $spouseDetail->save();

            // Clear points cache when partner EOI data changes
            if (class_exists('\App\Services\PointsService')) {
                $pointsService = new \App\Services\PointsService();
                $pointsService->clearCache($client->id);
            }

            // Log activity for partner EOI information update
            $this->logClientActivity(
                $client->id,
                'updated partner EOI information',
                "Updated partner EOI information for {$partnerClient->first_name} {$partnerClient->last_name}",
                'activity'
            );

            return response()->json([
                'success' => true,
                'message' => 'Partner EOI information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving partner EOI information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPartnerEoiData($partnerId)
    {
        try {
            // Get the partner's data from their actual profile
            $partnerClient = \App\Models\Admin::find($partnerId);
            if (!$partnerClient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Partner not found'
                ], 404);
            }

            // Get partner's English test scores
            $partnerTestScore = $partnerClient->testScores()->latest()->first();
            
            // Get partner's skills assessment
            $partnerOccupation = $partnerClient->occupations()->latest()->first();
            
            // Check if partner is citizen/PR from their visa records
            $isCitizen = 0;
            $hasPR = 0;
            
            // Build the response data
            $partnerData = [
                'partner_name' => $partnerClient->first_name . ' ' . $partnerClient->last_name,
                'dob' => $partnerClient->dob ? date('d/m/Y', strtotime($partnerClient->dob)) : 'Not set',
                'is_citizen' => $isCitizen,
                'has_pr' => $hasPR,
                'english_test' => null,
                'skills_assessment' => null
            ];

            if ($partnerTestScore) {
                $partnerData['english_test'] = [
                    'test_type' => $partnerTestScore->test_type ?? 'Not set',
                    'listening' => $partnerTestScore->listening ?? 'Not set',
                    'reading' => $partnerTestScore->reading ?? 'Not set',
                    'writing' => $partnerTestScore->writing ?? 'Not set',
                    'speaking' => $partnerTestScore->speaking ?? 'Not set',
                    'overall' => $partnerTestScore->overall ?? 'Not set',
                    'test_date' => $partnerTestScore->test_date ? date('d/m/Y', strtotime($partnerTestScore->test_date)) : 'Not set'
                ];
            }

            if ($partnerOccupation) {
                $partnerData['skills_assessment'] = [
                    'has_assessment' => 'Yes',
                    'occupation' => $partnerOccupation->nomi_occupation ?? 'Not set',
                    'assessment_date' => $partnerOccupation->dates ? date('d/m/Y', strtotime($partnerOccupation->dates)) : 'Not set',
                    'status' => 'Valid'
                ];
            } else {
                $partnerData['skills_assessment'] = [
                    'has_assessment' => 'No',
                    'occupation' => 'Not set',
                    'assessment_date' => 'Not set',
                    'status' => 'Not set'
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $partnerData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching partner EOI data: ' . $e->getMessage()
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

            // Function to get reciprocal relationship for children
            $getReciprocalRelationship = function($relationshipType, $childGender, $parentGender) {
                $reciprocalMap = [
                    'Son' => 'Father', // Default to Father
                    'Daughter' => 'Father', // Default to Father
                    'Step Son' => 'Step Father', // Default to Step Father
                    'Step Daughter' => 'Step Father', // Default to Step Father
                ];
                
                // Adjust based on parent's gender
                if (strtolower($parentGender) === 'female') {
                    $reciprocalMap = [
                        'Son' => 'Mother',
                        'Daughter' => 'Mother',
                        'Step Son' => 'Step Mother',
                        'Step Daughter' => 'Step Mother',
                    ];
                }
                
                return $reciprocalMap[$relationshipType] ?? 'Parent';
            };

            // Delete existing children records for this client (filter by children relationship types)
            ClientRelationship::where('client_id', $client->id)
                ->whereIn('relationship_type', ['Son', 'Daughter', 'Step Son', 'Step Daughter'])
                ->delete();

            // Insert new children records
            $childrenCount = 0;
            foreach ($children as $childData) {
                if (!empty($childData['details']) || !empty($childData['relationship_type'])) {
                    $relatedClientId = !empty($childData['child_id']) && $childData['child_id'] != 0 ? $childData['child_id'] : null;
                    $saveExtraFields = !$relatedClientId;
                    $childrenCount++;
                    
                    // Convert DOB from d/m/Y format to Y-m-d format for database storage
                    $dobFormatted = null;
                    if ($saveExtraFields && !empty($childData['dob']) && $childData['dob'] !== 'dd/mm/yyyy') {
                        try {
                            $dobDate = \Carbon\Carbon::createFromFormat('d/m/Y', $childData['dob']);
                            $dobFormatted = $dobDate->format('Y-m-d');
                        } catch (\Exception $e) {
                            // If date format is invalid, set to null and log the error
                            \Log::warning('Invalid DOB format for child: ' . $childData['dob']);
                            $dobFormatted = null;
                        }
                    }
                    
                    // Prepare details field for primary relationship
                    $primaryDetails = null;
                    if ($relatedClientId) {
                        // For existing clients, use the details from the form (which contains the constructed string)
                        $primaryDetails = $childData['details'];
                    } else {
                        // For new clients, construct details from the form data
                        $firstName = trim($childData['first_name'] ?? '');
                        $lastName = trim($childData['last_name'] ?? '');
                        $email = trim($childData['email'] ?? '');
                        $phone = trim($childData['phone'] ?? '');
                        
                        if (!empty($firstName) || !empty($lastName)) {
                            $primaryDetails = trim($firstName . ' ' . $lastName);
                            if (!empty($email)) {
                                $primaryDetails .= ' (' . $email;
                                if (!empty($phone)) {
                                    $primaryDetails .= ', ' . $phone;
                                }
                                $primaryDetails .= ')';
                            } elseif (!empty($phone)) {
                                $primaryDetails .= ' (' . $phone . ')';
                            }
                        } else {
                            $primaryDetails = $childData['details'];
                        }
                    }
                    
                    // Create the primary relationship (parent -> child)
                    $newChild = ClientRelationship::create([
                        'admin_id' => auth()->id(),
                        'client_id' => $client->id,
                        'related_client_id' => $relatedClientId,
                        'details' => $primaryDetails,
                        'relationship_type' => $childData['relationship_type'] ?? null,
                        'gender' => $childData['gender'] ?? null,
                        'company_type' => $childData['company_type'] ?? null,
                        'email' => $saveExtraFields ? ($childData['email'] ?? null) : null,
                        'first_name' => $saveExtraFields ? ($childData['first_name'] ?? null) : null,
                        'last_name' => $saveExtraFields ? ($childData['last_name'] ?? null) : null,
                        'phone' => $saveExtraFields ? ($childData['phone'] ?? null) : null,
                        'dob' => $dobFormatted
                    ]);

                    // Create reciprocal relationship if related_client_id is set and not 0
                    if ($relatedClientId) {
                        $relatedChild = \App\Models\Admin::find($relatedClientId);
                        if ($relatedChild) {
                            // Get the reciprocal relationship type based on parent's gender
                            $reciprocalRelationshipType = $getReciprocalRelationship(
                                $childData['relationship_type'] ?? 'Son', 
                                $childData['gender'] ?? 'Male', 
                                $client->gender ?? 'Male'
                            );
                            
                            // Check if reciprocal relationship already exists to avoid duplicates
                            $existingReciprocal = ClientRelationship::where('client_id', $relatedClientId)
                                ->where('related_client_id', $client->id)
                                ->where('relationship_type', $reciprocalRelationshipType)
                                ->first();
                            
                            if (!$existingReciprocal) {
                                ClientRelationship::create([
                                    'admin_id' => auth()->id(),
                                    'client_id' => $relatedClientId,
                                    'related_client_id' => $client->id,
                                    'details' => "{$client->first_name} {$client->last_name} ({$client->email}, {$client->phone}, {$client->client_id})",
                                    'relationship_type' => $reciprocalRelationshipType,
                                    'company_type' => null, // Reciprocal entries should have null company_type
                                    'gender' => $client->gender ?? null
                                ]);
                            }
                        }
                    }
                }
            }

            // Log activity for children information update
            $this->logClientActivity(
                $client->id,
                'updated children information',
                "Updated {$childrenCount} children record(s)",
                'activity'
            );

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

            // Delete existing EOI references for this client
            ClientEoiReference::where('client_id', $client->id)->delete();

            // Save new EOI references
            foreach ($eois as $eoiData) {
                if (!empty($eoiData['eoi_number']) || !empty($eoiData['subclass']) || !empty($eoiData['occupation'])) {
                    // Format submission date from d/m/Y to Y-m-d
                    $formatted_submission_date = null;
                    if (!empty($eoiData['submission_date'])) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $eoiData['submission_date']);
                            $formatted_submission_date = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            // If format conversion fails, try to use the date as-is
                            $formatted_submission_date = $eoiData['submission_date'];
                        }
                    }
                    
                    ClientEoiReference::create([
                        'client_id' => $client->id,
                        'admin_id' => Auth::id(),
                        'EOI_number' => $eoiData['eoi_number'] ?? null,
                        'EOI_subclass' => $eoiData['subclass'] ?? null,
                        'EOI_occupation' => $eoiData['occupation'] ?? null,
                        'EOI_point' => $eoiData['point'] ?? null,
                        'EOI_state' => $eoiData['state'] ?? null,
                        'EOI_submission_date' => $formatted_submission_date,
                        'EOI_ROI' => $eoiData['roi'] ?? null,
                        'EOI_password' => $eoiData['password'] ?? null,
                    ]);
                }
            }

            // Log activity for EOI references update
            $eoiCount = count(array_filter($eois, function($eoi) { 
                return !empty($eoi['eoi_number']) || !empty($eoi['subclass']) || !empty($eoi['occupation']); 
            }));
            $this->logClientActivity(
                $client->id,
                'updated EOI references',
                "Updated {$eoiCount} EOI reference record(s)",
                'activity'
            );

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

    /**
     * Save occupation information section
     */
    private function saveOccupationInfoSection($request, $client)
    {
        try {
            $requestData = $request->all();
            
            // Handle occupation deletion
            if (isset($requestData['delete_occupation_ids']) && is_array($requestData['delete_occupation_ids'])) {
                foreach ($requestData['delete_occupation_ids'] as $occupationId) {
                    $occupation = \App\Models\ClientOccupation::find($occupationId);
                    if ($occupation && $occupation->client_id == $client->id) {
                        $occupation->delete();
                    }
                }
            }

            // Handle occupation data
            if (isset($requestData['nomi_occupation']) && is_array($requestData['nomi_occupation'])) {
                foreach ($requestData['nomi_occupation'] as $key => $nomiOccupation) {
                    if (!empty($nomiOccupation) || isset($requestData['skill_assessment_hidden'][$key])) {
                        $occupationId = $requestData['occupation_id'][$key] ?? null;
                        $anzscoOccupationId = $requestData['anzsco_occupation_id'][$key] ?? null;
                        $skillAssessment = $requestData['skill_assessment_hidden'][$key] ?? null;
                        $occupationCode = $requestData['occupation_code'][$key] ?? null;
                        $list = $requestData['list'][$key] ?? null;
                        $visaSubclass = $requestData['visa_subclass'][$key] ?? null;
                        $date = $requestData['dates'][$key] ?? null;
                        $expiryDate = $requestData['expiry_dates'][$key] ?? null;
                        $occReferenceNo = $requestData['occ_reference_no'][$key] ?? null;
                        $relevantOccupation = isset($requestData['relevant_occupation_hidden'][$key]) && $requestData['relevant_occupation_hidden'][$key] === '1' ? 1 : 0;

                        // Convert dates from dd/mm/yyyy to Y-m-d for database storage
                        $formattedDate = null;
                        if (!empty($date)) {
                            try {
                                $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $date);
                                $formattedDate = $dateObj->format('Y-m-d');
                            } catch (\Exception $e) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Invalid Assessment Date format: ' . $date
                                ], 422);
                            }
                        }

                        $formattedExpiryDate = null;
                        if (!empty($expiryDate)) {
                            try {
                                $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $expiryDate);
                                $formattedExpiryDate = $dateObj->format('Y-m-d');
                            } catch (\Exception $e) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Invalid Expiry Date format: ' . $expiryDate
                                ], 422);
                            }
                        }

                        if ($occupationId) {
                            // Update existing record
                            $existingOccupation = \App\Models\ClientOccupation::find($occupationId);
                            if ($existingOccupation && $existingOccupation->client_id == $client->id) {
                                $existingOccupation->update([
                                    'admin_id' => Auth::user()->id,
                                    'skill_assessment' => $skillAssessment,
                                    'nomi_occupation' => $nomiOccupation,
                                    'occupation_code' => $occupationCode,
                                    'list' => $list,
                                    'visa_subclass' => $visaSubclass,
                                    'dates' => $formattedDate,
                                    'expiry_dates' => $formattedExpiryDate,
                                    'occ_reference_no' => $occReferenceNo,
                                    'relevant_occupation' => $relevantOccupation,
                                    'anzsco_occupation_id' => $anzscoOccupationId
                                ]);
                            }
                        } else {
                            // Create new record
                            \App\Models\ClientOccupation::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $client->id,
                                'skill_assessment' => $skillAssessment,
                                'nomi_occupation' => $nomiOccupation,
                                'occupation_code' => $occupationCode,
                                'list' => $list,
                                'visa_subclass' => $visaSubclass,
                                'dates' => $formattedDate,
                                'expiry_dates' => $formattedExpiryDate,
                                'occ_reference_no' => $occReferenceNo,
                                'relevant_occupation' => $relevantOccupation,
                                'anzsco_occupation_id' => $anzscoOccupationId
                            ]);
                        }
                    }
                }
            }

            // Log activity for occupation & skills update
            $occupationCount = 0;
            if (isset($requestData['nomi_occupation']) && is_array($requestData['nomi_occupation'])) {
                foreach ($requestData['nomi_occupation'] as $key => $nomiOccupation) {
                    if (!empty($nomiOccupation) || isset($requestData['skill_assessment_hidden'][$key])) {
                        $occupationCount++;
                    }
                }
            }
            $this->logClientActivity(
                $client->id,
                'updated occupation & skills',
                "Updated {$occupationCount} occupation record(s)",
                'activity'
            );

            return response()->json([
                'success' => true,
                'message' => 'Occupation information saved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving occupation information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveTestScoreInfoSection($request, $client)
    {
        try {
            $requestData = $request->all();
            
            // Handle test score deletion
            if (isset($requestData['delete_test_score_ids']) && is_array($requestData['delete_test_score_ids'])) {
                foreach ($requestData['delete_test_score_ids'] as $testScoreId) {
                    $testScore = \App\Models\ClientTestScore::find($testScoreId);
                    if ($testScore && $testScore->client_id == $client->id) {
                        $testScore->delete();
                    }
                }
            }

            // Handle test score data
            if (isset($requestData['test_type_hidden']) && is_array($requestData['test_type_hidden'])) {
                foreach ($requestData['test_type_hidden'] as $key => $testType) {
                    if (!empty($testType)) {
                        $testScoreId = $requestData['test_score_id'][$key] ?? null;
                        $listening = $requestData['listening'][$key] ?? null;
                        $reading = $requestData['reading'][$key] ?? null;
                        $writing = $requestData['writing'][$key] ?? null;
                        $speaking = $requestData['speaking'][$key] ?? null;
                        $overallScore = $requestData['overall_score'][$key] ?? null;
                        $testDate = $requestData['test_date'][$key] ?? null;
                        $testReferenceNo = $requestData['test_reference_no'][$key] ?? null;
                        $relevantTest = isset($requestData['relevant_test_hidden'][$key]) && $requestData['relevant_test_hidden'][$key] === '1' ? 1 : 0;

                        // Convert test_date from dd/mm/yyyy to Y-m-d for database storage
                        $formattedTestDate = null;
                        if (!empty($testDate)) {
                            try {
                                $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $testDate);
                                $formattedTestDate = $dateObj->format('Y-m-d');
                            } catch (\Exception $e) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Invalid Test Date format: ' . $testDate
                                ], 422);
                            }
                        }

                        // Calculate proficiency level using the service
                        $proficiencyService = new \App\Services\EnglishProficiencyService();
                        $scores = [
                            'listening' => $listening,
                            'reading' => $reading,
                            'writing' => $writing,
                            'speaking' => $speaking,
                            'overall' => $overallScore
                        ];
                        $proficiencyResult = $proficiencyService->calculateProficiency($testType, $scores, $formattedTestDate);

                        if ($testScoreId) {
                            // Update existing record
                            $existingTestScore = \App\Models\ClientTestScore::find($testScoreId);
                            if ($existingTestScore && $existingTestScore->client_id == $client->id) {
                                $existingTestScore->update([
                                    'admin_id' => Auth::user()->id,
                                    'test_type' => $testType,
                                    'listening' => $listening,
                                    'reading' => $reading,
                                    'writing' => $writing,
                                    'speaking' => $speaking,
                                    'overall_score' => $overallScore,
                                    'proficiency_level' => $proficiencyResult['level'],
                                    'proficiency_points' => $proficiencyResult['points'],
                                    'test_date' => $formattedTestDate,
                                    'test_reference_no' => $testReferenceNo,
                                    'relevant_test' => $relevantTest
                                ]);
                            }
                        } else {
                            // Create new record
                            \App\Models\ClientTestScore::create([
                                'admin_id' => Auth::user()->id,
                                'client_id' => $client->id,
                                'test_type' => $testType,
                                'listening' => $listening,
                                'reading' => $reading,
                                'writing' => $writing,
                                'speaking' => $speaking,
                                'overall_score' => $overallScore,
                                'proficiency_level' => $proficiencyResult['level'],
                                'proficiency_points' => $proficiencyResult['points'],
                                'test_date' => $formattedTestDate,
                                'test_reference_no' => $testReferenceNo,
                                'relevant_test' => $relevantTest
                            ]);
                        }
                    }
                }
            }

            // Log activity for English test scores update
            $testScoreCount = isset($requestData['test_type_hidden']) && is_array($requestData['test_type_hidden'])
                ? count(array_filter($requestData['test_type_hidden'], function($testType) {
                    return !empty($testType);
                }))
                : 0;
            $this->logClientActivity(
                $client->id,
                'updated English test scores',
                "Updated {$testScoreCount} test score record(s)",
                'activity'
            );

            return response()->json([
                'success' => true,
                'message' => 'Test score information saved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving test score information: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveRelatedFilesInfoSection($request, $client)
    {
        try {
            $relatedFiles = $request->input('related_files', []);
            
            // Log what we received
            \Log::info('Save related files received data', [
                'clientId' => $client->id,
                'rawRelatedFiles' => $relatedFiles,
                'allRequestData' => $request->all()
            ]);
            
            // Convert array to comma-separated string
            $relatedFilesString = '';
            if (!empty($relatedFiles) && is_array($relatedFiles)) {
                // Filter out empty values and trim
                $relatedFiles = array_filter(
                    array_map('trim', $relatedFiles),
                    function($id) {
                        return !empty($id);
                    }
                );
                $relatedFilesString = implode(',', $relatedFiles);
            }

            // Log what we're saving
            \Log::info('Saving related files', [
                'clientId' => $client->id,
                'relatedFilesArray' => $relatedFiles,
                'relatedFilesString' => $relatedFilesString
            ]);

            // Handle bidirectional relationships BEFORE updating current client
            $this->updateBidirectionalRelatedFiles($client->id, $relatedFiles);

            // Update the client's related_files field AFTER handling bidirectional relationships
            $client->related_files = $relatedFilesString;
            $client->save();

            // Log activity for related files update
            $relatedFilesCount = !empty($relatedFiles) ? count($relatedFiles) : 0;
            $this->logClientActivity(
                $client->id,
                'updated related files',
                "Updated related files: {$relatedFilesCount} file(s) linked",
                'activity'
            );

            return response()->json([
                'success' => true,
                'message' => 'Related files saved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in saveRelatedFilesInfoSection: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving related files: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateBidirectionalRelatedFiles($currentClientId, $newRelatedFileIds)
    {
        try {
            // Get current related files from database BEFORE any updates
            $currentClient = Admin::find($currentClientId);
            $currentRelatedFiles = [];
            
            if ($currentClient && !empty($currentClient->related_files)) {
                // Split by comma and filter out empty values
                $currentRelatedFiles = array_filter(
                    array_map('trim', explode(',', $currentClient->related_files)),
                    function($id) {
                        return !empty($id);
                    }
                );
            }

            // Convert new related file IDs to strings and filter empty values
            $newRelatedFileIds = array_filter(
                array_map('strval', $newRelatedFileIds),
                function($id) {
                    return !empty($id);
                }
            );

            // Convert current related files to strings for comparison
            $currentRelatedFiles = array_map('strval', $currentRelatedFiles);

            // Find files that were removed (exist in current but not in new)
            $removedFiles = array_diff($currentRelatedFiles, $newRelatedFileIds);

            // Log for debugging
            \Log::info('Bidirectional update debug', [
                'currentClientId' => $currentClientId,
                'currentRelatedFiles' => $currentRelatedFiles,
                'newRelatedFileIds' => $newRelatedFileIds,
                'removedFiles' => $removedFiles,
                'example_scenario' => 'If Client A (36464) removes Client B (36465), Client B should remove Client A'
            ]);

            // Remove current client from removed files' related_files
            foreach ($removedFiles as $removedFileId) {
                if (!empty($removedFileId)) {
                    $relatedClient = Admin::find($removedFileId);
                    if ($relatedClient) {
                        $relatedFiles = [];
                        if (!empty($relatedClient->related_files)) {
                            // Split and filter existing related files
                            $relatedFiles = array_filter(
                                array_map('trim', explode(',', $relatedClient->related_files)),
                                function($id) {
                                    return !empty($id);
                                }
                            );
                        }
                        
                        // Remove current client from the list
                        $relatedFiles = array_filter($relatedFiles, function($id) use ($currentClientId) {
                            return $id != $currentClientId;
                        });
                        
                        // Update the related client's related_files
                        $relatedClient->related_files = implode(',', $relatedFiles);
                        $relatedClient->save();
                        
                        \Log::info('Removed client from related files', [
                            'relatedClientId' => $removedFileId,
                            'removedClientId' => $currentClientId,
                            'newRelatedFiles' => $relatedClient->related_files
                        ]);
                    }
                }
            }

            // Add current client to new related files
            foreach ($newRelatedFileIds as $relatedFileId) {
                if (!empty($relatedFileId)) {
                    $relatedClient = Admin::find($relatedFileId);
                    if ($relatedClient) {
                        $existingRelatedFiles = [];
                        if (!empty($relatedClient->related_files)) {
                            // Split and filter existing related files
                            $existingRelatedFiles = array_filter(
                                array_map('trim', explode(',', $relatedClient->related_files)),
                                function($id) {
                                    return !empty($id);
                                }
                            );
                        }
                        
                        // Add current client if not already present
                        if (!in_array($currentClientId, $existingRelatedFiles)) {
                            $existingRelatedFiles[] = $currentClientId;
                            $relatedClient->related_files = implode(',', $existingRelatedFiles);
                            $relatedClient->save();
                            
                            \Log::info('Added client to related files', [
                                'relatedClientId' => $relatedFileId,
                                'addedClientId' => $currentClientId,
                                'newRelatedFiles' => $relatedClient->related_files
                            ]);
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            \Log::error('Error updating bidirectional related files: ' . $e->getMessage());
        }
    }

    // Test method to debug specific scenario
    public function testBidirectionalRemoval(Request $request)
    {
        try {
            $clientAId = $request->input('client_a_id', '36464');
            $clientBId = $request->input('client_b_id', '36465');
            
            // Get current state
            $clientA = Admin::find($clientAId);
            $clientB = Admin::find($clientBId);
            
            $result = [
                'client_a' => [
                    'id' => $clientAId,
                    'name' => $clientA ? $clientA->first_name . ' ' . $clientA->last_name : 'Not found',
                    'related_files' => $clientA ? $clientA->related_files : 'Not found'
                ],
                'client_b' => [
                    'id' => $clientBId,
                    'name' => $clientB ? $clientB->first_name . ' ' . $clientB->last_name : 'Not found',
                    'related_files' => $clientB ? $clientB->related_files : 'Not found'
                ]
            ];
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Save Parents Information Section
     */
    public function saveParentsInfoSection(Request $request)
    {
        try {
            $clientId = $request->input('id'); // Use 'id' instead of 'client_id' - 'id' is the database ID
            $client = Admin::where('id', $clientId)->where('role', '7')->first();
            
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            $parentsData = $request->input('parents', []);
            
            // Delete existing parent relationships for this client
            ClientRelationship::where('client_id', $client->id)
                ->whereIn('relationship_type', ['Father', 'Mother', 'Step Father', 'Step Mother'])
                ->delete();

            $parentsCount = 0;
            foreach ($parentsData as $parentData) {
                if (empty($parentData['relationship_type'])) {
                    continue;
                }
                $parentsCount++;

                $saveExtraFields = empty($parentData['details']) || trim($parentData['details']) === '';
                
                // Format DOB from d/m/Y to Y-m-d
                $dobFormatted = null;
                if ($saveExtraFields && !empty($parentData['dob']) && $parentData['dob'] !== 'dd/mm/yyyy') {
                    try {
                        $dobDate = \Carbon\Carbon::createFromFormat('d/m/Y', $parentData['dob']);
                        $dobFormatted = $dobDate->format('Y-m-d');
                    } catch (\Exception $e) {
                        \Log::warning('Invalid DOB format for parent: ' . $parentData['dob']);
                        $dobFormatted = null;
                    }
                }

                // For existing clients (details not empty), use details as-is
                // For new clients (details empty), construct details from first_name, last_name, email, phone
                $primaryDetails = '';
                if (!$saveExtraFields) {
                    // Existing client - use details as-is
                    $primaryDetails = $parentData['details'];
                } else {
                    // New client - construct details from individual fields
                    $detailsParts = [];
                    if (!empty($parentData['first_name'])) $detailsParts[] = trim($parentData['first_name']);
                    if (!empty($parentData['last_name'])) $detailsParts[] = trim($parentData['last_name']);
                    if (!empty($parentData['email'])) $detailsParts[] = trim($parentData['email']);
                    if (!empty($parentData['phone'])) $detailsParts[] = trim($parentData['phone']);
                    $primaryDetails = implode(', ', $detailsParts);
                }

                // Save primary parent relationship
                $parentRelationship = ClientRelationship::create([
                    'admin_id' => auth()->id(),
                    'client_id' => $client->id,
                    'related_client_id' => null,
                    'relationship_type' => $parentData['relationship_type'],
                    'details' => $primaryDetails,
                    'email' => $saveExtraFields ? ($parentData['email'] ?? null) : null,
                    'first_name' => $saveExtraFields ? ($parentData['first_name'] ?? null) : null,
                    'last_name' => $saveExtraFields ? ($parentData['last_name'] ?? null) : null,
                    'phone' => $saveExtraFields ? ($parentData['phone'] ?? null) : null,
                    'dob' => $dobFormatted,
                    'gender' => $parentData['gender'] ?? null,
                    'company_type' => $parentData['company_type'] ?? null
                ]);

                // Create reciprocal relationship if related client exists
                if (!empty($parentData['details']) && trim($parentData['details']) !== '') {
                    // Try to find related client by details
                    $relatedClient = $this->findRelatedClientByDetails($parentData['details']);
                    
                    if ($relatedClient) {
                        // Update primary relationship with related client ID
                        $parentRelationship->update(['related_client_id' => $relatedClient->id]);

                        // Determine reciprocal relationship type based on parent's gender
                        $reciprocalRelationshipType = $this->getReciprocalRelationshipForParent($parentData['relationship_type'], $parentData['gender'] ?? '');
                        
                        // Check if reciprocal relationship already exists
                        $existingReciprocal = ClientRelationship::where('client_id', $relatedClient->id)
                            ->where('related_client_id', $client->id)
                            ->where('relationship_type', $reciprocalRelationshipType)
                            ->first();

                        // Create reciprocal relationship if it doesn't exist
                        if (!$existingReciprocal) {
                            ClientRelationship::create([
                                'admin_id' => auth()->id(),
                                'client_id' => $relatedClient->id,
                                'related_client_id' => $client->id,
                                'relationship_type' => $reciprocalRelationshipType,
                                'details' => $client->first_name . ' ' . $client->last_name . ' (' . $client->email . ', ' . $client->phone . ', ' . $client->client_id . ')',
                                'email' => null,
                                'first_name' => null,
                                'last_name' => null,
                                'phone' => null,
                                'dob' => null,
                                'company_type' => null, // Reciprocal entries should have null company_type
                                'gender' => $client->gender ?? null
                            ]);
                        }
                    }
                }
            }

            // Log activity for parents information update
            $this->logClientActivity(
                $client->id,
                'updated parents information',
                "Updated {$parentsCount} parent record(s)",
                'activity'
            );

            return response()->json([
                'success' => true,
                'message' => 'Parents information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving parents information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save Siblings Information Section
     */
    public function saveSiblingsInfoSection(Request $request)
    {
        try {
            $clientId = $request->input('id'); // Use 'id' instead of 'client_id' - 'id' is the database ID
            $client = Admin::where('id', $clientId)->where('role', '7')->first();
            
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            $siblingsData = $request->input('siblings', []);
            
            // Delete existing sibling relationships for this client
            ClientRelationship::where('client_id', $client->id)
                ->whereIn('relationship_type', ['Brother', 'Sister', 'Step Brother', 'Step Sister'])
                ->delete();

            $siblingsCount = 0;
            foreach ($siblingsData as $siblingData) {
                if (empty($siblingData['relationship_type'])) {
                    continue;
                }
                $siblingsCount++;

                $saveExtraFields = empty($siblingData['details']) || trim($siblingData['details']) === '';
                
                // Format DOB from d/m/Y to Y-m-d
                $dobFormatted = null;
                if ($saveExtraFields && !empty($siblingData['dob']) && $siblingData['dob'] !== 'dd/mm/yyyy') {
                    try {
                        $dobDate = \Carbon\Carbon::createFromFormat('d/m/Y', $siblingData['dob']);
                        $dobFormatted = $dobDate->format('Y-m-d');
                    } catch (\Exception $e) {
                        \Log::warning('Invalid DOB format for sibling: ' . $siblingData['dob']);
                        $dobFormatted = null;
                    }
                }

                // For existing clients (details not empty), use details as-is
                // For new clients (details empty), construct details from first_name, last_name, email, phone
                $primaryDetails = '';
                if (!$saveExtraFields) {
                    // Existing client - use details as-is
                    $primaryDetails = $siblingData['details'];
                } else {
                    // New client - construct details from individual fields
                    $detailsParts = [];
                    if (!empty($siblingData['first_name'])) $detailsParts[] = trim($siblingData['first_name']);
                    if (!empty($siblingData['last_name'])) $detailsParts[] = trim($siblingData['last_name']);
                    if (!empty($siblingData['email'])) $detailsParts[] = trim($siblingData['email']);
                    if (!empty($siblingData['phone'])) $detailsParts[] = trim($siblingData['phone']);
                    $primaryDetails = implode(', ', $detailsParts);
                }

                // Save primary sibling relationship
                $siblingRelationship = ClientRelationship::create([
                    'admin_id' => auth()->id(),
                    'client_id' => $client->id,
                    'related_client_id' => null,
                    'relationship_type' => $siblingData['relationship_type'],
                    'details' => $primaryDetails,
                    'email' => $saveExtraFields ? ($siblingData['email'] ?? null) : null,
                    'first_name' => $saveExtraFields ? ($siblingData['first_name'] ?? null) : null,
                    'last_name' => $saveExtraFields ? ($siblingData['last_name'] ?? null) : null,
                    'phone' => $saveExtraFields ? ($siblingData['phone'] ?? null) : null,
                    'dob' => $dobFormatted,
                    'gender' => $siblingData['gender'] ?? null,
                    'company_type' => $siblingData['company_type'] ?? null
                ]);

                // Create reciprocal relationship if related client exists
                if (!empty($siblingData['details']) && trim($siblingData['details']) !== '') {
                    // Try to find related client by details
                    $relatedClient = $this->findRelatedClientByDetails($siblingData['details']);
                    
                    if ($relatedClient) {
                        // Update primary relationship with related client ID
                        $siblingRelationship->update(['related_client_id' => $relatedClient->id]);

                        // Determine reciprocal relationship type based on current client's gender
                        $reciprocalRelationshipType = $this->getReciprocalRelationshipForSibling($siblingData['relationship_type'], $client->gender ?? '');
                        
                        // Check if reciprocal relationship already exists
                        $existingReciprocal = ClientRelationship::where('client_id', $relatedClient->id)
                            ->where('related_client_id', $client->id)
                            ->where('relationship_type', $reciprocalRelationshipType)
                            ->first();

                        // Create reciprocal relationship if it doesn't exist
                        if (!$existingReciprocal) {
                            ClientRelationship::create([
                                'admin_id' => auth()->id(),
                                'client_id' => $relatedClient->id,
                                'related_client_id' => $client->id,
                                'relationship_type' => $reciprocalRelationshipType,
                                'details' => $client->first_name . ' ' . $client->last_name . ' (' . $client->email . ', ' . $client->phone . ', ' . $client->client_id . ')',
                                'email' => null,
                                'first_name' => null,
                                'last_name' => null,
                                'phone' => null,
                                'dob' => null,
                                'company_type' => null, // Reciprocal entries should have null company_type
                                'gender' => $client->gender ?? null
                            ]);
                        }
                    }
                }
            }

            // Log activity for siblings information update
            $this->logClientActivity(
                $client->id,
                'updated siblings information',
                "Updated {$siblingsCount} sibling record(s)",
                'activity'
            );

            return response()->json([
                'success' => true,
                'message' => 'Siblings information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving siblings information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save Others Information Section
     */
    public function saveOthersInfoSection(Request $request)
    {
        try {
            $clientId = $request->input('id'); // Use 'id' instead of 'client_id' - 'id' is the database ID
            $client = Admin::where('id', $clientId)->where('role', '7')->first();
            
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            $othersData = $request->input('others', []);
            
            // Delete existing other relationships for this client
            ClientRelationship::where('client_id', $client->id)
                ->whereIn('relationship_type', ['Cousin', 'Friend', 'Uncle', 'Aunt', 'Grandchild', 'Granddaughter', 'Grandparent', 'Niece', 'Nephew', 'Grandfather'])
                ->delete();

            $othersCount = 0;
            foreach ($othersData as $otherData) {
                if (empty($otherData['relationship_type'])) {
                    continue;
                }
                $othersCount++;

                $saveExtraFields = empty($otherData['details']) || trim($otherData['details']) === '';
                
                // Format DOB from d/m/Y to Y-m-d
                $dobFormatted = null;
                if ($saveExtraFields && !empty($otherData['dob']) && $otherData['dob'] !== 'dd/mm/yyyy') {
                    try {
                        $dobDate = \Carbon\Carbon::createFromFormat('d/m/Y', $otherData['dob']);
                        $dobFormatted = $dobDate->format('Y-m-d');
                    } catch (\Exception $e) {
                        \Log::warning('Invalid DOB format for other: ' . $otherData['dob']);
                        $dobFormatted = null;
                    }
                }

                // For existing clients (details not empty), use details as-is
                // For new clients (details empty), construct details from first_name, last_name, email, phone
                $primaryDetails = '';
                if (!$saveExtraFields) {
                    // Existing client - use details as-is
                    $primaryDetails = $otherData['details'];
                } else {
                    // New client - construct details from individual fields
                    $detailsParts = [];
                    if (!empty($otherData['first_name'])) $detailsParts[] = trim($otherData['first_name']);
                    if (!empty($otherData['last_name'])) $detailsParts[] = trim($otherData['last_name']);
                    if (!empty($otherData['email'])) $detailsParts[] = trim($otherData['email']);
                    if (!empty($otherData['phone'])) $detailsParts[] = trim($otherData['phone']);
                    $primaryDetails = implode(', ', $detailsParts);
                }

                // Save primary other relationship
                $otherRelationship = ClientRelationship::create([
                    'admin_id' => auth()->id(),
                    'client_id' => $client->id,
                    'related_client_id' => null,
                    'relationship_type' => $otherData['relationship_type'],
                    'details' => $primaryDetails,
                    'email' => $saveExtraFields ? ($otherData['email'] ?? null) : null,
                    'first_name' => $saveExtraFields ? ($otherData['first_name'] ?? null) : null,
                    'last_name' => $saveExtraFields ? ($otherData['last_name'] ?? null) : null,
                    'phone' => $saveExtraFields ? ($otherData['phone'] ?? null) : null,
                    'dob' => $dobFormatted,
                    'gender' => $otherData['gender'] ?? null,
                    'company_type' => $otherData['company_type'] ?? null
                ]);

                // Create reciprocal relationship if related client exists
                if (!empty($otherData['details']) && trim($otherData['details']) !== '') {
                    // Try to find related client by details
                    $relatedClient = $this->findRelatedClientByDetails($otherData['details']);
                    
                    if ($relatedClient) {
                        // Update primary relationship with related client ID
                        $otherRelationship->update(['related_client_id' => $relatedClient->id]);

                        // Determine reciprocal relationship type based on other's gender
                        $reciprocalRelationshipType = $this->getReciprocalRelationshipForOther($otherData['relationship_type'], $otherData['gender'] ?? '');
                        
                        // Check if reciprocal relationship already exists
                        $existingReciprocal = ClientRelationship::where('client_id', $relatedClient->id)
                            ->where('related_client_id', $client->id)
                            ->where('relationship_type', $reciprocalRelationshipType)
                            ->first();

                        // Create reciprocal relationship if it doesn't exist
                        if (!$existingReciprocal) {
                            ClientRelationship::create([
                                'admin_id' => auth()->id(),
                                'client_id' => $relatedClient->id,
                                'related_client_id' => $client->id,
                                'relationship_type' => $reciprocalRelationshipType,
                                'details' => $client->first_name . ' ' . $client->last_name . ' (' . $client->email . ', ' . $client->phone . ', ' . $client->client_id . ')',
                                'email' => null,
                                'first_name' => null,
                                'last_name' => null,
                                'phone' => null,
                                'dob' => null,
                                'company_type' => null, // Reciprocal entries should have null company_type
                                'gender' => $client->gender ?? null
                            ]);
                        }
                    }
                }
            }

            // Log activity for others information update
            $this->logClientActivity(
                $client->id,
                'updated other relationships',
                "Updated {$othersCount} other relationship record(s)",
                'activity'
            );

            return response()->json([
                'success' => true,
                'message' => 'Others information updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving others information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find related client by details string
     * The details string contains client information like "John Doe (john@email.com, +1234567890, CLI123)"
     */
    private function findRelatedClientByDetails($details)
    {
        if (empty($details) || trim($details) === '') {
            return null;
        }

        // Extract email, phone, and client_id from the details string
        // Format: "Name (email, phone, client_id)"
        $email = null;
        $phone = null;
        $clientId = null;
        
        // Try to extract email from parentheses
        if (preg_match('/\(([^,]+),/', $details, $matches)) {
            $email = trim($matches[1]);
        }
        
        // Try to extract phone (second item in parentheses)
        if (preg_match('/\([^,]+,([^,]+),/', $details, $matches)) {
            $phone = trim($matches[1]);
        }
        
        // Try to extract client_id (third item in parentheses)
        if (preg_match('/\([^,]+,[^,]+,([^)]+)\)/', $details, $matches)) {
            $clientId = trim($matches[1]);
        }

        // Search for client by email first (most reliable)
        if ($email) {
            $client = Admin::where('role', '7')
                ->where('email', $email)
                ->first();
            if ($client) {
                return $client;
            }
        }

        // Search by client_id if email not found
        if ($clientId) {
            $client = Admin::where('role', '7')
                ->where('client_id', $clientId)
                ->first();
            if ($client) {
                return $client;
            }
        }

        // Search by phone if email and client_id not found
        if ($phone) {
            $client = Admin::where('role', '7')
                ->where('phone', $phone)
                ->first();
            if ($client) {
                return $client;
            }
        }

        // If no specific identifiers found, try to extract name and search by name
        $namePart = trim(explode('(', $details)[0]);
        $nameParts = explode(' ', $namePart);
        
        if (count($nameParts) >= 2) {
            $firstName = trim($nameParts[0]);
            $lastName = trim($nameParts[1]);
            
            $client = Admin::where('role', '7')
                ->where('first_name', $firstName)
                ->where('last_name', $lastName)
                ->first();
            if ($client) {
                return $client;
            }
        }

        return null;
    }

    /**
     * Get reciprocal relationship for parent
     */
    private function getReciprocalRelationshipForParent($parentRelationship, $parentGender)
    {
        switch ($parentRelationship) {
            case 'Father':
                return $parentGender === 'Female' ? 'Daughter' : 'Son';
            case 'Mother':
                return $parentGender === 'Female' ? 'Daughter' : 'Son';
            case 'Step Father':
                return $parentGender === 'Female' ? 'Step Daughter' : 'Step Son';
            case 'Step Mother':
                return $parentGender === 'Female' ? 'Step Daughter' : 'Step Son';
            default:
                return 'Child';
        }
    }

    /**
     * Get reciprocal relationship for sibling
     * If client has a Brother, the reciprocal is Brother (if client is Male) or Sister (if client is Female)
     * If client has a Sister, the reciprocal is Sister (if client is Female) or Brother (if client is Male)
     */
    private function getReciprocalRelationshipForSibling($siblingRelationship, $clientGender)
    {
        switch ($siblingRelationship) {
            case 'Brother':
                return $clientGender === 'Female' ? 'Sister' : 'Brother';
            case 'Sister':
                return $clientGender === 'Female' ? 'Sister' : 'Brother';
            case 'Step Brother':
                return $clientGender === 'Female' ? 'Step Sister' : 'Step Brother';
            case 'Step Sister':
                return $clientGender === 'Female' ? 'Step Sister' : 'Step Brother';
            default:
                return 'Sibling';
        }
    }

    /**
     * Get reciprocal relationship for other
     */
    private function getReciprocalRelationshipForOther($otherRelationship, $otherGender)
    {
        switch ($otherRelationship) {
            case 'Uncle':
                return $otherGender === 'Female' ? 'Niece' : 'Nephew';
            case 'Aunt':
                return $otherGender === 'Female' ? 'Niece' : 'Nephew';
            case 'Niece':
                return $otherGender === 'Female' ? 'Aunt' : 'Uncle';
            case 'Nephew':
                return $otherGender === 'Female' ? 'Aunt' : 'Uncle';
            case 'Grandfather':
                return $otherGender === 'Female' ? 'Granddaughter' : 'Grandchild';
            case 'Grandparent':
                return $otherGender === 'Female' ? 'Granddaughter' : 'Grandchild';
            case 'Grandchild':
                return $otherGender === 'Female' ? 'Grandmother' : 'Grandfather';
            case 'Granddaughter':
                return $otherGender === 'Female' ? 'Grandmother' : 'Grandfather';
            case 'Cousin':
                return 'Cousin';
            case 'Friend':
                return 'Friend';
            default:
                return 'Other';
        }
    }
}
