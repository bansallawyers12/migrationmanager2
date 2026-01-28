<?php

namespace App\Http\Controllers\API;

use App\Models\BookingAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Carbon\Carbon;

class ClientPortalAppointmentController extends BaseController
{
    /**
     * Get appointment variable lists
     * 
     * Returns all appointment-related options including locations, meeting types,
     * preferred languages, services, and service types.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppointmentVariableLists(Request $request)
    {
        try {
            $result = [
                'location' => [
                    [
                        'id' => 1,
                        'name' => 'Adelaide Office',
                        'address' => 'Unit 5, 55 Gawler Pl',
                        'city' => 'Adelaide',
                        'state' => 'SA',
                        'postcode' => '5000',
                        'full_address' => 'Unit 5, 55 Gawler Pl Adelaide SA 5000'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Melbourne Office',
                        'address' => 'Level 8/278 Collins St',
                        'city' => 'Melbourne',
                        'state' => 'VIC',
                        'postcode' => '3000',
                        'full_address' => 'Level 8/278 Collins St Melbourne VIC 3000'
                    ]
                ],
                'meeting_type' => [
                    [
                        'id' => 1,
                        'name' => 'Phone Call',
                        'description' => 'Speak directly with our experts',
                        'icon' => 'phone'
                    ],
                    [
                        'id' => 2,
                        'name' => 'In Person',
                        'description' => 'Visit our office',
                        'icon' => 'building'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Video Call',
                        'description' => 'Online consultation',
                        'note' => 'Available for paid appointments only',
                        'icon' => 'video'
                    ]
                ],
                'preferred_language' => [
                    [
                        'id' => 1,
                        'code' => 'en',
                        'name' => 'English',
                        'country_code' => 'AU',
                        'country_flag' => '🇦🇺'
                    ],
                    [
                        'id' => 2,
                        'code' => 'hi',
                        'name' => 'Hindi',
                        'country_code' => 'IN',
                        'country_flag' => '🇮🇳'
                    ],
                    [
                        'id' => 3,
                        'code' => 'pa',
                        'name' => 'Punjabi',
                        'country_code' => 'IN',
                        'country_flag' => '🇮🇳'
                    ]
                ],
                'select_your_service' => [
                    [
                        'id' => 1,
                        'name' => 'Permanent Residency Appointment'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Temporary Residency Appointment'
                    ],
                    [
                        'id' => 3,
                        'name' => 'JRP/Skill Assessment'
                    ],
                    [
                        'id' => 4,
                        'name' => 'Tourist Visa'
                    ],
                    [
                        'id' => 5,
                        'name' => 'Education/Course Change/Student Visa/Student Dependent Visa (for education selection only)'
                    ],
                    [
                        'id' => 6,
                        'name' => 'Complex matters: ART, Protection visa, Federal Case'
                    ],
                    [
                        'id' => 7,
                        'name' => 'Visa Cancellation/ NOICC/ Visa refusals'
                    ],
                    [
                        'id' => 8,
                        'name' => 'INDIA/UK/CANADA/EUROPE TO AUSTRALIA'
                    ]
                ],
                'service_type' => [
                    [
                        'id' => 1,
                        'name' => 'Free Consultation',
                        'price' => 0,
                        'price_display' => 'FREE',
                        'duration' => 15,
                        'duration_unit' => 'minutes',
                        'time_slots' => [
                            'start_time' => '10:45',
                            'end_time' => '16:00',
                            'time_format' => 'AM/PM'
                        ],
                        'availability' => [
                            'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                            'time_slots' => '15-minute time slots'
                        ],
                        'description' => 'Perfect for initial inquiries: Quick assessment of your immigration situation, basic visa pathway guidance, and preliminary advice. Available for clients currently within Australia only. Includes initial case evaluation and next steps recommendation.',
                        'includes_video_call' => false,
                        'available_for_overseas' => false
                    ],
                    [
                        'id' => 2,
                        'name' => 'Comprehensive Migration Advice',
                        'price' => 150,
                        'price_display' => '$150',
                        'duration' => 30,
                        'duration_unit' => 'minutes',
                        'time_slots' => [
                            'start_time' => '09:00',
                            'end_time' => '17:00',
                            'time_format' => 'AM/PM'
                        ],
                        'availability' => [
                            'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                            'time_slots' => '30-minute time slots'
                        ],
                        'description' => 'In-depth professional consultation: Comprehensive case analysis, detailed migration strategy, complex visa applications, ART appeals, visa cancellations, protection visas, and personalized action plans. Suitable for overseas applicants and complex cases.',
                        'includes_video_call' => true,
                        'available_for_overseas' => true
                    ],
                    [
                        'id' => 3,
                        'name' => 'Overseas Applicant Enquiry',
                        'price' => 150,
                        'price_display' => '$150',
                        'duration' => 30,
                        'duration_unit' => 'minutes',
                        'time_slots' => [
                            'start_time' => '09:00',
                            'end_time' => '17:00',
                            'time_format' => 'AM/PM'
                        ],
                        'availability' => [
                            'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                            'time_slots' => '30-minute time slots'
                        ],
                        'description' => 'Specialized consultation for overseas applicants: For applicants currently outside Australia or inquiring on behalf of someone overseas. Includes detailed assessment and personalized migration strategy.',
                        'includes_video_call' => true,
                        'available_for_overseas' => true
                    ]
                ]
            ];

            return $this->sendResponse($result, 'Appointment variable lists retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get appointment list for authenticated client
     * 
     * Returns paginated list of appointments for the authenticated client.
     * Supports filtering by status, date range, location, and enquiry type.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppointmentList(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->sendError('Unauthenticated', [], 401);
            }

            $clientId = $user->id;

            // Build query for client's appointments
            $query = BookingAppointment::where('client_id', $clientId);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('appointment_datetime', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('appointment_datetime', '<=', $request->end_date);
            }

            if ($request->filled('location')) {
                $query->where('location', $request->location);
            }

            if ($request->filled('enquiry_type')) {
                $query->where('enquiry_type', $request->enquiry_type);
            }

            // Pagination
            $perPage = min((int) $request->input('per_page', 50), 100); // Max 100 per page
            $page = (int) $request->input('page', 1);

            // Order by appointment datetime (newest first)
            $query->orderByDesc('appointment_datetime');

            $appointments = $query->paginate($perPage, ['*'], 'page', $page);

            // Format response
            $formattedAppointments = $appointments->map(function ($appointment) {
                return $this->formatAppointmentData($appointment);
            });

            $result = [
                'data' => $formattedAppointments,
                'pagination' => [
                    'current_page' => $appointments->currentPage(),
                    'per_page' => $appointments->perPage(),
                    'total' => $appointments->total(),
                    'last_page' => $appointments->lastPage(),
                    'from' => $appointments->firstItem(),
                    'to' => $appointments->lastItem(),
                ]
            ];

            return $this->sendResponse($result, 'Appointments retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Get Appointment List API Error: ' . $e->getMessage(), [
                'user_id' => $request->user()->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get single appointment by ID
     * 
     * Returns a single appointment details for the authenticated client.
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSingleAppointment(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->sendError('Unauthenticated', [], 401);
            }

            $clientId = $user->id;

            // Find appointment that belongs to the authenticated client
            $appointment = BookingAppointment::where('id', $id)
                ->where('client_id', $clientId)
                ->first();

            if (!$appointment) {
                return $this->sendError('Appointment not found', [], 404);
            }

            $result = $this->formatAppointmentData($appointment);

            return $this->sendResponse($result, 'Appointment retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Get Single Appointment API Error: ' . $e->getMessage(), [
                'user_id' => $request->user()->id ?? null,
                'appointment_id' => $id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Format appointment data for API response
     * 
     * @param BookingAppointment $appointment
     * @return array
     */
    private function formatAppointmentData($appointment)
    {
        // Map status to display format
        $statusDisplay = match($appointment->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
            'rescheduled' => 'Rescheduled',
            default => ucfirst($appointment->status ?? 'Pending')
        };

        // Map enquiry type to display format
        $enquiryTypeDisplay = match($appointment->enquiry_type) {
            'pr' => 'Permanent Residency',
            'tr' => 'Temporary Residency',
            'tourist' => 'Tourist Visa',
            'education' => 'Education/Student Visa',
            'pr_complex' => 'PR/Complex',
            'jrp' => 'JRP/Skill Assessment',
            'visa_cancellation' => 'Visa Cancellation/NOICC/Refusals',
            'india_uk_canada_europe' => 'INDIA/UK/CANADA/EUROPE TO AUSTRALIA',
            default => ucfirst($appointment->enquiry_type ?? 'General')
        };

        // Format meeting type
        $meetingTypeDisplay = match($appointment->meeting_type) {
            'in_person' => 'In Person',
            'phone' => 'Phone Call',
            'video' => 'Video Call',
            default => ucfirst($appointment->meeting_type ?? 'In Person')
        };

        return [
            'id' => $appointment->id,
            'full_name' => $appointment->client_name,
            'email' => $appointment->client_email,
            'phone' => $appointment->client_phone,
            'location' => $appointment->location,
            'meeting_type' => $appointment->meeting_type,
            'meeting_type_display' => $meetingTypeDisplay,
            'preferred_language' => strtolower($appointment->preferred_language ?? 'english'),
            'enquiry_type' => $appointment->enquiry_type,
            'enquiry_type_display' => $enquiryTypeDisplay,
            'service_type' => $appointment->service_type,
            'specific_service' => $appointment->service_type ?? 'consultation',
            'enquiry_details' => $appointment->enquiry_details,
            'appointment_date' => $appointment->appointment_datetime ? $appointment->appointment_datetime->format('Y-m-d') : null,
            'appointment_time' => $appointment->appointment_datetime ? $appointment->appointment_datetime->format('H:i:s') : null,
            'appointment_datetime' => $appointment->appointment_datetime ? $appointment->appointment_datetime->toIso8601String() : null,
            'duration_minutes' => $appointment->duration_minutes ?? 15,
            'status' => $appointment->status,
            'status_display' => $statusDisplay,
            'is_paid' => $appointment->is_paid ?? false,
            'amount' => number_format((float)($appointment->amount ?? 0), 2, '.', ''),
            'discount_amount' => number_format((float)($appointment->discount_amount ?? 0), 2, '.', ''),
            'final_amount' => number_format((float)($appointment->final_amount ?? 0), 2, '.', ''),
            'promo_code' => $appointment->promo_code,
            'assigned_admin' => $appointment->consultant ? [
                'id' => $appointment->consultant->id,
                'name' => $appointment->consultant->name ?? null,
            ] : null,
            'payment' => $appointment->payment_status ?? null,
            'admin_notes' => $appointment->admin_notes,
            'client_notes' => null, // Add if this field exists in the table
            'confirmed_at' => $appointment->confirmed_at ? $appointment->confirmed_at->toIso8601String() : null,
            'cancelled_at' => $appointment->cancelled_at ? $appointment->cancelled_at->toIso8601String() : null,
            'cancellation_reason' => $appointment->cancellation_reason,
            'created_at' => $appointment->created_at ? $appointment->created_at->toIso8601String() : null,
            'updated_at' => $appointment->updated_at ? $appointment->updated_at->toIso8601String() : null,
        ];
    }

    /**
     * Add new appointment
     * 
     * Creates a new appointment for the authenticated client.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAppointment(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->sendError('Unauthenticated', [], 401);
            }

            $clientId = $user->id;

            // Get client information from authenticated user
            $clientName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            $clientEmail = $user->email ?? '';
            $clientPhone = $user->phone ?? null;

            // Validate that client has required information
            if (empty($clientName)) {
                return $this->sendError('Client name is missing. Please update your profile.', [], 422);
            }
            if (empty($clientEmail)) {
                return $this->sendError('Client email is missing. Please update your profile.', [], 422);
            }

            // Validate input
            $validated = $request->validate([
                'location' => 'required|in:melbourne,adelaide',
                'meeting_type' => 'required|string',
                'preferred_language' => 'required|string|max:50',
                'enquiry_type' => 'nullable|string|max:255',
                'service_type' => 'nullable|string|max:255',
                'specific_service' => 'nullable|string|max:255',
                'enquiry_details' => 'nullable|string',
                'appointment_date' => 'required|date|date_format:Y-m-d',
                'appointment_time' => 'required|string',
                'is_paid' => 'required|boolean',
            ]);

            // Map meeting type (handle various formats)
            $meetingType = $this->mapMeetingType($validated['meeting_type']);

            // Validate: Video meeting type is only allowed for paid appointments
            if ($meetingType === 'video' && !$validated['is_paid']) {
                return $this->sendError('Video call appointments are only available for paid services', [], 422);
            }

            // Determine duration based on service type or default to 15 minutes
            $durationMinutes = 15; // Default
            if ($validated['is_paid']) {
                $durationMinutes = 30; // Paid appointments are typically 30 minutes
            }

            // Determine service_id (1 = Paid, 2 = Free)
            $serviceId = $validated['is_paid'] ? 1 : 2;

            // Combine date and time into datetime
            $appointmentDateTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $validated['appointment_date'] . ' ' . $validated['appointment_time']
            );

            // Validate appointment is in the future
            if ($appointmentDateTime->isPast()) {
                return $this->sendError('Appointment date and time must be in the future', [], 422);
            }

            // Determine status based on payment
            $status = $validated['is_paid'] ? 'pending' : 'confirmed';
            $confirmedAt = $validated['is_paid'] ? null : now();

            // Determine amount based on is_paid
            $amount = $validated['is_paid'] ? 150.00 : 0.00;
            $finalAmount = $amount;

            // Map inperson_address (1 = Adelaide, 2 = Melbourne)
            $inpersonAddress = $validated['location'] === 'adelaide' ? 1 : 2;

            // Generate unique bansal_appointment_id (temporary, can be updated when synced)
            // Use timestamp-based ID to ensure uniqueness
            $bansalAppointmentId = time() * 1000 + rand(100, 999);

            // Create appointment
            $appointment = BookingAppointment::create([
                'bansal_appointment_id' => $bansalAppointmentId, // Temporary unique ID, can be updated when synced with Bansal API
                'order_hash' => null,
                
                'client_id' => $clientId,
                'consultant_id' => null, // Can be assigned later
                'assigned_by_admin_id' => null,
                
                'client_name' => $clientName,
                'client_email' => $clientEmail,
                'client_phone' => $clientPhone,
                'client_timezone' => 'Australia/Melbourne',
                
                'appointment_datetime' => $appointmentDateTime,
                'timeslot_full' => $appointmentDateTime->format('h:i A'),
                'duration_minutes' => $durationMinutes,
                'location' => $validated['location'],
                'inperson_address' => $inpersonAddress,
                'meeting_type' => $meetingType,
                'preferred_language' => $validated['preferred_language'],
                
                'service_id' => $serviceId,
                'noe_id' => null, // Can be set based on enquiry_type if needed
                'enquiry_type' => $validated['enquiry_type'] ?? null,
                'service_type' => $validated['service_type'] ?? null,
                'enquiry_details' => $validated['enquiry_details'] ?? null,
                
                'status' => $status,
                'confirmed_at' => $confirmedAt,
                
                'is_paid' => $validated['is_paid'],
                'amount' => $amount,
                'discount_amount' => 0.00,
                'final_amount' => $finalAmount,
                'promo_code' => null,
                'payment_status' => $validated['is_paid'] ? 'pending' : null,
                'payment_method' => null,
                'paid_at' => null,
                
                'admin_notes' => null,
                'follow_up_required' => false,
                'follow_up_date' => null,
                'confirmation_email_sent' => false,
                'confirmation_email_sent_at' => null,
                'reminder_sms_sent' => false,
                'reminder_sms_sent_at' => null,
                
                'synced_from_bansal_at' => null,
                'last_synced_at' => null,
                'sync_status' => 'new',
                'sync_error' => null,
                'slot_overwrite_hidden' => 0, // Always set to 0 automatically
                'user_id' => $clientId,
            ]);

            // Format and return the created appointment
            $result = $this->formatAppointmentData($appointment);

            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully',
                'data' => $result
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->sendError('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('Add Appointment API Error: ' . $e->getMessage(), [
                'user_id' => $request->user()->id ?? null,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Map meeting type from various formats to database format
     * 
     * @param string $meetingType
     * @return string
     */
    /**
     * Get disabled dates from calendar
     * 
     * Returns disabled dates array along with duration, start_time, end_time, and weeks
     * for the specified service type and location. Uses slot_overwrite=0 automatically.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDisabledDateFromCalendar(Request $request)
    {
        try {
            // Get input parameters
            $id = $request->input('id'); // 1=>consultation, 2=>paid-consultation, 3=>overseas-enquiry
            $enquiry_item = $request->input('enquiry_item'); // 1=>permanent-residency, 2=>temporary-residency, etc.
            $inperson_address = $request->input('inperson_address'); // 1=>Adelaide, 2=>melbourne
            $slot_overwrite = 0; // Always set to 0, not passed in input
            
            // Validate required parameters
            if (empty($id) || empty($enquiry_item) || empty($inperson_address)) {
                return $this->sendError('Missing required parameters: id, enquiry_item, and inperson_address are required', [], 422);
            }
            
            Log::info('getDisabledDateFromCalendar called', [
                'id' => $id,
                'enquiry_item' => $enquiry_item,
                'inperson_address' => $inperson_address,
                'slot_overwrite' => $slot_overwrite
            ]);
            
            // Map id to specific_service
            $specific_service_map = [
                1 => 'consultation',
                2 => 'paid-consultation',
                3 => 'overseas-enquiry'
            ];
            $specific_service = $specific_service_map[$id] ?? 'consultation';
            
            // Map enquiry_item to service_type
            $service_type_map = [
                1 => 'permanent-residency',
                2 => 'temporary-residency',
                3 => 'jrp-skill-assessment',
                4 => 'tourist-visa',
                5 => 'education-visa',
                6 => 'complex-matters',
                7 => 'visa-cancellation',
                8 => 'international-migration'
            ];
            $service_type = $service_type_map[$enquiry_item] ?? 'permanent-residency';
            
            // Map inperson_address to location
            $location_map = [
                1 => 'adelaide',
                2 => 'melbourne'
            ];
            $location = $location_map[$inperson_address] ?? 'adelaide';
            
            // Prepare request data for external API
            $requestData = [
                'specific_service' => $specific_service,
                'service_type' => $service_type,
                'location' => $location,
                'slot_overwrite' => $slot_overwrite
            ];
            
            try {
                // Get API configuration
                $baseUrl = 'https://www.bansalimmigration.com.au/api/crm';
                $apiToken = config('services.bansal_api.token');
                $timeout = config('services.bansal_api.timeout', 30);
                
                if (empty($apiToken)) {
                    Log::error('Bansal API token not configured');
                    return $this->sendError('Bansal API token not configured. Set BANSAL_API_TOKEN in .env', [], 500);
                }
                
                // Make API call to external Bansal API
                $response = Http::timeout($timeout)
                    ->withToken($apiToken)
                    ->acceptJson()
                    ->post("{$baseUrl}/appointments/get-datetime-backend", $requestData);
                
                if ($response->failed()) {
                    Log::error('Bansal API get-datetime-backend Error', [
                        'method' => 'getDisabledDateFromCalendar',
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'request_data' => $requestData
                    ]);
                    
                    return $this->sendError('Failed to fetch datetime backend from external API', [
                        'error' => $response->status() === 404 ? 'Endpoint not found' : 'API request failed'
                    ], $response->status());
                }
                
                $data = $response->json();
                
                // Format response to match expected output
                $result = [
                    'success' => $data['success'] ?? true,
                    'disabledatesarray' => $data['disabledatesarray'] ?? [],
                    'duration' => $data['duration'] ?? 15,
                    'start_time' => $data['start_time'] ?? '10:45',
                    'end_time' => $data['end_time'] ?? '16:00',
                    'weeks' => $data['weeks'] ?? []
                ];
                
                return $this->sendResponse($result, 'Disabled dates retrieved successfully');
                
            } catch (RequestException $e) {
                $response = $e->response;
                $responseBody = $response?->json();
                $message = null;
                
                if (is_array($responseBody)) {
                    $message = $responseBody['message']
                        ?? ($responseBody['error']['message'] ?? null);
                }
                
                $message = $message ?: $response?->body() ?: $e->getMessage();
                
                Log::error('Bansal API get-datetime-backend Request Error', [
                    'method' => 'getDisabledDateFromCalendar',
                    'message' => $message,
                    'request_data' => $requestData,
                    'exception' => $e->getMessage()
                ]);
                
                return $this->sendError('API request failed: ' . $message, [], 500);
            } catch (\Exception $e) {
                Log::error('Bansal API get-datetime-backend Exception', [
                    'method' => 'getDisabledDateFromCalendar',
                    'message' => $e->getMessage(),
                    'request_data' => $requestData,
                    'trace' => $e->getTraceAsString()
                ]);
                
                return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('getDisabledDateFromCalendar Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get disabled time slots for a specific date from calendar
     * 
     * Returns disabled time slots array for the specified date, service type and location.
     * Uses slot_overwrite=0 automatically.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDisabledSlotsOfAnyDateFromCalendar(Request $request)
    {
        try {
            // Get input parameters
            $service_id = $request->input('service_id'); // 1=>consultation, 2=>paid-consultation, 3=>overseas-enquiry
            $enquiry_item = $request->input('enquiry_item'); // 1=>permanent-residency, 2=>temporary-residency, etc.
            $inperson_address = $request->input('inperson_address'); // 1=>adelaide, 2=>melbourne
            $sel_date = $request->input('sel_date'); // Date in dd/mm/yyyy format
            $slot_overwrite = 0; // Always set to 0, not passed in input
            
            // Validate required parameters
            if (empty($service_id) || empty($enquiry_item) || empty($inperson_address) || empty($sel_date)) {
                return $this->sendError('Missing required parameters: service_id, enquiry_item, inperson_address, and sel_date are required', [], 422);
            }
            
            // Validate date format (dd/mm/yyyy)
            if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $sel_date)) {
                return $this->sendError('Invalid date format. Date must be in dd/mm/yyyy format', [], 422);
            }
            
            Log::info('getDisabledSlotsOfAnyDateFromCalendar called', [
                'service_id' => $service_id,
                'enquiry_item' => $enquiry_item,
                'inperson_address' => $inperson_address,
                'sel_date' => $sel_date,
                'slot_overwrite' => $slot_overwrite
            ]);
            
            // Map service_id to specific_service
            $specific_service_map = [
                1 => 'consultation',
                2 => 'paid-consultation',
                3 => 'overseas-enquiry'
            ];
            $specific_service = $specific_service_map[$service_id] ?? 'consultation';
            
            // Map enquiry_item to service_type
            $service_type_map = [
                1 => 'permanent-residency',
                2 => 'temporary-residency',
                3 => 'jrp-skill-assessment',
                4 => 'tourist-visa',
                5 => 'education-visa',
                6 => 'complex-matters',
                7 => 'visa-cancellation',
                8 => 'international-migration'
            ];
            $service_type = $service_type_map[$enquiry_item] ?? 'permanent-residency';
            
            // Map inperson_address to location
            $location_map = [
                1 => 'adelaide',
                2 => 'melbourne'
            ];
            $location = $location_map[$inperson_address] ?? 'adelaide';
            
            try {
                // Use BansalApiClient to call the website API (same as getdisableddatetime)
                $apiClient = new \App\Services\BansalAppointmentSync\BansalApiClient();
                $response = $apiClient->getDisabledDateTime(
                    $specific_service,
                    $service_type,
                    $location,
                    $sel_date,
                    $slot_overwrite
                );
                
                // Format response to match expected output
                $result = [
                    'success' => $response['success'] ?? true,
                    'disabledtimeslotes' => $response['disabledtimeslotes'] ?? []
                ];
                
                return $this->sendResponse($result, 'Disabled time slots retrieved successfully');
                
            } catch (\Exception $e) {
                Log::error('Bansal API get-disabled-datetime Exception', [
                    'method' => 'getDisabledSlotsOfAnyDateFromCalendar',
                    'message' => $e->getMessage(),
                    'service_id' => $service_id,
                    'enquiry_item' => $enquiry_item,
                    'inperson_address' => $inperson_address,
                    'sel_date' => $sel_date,
                    'slot_overwrite' => $slot_overwrite,
                    'trace' => $e->getTraceAsString()
                ]);
                
                return $this->sendError('An error occurred: ' . $e->getMessage(), [
                    'disabledtimeslotes' => []
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('getDisabledSlotsOfAnyDateFromCalendar Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    private function mapMeetingType(string $meetingType): string
    {
        // Normalize: convert to lowercase and replace spaces/hyphens with underscores
        $normalized = strtolower(trim($meetingType));
        $normalized = str_replace([' ', '-'], '_', $normalized);

        return match($normalized) {
            'in_person', 'inperson', 'in-person', 'in person', 'office', 'onsite' => 'in_person',
            'phone', 'telephone', 'call', 'phone_call' => 'phone',
            'video', 'videocall', 'video_call', 'video-call', 'zoom', 'online' => 'video',
            default => 'in_person' // Default fallback
        };
    }
}
