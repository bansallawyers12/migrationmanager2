<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

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
}
