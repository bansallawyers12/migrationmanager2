<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\Application;
use App\Models\ActivitiesLog;
use App\Models\ClientPortalDetailAudit;
use App\Models\ClientMatter;
use App\Models\WorkflowStage;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Broadcast;
use App\Services\FCMService;

/**
 * ClientPortalController
 * 
 * Handles all client portal functionality including:
 * - Portal user management (activating/deactivating access, sending credentials)
 * - Application operations (stages, documents, notes, messaging)
 * - Client portal tab functionality
 * 
 * Previously split between ApplicationsController and ClientPortalController,
 * now consolidated into a single controller for all client portal operations.
 */
class ClientPortalController extends Controller
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

    /**
     * Toggle Client Portal Status and Send Email
     */
    public function toggleClientPortal(Request $request)
    {
        try {
            $request->validate([
                'client_id' => 'required|integer',
                'status' => 'required|in:true,false,1,0'
            ]);

            $clientId = $request->client_id;
            $status = ($request->status === true || $request->status === 'true' || $request->status === '1' || $request->status === 1) ? 1 : 0;

            // Update the client's cp_status
            $client = \App\Models\Admin::where('id', $clientId)->where('role', '7')->first();

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            $client->cp_status = $status;
            
            // Handle password based on status
            if ($status == 1) {
                // Generate and save password when activating client portal
                $randomPassword = Str::random(12);
                $hashedPassword = Hash::make($randomPassword);
                $client->password = $hashedPassword;
            } else {
                // Clear password when deactivating client portal for security
                $client->password = null;
            }
            
            $client->save();

            // Send appropriate email based on status change
            if ($status == 1) {
                // Status is being turned ON - send activation email with password
                $this->sendClientPortalActivationEmail($client, $randomPassword);
            } else {
                // Status is being turned OFF - send deactivation email
                $this->sendClientPortalDeactivationEmail($client);
            }

            return response()->json([
                'success' => true,
                'message' => $status ? 'Client Portal activated and email sent successfully' : 'Client Portal deactivated and email sent successfully',
                'status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating client portal status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send Client Portal activation email
     */
    private function sendClientPortalActivationEmail($client, $password)
    {
        try {
            // Get client's email directly from admins table
            $emailAddress = $client->email;

            if (!$emailAddress) {
                throw new \Exception('No email address found for client');
            }

            // Email content
            $subject = 'Client Portal Access Activated - Bansal Immigration';
            $message = "
                <html>
                <body>
                    <h2>Client Portal Access Activated</h2>
                    <p>Dear {$client->first_name} {$client->last_name},</p>
                    <p>Your client portal has been activated successfully. Below are your login credentials:</p>
                    <p><strong>Email:</strong> {$client->email}</p>
                    <p><strong>Password:</strong> {$password}</p>
                    <p>You can now access your client portal using the mobile app with these credentials to view your case details.</p>
                    <p>Download the mobile app from the following link: <a href='https://play.google.com/store/apps/details?id=com.bansalimmigration.clientportal'>https://play.google.com/store/apps/details?id=com.bansalimmigration.clientportal</a></p>
                    <p><strong>Important:</strong> Please keep your login credentials secure and do not share them with anyone. After Login in mboile App you can chnage your password.</p>
                    <p>Please contact us if you have any questions.</p>
                    <br>
                    <p>Best regards,<br>Bansal Immigration Team</p>
                </body>
                </html>
            ";

            // Send email using Mail facade
            Mail::send('emails.client_portal_active_email', ['content' => $message], function($message) use ($emailAddress, $subject) {
                $message->to($emailAddress)
                       ->subject($subject)
                       ->from(config('mail.from.address'), config('mail.from.name'));
            });

        } catch (\Exception $e) {
            Log::error('Failed to send client portal activation email: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send Client Portal deactivation email
     */
    private function sendClientPortalDeactivationEmail($client)
    {
        try {
            // Get client's email directly from admins table
            $emailAddress = $client->email;

            if (!$emailAddress) {
                throw new \Exception('No email address found for client');
            }

            // Email content for deactivation
            $subject = 'Client Portal Access Deactivated - Bansal Immigration';
            $message = "
                <html>
                <body>
                    <h2>Client Portal Access Deactivated</h2>
                    <p>Dear {$client->first_name} {$client->last_name},</p>
                    <p>Your client portal access has been deactivated. Now you cannot access the client portal from mobile app.</p>
                    <p>Please contact the administrator for further assistance.</p>
                    <br>
                    <p>Best regards,<br>Bansal Immigration Team</p>
                </body>
                </html>
            ";

            // Send email using Mail facade
            Mail::send('emails.client_portal_active_email', ['content' => $message], function($message) use ($emailAddress, $subject) {
                $message->to($emailAddress)
                       ->subject($subject)
                       ->from(config('mail.from.address'), config('mail.from.name'));
            });

        } catch (\Exception $e) {
            Log::error('Failed to send client portal deactivation email: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Approve Audit Value
     * Updates the admins table with the audit value and removes the audit entry
     */
    public function approveAuditValue(Request $request)
    {
        try {
            $request->validate([
                'audit_id' => 'required|integer',
                'field_name' => 'required|string',
                'client_id' => 'required|integer',
                'client_matter_id' => 'required|integer'
            ]);

            $auditId = $request->audit_id;
            $fieldName = $request->field_name;
            $clientId = $request->client_id;
            $clientMatterId = $request->client_matter_id;

            // Get the audit entry
            $auditEntry = ClientPortalDetailAudit::find($auditId);

            if (!$auditEntry || $auditEntry->client_id != $clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audit entry not found'
                ], 404);
            }

            // Get the client
            $client = Admin::find($clientId);
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            DB::beginTransaction();

            try {
                $newValue = $auditEntry->new_value;

                // Map field names to database columns
                $fieldMapping = [
                    'first_name' => 'first_name',
                    'last_name' => 'last_name',
                    'client_id' => 'client_id',
                    'dob' => 'dob',
                    'age' => 'age',
                    'gender' => 'gender',
                    'marital_status' => 'marital_status',
                ];

                if (!isset($fieldMapping[$fieldName])) {
                    throw new \Exception('Invalid field name');
                }

                $dbField = $fieldMapping[$fieldName];

                // Update the admins table
                $client->$dbField = $newValue;
                
                // If DOB is updated, recalculate age
                if ($fieldName === 'dob' && $newValue) {
                    try {
                        $dob = \Carbon\Carbon::parse($newValue);
                        $now = \Carbon\Carbon::now();
                        $age = $dob->diff($now)->format('%y years %m months');
                        $client->age = $age;
                    } catch (\Exception $e) {
                        // Age calculation failed, continue without updating age
                    }
                }
                
                $client->save();

                // Delete the audit entry
                $auditEntry->delete();

                // If DOB is approved, also remove age audit entry since age will be recalculated
                if ($fieldName === 'dob') {
                    $ageAuditEntry = ClientPortalDetailAudit::where('client_id', $clientId)
                        ->where('meta_key', 'age')
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    
                    if ($ageAuditEntry) {
                        $ageAuditEntry->delete();
                    }
                }
                
                // If age is approved, also remove DOB audit entry since they are related
                if ($fieldName === 'age') {
                    $dobAuditEntry = ClientPortalDetailAudit::where('client_id', $clientId)
                        ->where('meta_key', 'dob')
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    
                    if ($dobAuditEntry) {
                        $dobAuditEntry->delete();
                    }
                }

                // Get field label for message
                $fieldLabels = [
                    'first_name' => 'First Name',
                    'last_name' => 'Last Name',
                    'client_id' => 'Client ID',
                    'dob' => 'Date of Birth',
                    'age' => 'Age',
                    'gender' => 'Gender',
                    'marital_status' => 'Marital Status'
                ];

                $fieldLabel = $fieldLabels[$fieldName] ?? $fieldName;

                // Get sender (admin) information
                $sender = Auth::guard('admin')->user();
                $senderId = $sender ? $sender->id : null;
                $senderName = $sender ? ($sender->first_name . ' ' . $sender->last_name) : 'Admin';

                // Create approval message
                $message = "Your Basic Detail {$fieldLabel} related changes approved by Admin. Please check at your end.";

                // Create message record
                $messageData = [
                    'message' => $message,
                    'sender' => $senderName,
                    'sender_id' => $senderId,
                    'sent_at' => now(),
                    'client_matter_id' => $clientMatterId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $messageId = DB::table('messages')->insertGetId($messageData);

                if ($messageId) {
                    // Insert recipient into pivot table
                    MessageRecipient::insert([
                        'message_id' => $messageId,
                        'recipient_id' => $clientId,
                        'recipient' => $client->first_name . ' ' . $client->last_name,
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Broadcast message via Laravel Reverb (configured via BROADCAST_DRIVER in .env)
                    if (class_exists('\App\Events\MessageSent')) {
                        $messageForBroadcast = [
                            'id' => $messageId,
                            'message' => $message,
                            'sender' => $senderName,
                            'sender_id' => $senderId,
                            'sent_at' => now()->toISOString(),
                            'created_at' => now()->toISOString(),
                            'client_matter_id' => $clientMatterId,
                            'recipients' => [[
                                'recipient_id' => $clientId,
                                'recipient' => $client->first_name . ' ' . $client->last_name
                            ]]
                        ];

                        try {
                            broadcast(new \App\Events\MessageSent($messageForBroadcast, $clientId));
                        } catch (\Exception $e) {
                            Log::warning('Failed to broadcast message to client', [
                                'client_id' => $clientId,
                                'message_id' => $messageId,
                                'error' => $e->getMessage()
                            ]);
                        }
                        
                        if ($senderId) {
                            try {
                                broadcast(new \App\Events\MessageSent($messageForBroadcast, $senderId));
                            } catch (\Exception $e) {
                                Log::warning('Failed to broadcast message to sender', [
                                    'sender_id' => $senderId,
                                    'message_id' => $messageId,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Change approved and updated successfully. Message sent to client.'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving change: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject Audit Value
     * Removes the audit entry and sends a message to the client
     */
    public function rejectAuditValue(Request $request)
    {
        try {
            $request->validate([
                'audit_id' => 'required|integer',
                'field_name' => 'required|string',
                'client_id' => 'required|integer',
                'client_matter_id' => 'required|integer'
            ]);

            $auditId = $request->audit_id;
            $fieldName = $request->field_name;
            $clientId = $request->client_id;
            $clientMatterId = $request->client_matter_id;

            // Get the audit entry
            $auditEntry = ClientPortalDetailAudit::find($auditId);

            if (!$auditEntry || $auditEntry->client_id != $clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audit entry not found'
                ], 404);
            }

            // Get field label for message
            $fieldLabels = [
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'client_id' => 'Client ID',
                'dob' => 'Date of Birth',
                'age' => 'Age',
                'gender' => 'Gender',
                'marital_status' => 'Marital Status'
            ];

            $fieldLabel = $fieldLabels[$fieldName] ?? $fieldName;

            DB::beginTransaction();

            try {
                // Delete the audit entry
                $auditEntry->delete();

                // If DOB is rejected, also remove age audit entry since age depends on DOB
                if ($fieldName === 'dob') {
                    $ageAuditEntry = ClientPortalDetailAudit::where('client_id', $clientId)
                        ->where('meta_key', 'age')
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    
                    if ($ageAuditEntry) {
                        $ageAuditEntry->delete();
                    }
                }
                
                // If age is rejected, also remove DOB audit entry since they are related
                if ($fieldName === 'age') {
                    $dobAuditEntry = ClientPortalDetailAudit::where('client_id', $clientId)
                        ->where('meta_key', 'dob')
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    
                    if ($dobAuditEntry) {
                        $dobAuditEntry->delete();
                    }
                }

                // Get sender (admin) information
                $sender = Auth::guard('admin')->user();
                $senderId = $sender ? $sender->id : null;
                $senderName = $sender ? ($sender->first_name . ' ' . $sender->last_name) : 'Admin';

                // Get client information
                $client = Admin::find($clientId);
                if (!$client) {
                    throw new \Exception('Client not found');
                }

                // Create rejection message
                $message = "Your Basic Detail {$fieldLabel} related changes rejected by Admin. Please try again.";

                // Create message record
                $messageData = [
                    'message' => $message,
                    'sender' => $senderName,
                    'sender_id' => $senderId,
                    'sent_at' => now(),
                    'client_matter_id' => $clientMatterId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $messageId = DB::table('messages')->insertGetId($messageData);

                if ($messageId) {
                    // Insert recipient into pivot table
                    MessageRecipient::insert([
                        'message_id' => $messageId,
                        'recipient_id' => $clientId,
                        'recipient' => $client->first_name . ' ' . $client->last_name,
                        'is_read' => false,
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Broadcast message via Laravel Reverb (configured via BROADCAST_DRIVER in .env)
                    if (class_exists('\App\Events\MessageSent')) {
                        $messageForBroadcast = [
                            'id' => $messageId,
                            'message' => $message,
                            'sender' => $senderName,
                            'sender_id' => $senderId,
                            'sent_at' => now()->toISOString(),
                            'created_at' => now()->toISOString(),
                            'client_matter_id' => $clientMatterId,
                            'recipients' => [[
                                'recipient_id' => $clientId,
                                'recipient' => $client->first_name . ' ' . $client->last_name
                            ]]
                        ];

                        try {
                            broadcast(new \App\Events\MessageSent($messageForBroadcast, $clientId));
                        } catch (\Exception $e) {
                            Log::warning('Failed to broadcast message to client', [
                                'client_id' => $clientId,
                                'message_id' => $messageId,
                                'error' => $e->getMessage()
                            ]);
                        }
                        
                        if ($senderId) {
                            try {
                                broadcast(new \App\Events\MessageSent($messageForBroadcast, $senderId));
                            } catch (\Exception $e) {
                                Log::warning('Failed to broadcast message to sender', [
                                    'sender_id' => $senderId,
                                    'message_id' => $messageId,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Change rejected and message sent to client'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting change: ' . $e->getMessage()
            ], 500);
        }
    }

	//Load Application Insert Update Data
	public function loadApplicationInsertUpdateData(Request $request){
		// Get client_id and client_matter_id from request
		$clientId = $request->client_id;
		$clientMatterId = $request->client_matter_id;

		// get workflow stage name from 
		$workflowStage = DB::table('client_matters')
			->where('client_matters.client_id', $clientId)
			->where('client_matters.id', $clientMatterId)
			->join('workflow_stages', 'client_matters.workflow_stage_id', '=', 'workflow_stages.id')
			->select('workflow_stages.name','workflow_stages.w_id')
			->first();
		
		// Check if record exists in applications table
		$existingApplication = DB::table('applications')
			->where('client_id', $clientId)
			->where('client_matter_id', $clientMatterId)
			->first();
			
		if($existingApplication) {
			// Update existing record
			$applicationId = DB::table('applications')
				->where('client_id', $clientId)
				->where('client_matter_id', $clientMatterId)
				->update([
					'user_id' => Auth::user()->id,
					'stage' => $workflowStage->name,
					'workflow' => $workflowStage->w_id,
					'updated_at' => now()
				]);
				
			$applicationId = $existingApplication->id;
		} else {
			// Insert new record
			$applicationId = DB::table('applications')->insertGetId([
				'client_matter_id' => $clientMatterId,
				'user_id' => Auth::user()->id,
				'client_id' => $clientId,
				'stage' => $workflowStage->name,
				'workflow' => $workflowStage->w_id,
				'created_at' => now(),
				'updated_at' => now()
			]);
		}
		
		return response()->json([
			'status' => true,
			'application_id' => $applicationId,
			'message' => $existingApplication ? 'Application updated successfully' : 'Application created successfully'
		]);
	}

	public function completestage(Request $request){
		$fetchData = Application::find($request->id);
		$fetchData->status = 1;

		$saved = $fetchData->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['stage']	=	$fetchData->stage;
			$response['width']	=	100;
			$response['message']	=	'Application has been successfully completed.';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}

		echo json_encode($response);
	}
	public function updatestage(Request $request){
		$fetchData = Application::find($request->id);
		$workflowstagecount = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->count();
		$widthcount = 0;
		if($workflowstagecount !== 0){
			$s = 100 / $workflowstagecount;
			$widthcount = round($s);
		}
		//$workflowstage = \App\Models\WorkflowStage::where('name', $fetchData->stage)->where('w_id', $fetchData->workflow)->first();
		$workflowstage = \App\Models\WorkflowStage::where('name', 'like', '%'.$fetchData->stage.'%')->where('w_id', $fetchData->workflow)->first();
		$nextid = \App\Models\WorkflowStage::where('id', '>', $workflowstage->id)->where('w_id', $fetchData->workflow)->orderBy('id','asc')->first();

		$fetchData->stage = $nextid->name;
		$comments = 'moved the stage from  <b>'.$workflowstage->name.'</b> to <b>'.$nextid->name.'</b>';

		$width = $fetchData->progresswidth + $widthcount;
		$fetchData->progresswidth = $width;
		$saved = $fetchData->save();
		if($saved){
			$obj = new ActivitiesLog;
			$obj->client_id = $fetchData->client_id;
			$obj->created_by = Auth::user()->id;
			$obj->subject = 'Stage: ' . $workflowstage->name;
			$obj->description = $comments;
			$obj->activity_type = 'stage';
			$obj->use_for = 'application';
			$saved = $obj->save();
			$displayback = false;
			$workflowstage = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->orderBy('id','desc')->first();

			if($workflowstage->name == $fetchData->stage){
				$displayback = true;
			}
			$response['status'] 	= 	true;
			$response['stage']	=	$fetchData->stage;
			$response['width']	=	$width;
			$response['displaycomplete']	=	$displayback;
			$response['message']	=	'Application has been successfully moved to next stage.';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function updatebackstage(Request $request){
		$fetchData = Application::find($request->id);
		$workflowstage = \App\Models\WorkflowStage::where('name', $fetchData->stage)->where('w_id', $fetchData->workflow)->first();
		$nextid = \App\Models\WorkflowStage::where('id', '<', $workflowstage->id)->where('w_id', $fetchData->workflow)->orderBy('id','Desc')->first();
		if($nextid){
			$workflowstagecount = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->count();
			$widthcount = 0;
			if($workflowstagecount !== 0){
				$s = 100 / $workflowstagecount;
				$widthcount = round($s);
			}
			$fetchData->stage = $nextid->name;
			$comments = 'moved the stage from  <b>'.$workflowstage->name.'</b> to <b>'.$nextid->name.'</b>';
			$width = $fetchData->progresswidth - $widthcount;
			if($width <= 0){
				$width = 0;
			}

			$fetchData->progresswidth = $width;

			$saved = $fetchData->save();
			if($saved){

				$obj = new ActivitiesLog;
				$obj->client_id = $fetchData->client_id;
				$obj->created_by = Auth::user()->id;
				$obj->subject = 'Stage: ' . $workflowstage->name; // Fixed: was ->stage, should be ->name
				$obj->description = $comments;
				$obj->activity_type = 'stage';
				$obj->use_for = 'application';
				$saved = $obj->save();

				$displayback = false;
				$workflowstage = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->orderBy('id','desc')->first();

				if($workflowstage->name == $fetchData->stage){
					$displayback = true;
				}

				$response['status'] 	= 	true;
				$response['stage']	=	$fetchData->stage;
				$response['displaycomplete']	=	$displayback;

				$response['width']	=	$width;
				$response['message']	=	'Application has been successfully moved to previous stage.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
	   }else{
		   $response['status'] 	= 	false;
				$response['message']	=	'';
	   }
		echo json_encode($response);
	}

	/**
	 * Move Client Matter to Next Stage
	 * 
	 * Updates the workflow_stage_id for a client_matter to the next stage in sequence
	 * Also updates the applications table if it exists (for backward compatibility)
	 * 
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function updateClientMatterNextStage(Request $request){
		try {
			$matterId = $request->input('matter_id');
			
			if (!$matterId) {
				return response()->json([
					'status' => false,
					'message' => 'Matter ID is required'
				], 422);
			}

			// Get the client matter
			$clientMatter = ClientMatter::find($matterId);
			
			if (!$clientMatter) {
				return response()->json([
					'status' => false,
					'message' => 'Client matter not found'
				], 404);
			}

			// Get current stage
			$currentStageId = $clientMatter->workflow_stage_id;
			
			if (!$currentStageId) {
				return response()->json([
					'status' => false,
					'message' => 'Current stage not found'
				], 404);
			}

			// Get current stage details
			$currentStage = WorkflowStage::find($currentStageId);
			
			if (!$currentStage) {
				return response()->json([
					'status' => false,
					'message' => 'Current workflow stage not found'
				], 404);
			}

			// Get next stage (ordered by id ascending)
			$nextStage = WorkflowStage::where('id', '>', $currentStageId)
				->orderBy('id', 'asc')
				->first();

			if (!$nextStage) {
				return response()->json([
					'status' => false,
					'message' => 'Already at the last stage',
					'is_last_stage' => true
				], 400);
			}

			// Update client_matters table
			$clientMatter->workflow_stage_id = $nextStage->id;
			$saved = $clientMatter->save();

			if ($saved) {
				// Update applications table if it exists (for backward compatibility)
				// This follows the same pattern as loadApplicationInsertUpdateData method
				$application = DB::table('applications')
					->where('client_matter_id', $matterId)
					->where('client_id', $clientMatter->client_id)
					->first();

				if ($application) {
					// Get workflow info from workflow_stages table
					// Note: w_id is a legacy field that may or may not exist
					// Following the same pattern as loadApplicationInsertUpdateData (line 554)
					try {
						$workflowInfo = DB::table('workflow_stages')
							->where('id', $nextStage->id)
							->select('workflow_stages.name', 'workflow_stages.w_id')
							->first();

						if ($workflowInfo) {
							$updateData = [
								'stage' => $workflowInfo->name,
								'updated_at' => now()
							];
							
							// Only include workflow if w_id exists and is not null
							if (isset($workflowInfo->w_id) && !is_null($workflowInfo->w_id)) {
								$updateData['workflow'] = $workflowInfo->w_id;
							}
							
							DB::table('applications')
								->where('id', $application->id)
								->update($updateData);
						}
					} catch (\Exception $e) {
						// If w_id column doesn't exist, just update stage field
						// This maintains backward compatibility
						DB::table('applications')
							->where('id', $application->id)
							->update([
								'stage' => $nextStage->name,
								'updated_at' => now()
							]);
					}
				}

				// Calculate progress percentage
				$totalStages = WorkflowStage::count();
				$currentStageIndex = WorkflowStage::where('id', '<=', $nextStage->id)->count();
				$progressPercentage = $totalStages > 0 ? round(($currentStageIndex / $totalStages) * 100) : 0;

				// Check if this is the last stage
				$isLastStage = !WorkflowStage::where('id', '>', $nextStage->id)->exists();

				// Log activity (Client Portal tab - website)
				$comments = 'moved the stage from <b>' . $currentStage->name . '</b> to <b>' . $nextStage->name . '</b>';
				
				$activityLog = new ActivitiesLog;
				$activityLog->client_id = $clientMatter->client_id;
				$activityLog->created_by = Auth::user()->id;
				$activityLog->subject = 'Stage: ' . $currentStage->name;
				$activityLog->description = $comments;
				$activityLog->activity_type = 'stage';
				// Note: use_for is an integer field in PostgreSQL, so we don't set it here
				// The existing code pattern for stage activities doesn't require use_for
				$activityLog->task_status = 0;
				$activityLog->pin = 0;
				$activityLog->source = 'client_portal_web';
				$activityLog->save();

				return response()->json([
					'status' => true,
					'message' => 'Matter has been successfully moved to the next stage.',
					'stage_name' => $nextStage->name,
					'stage_id' => $nextStage->id,
					'progress_percentage' => $progressPercentage,
					'is_last_stage' => $isLastStage
				]);
			} else {
				return response()->json([
					'status' => false,
					'message' => 'Failed to update matter stage. Please try again.'
				], 500);
			}

		} catch (\Exception $e) {
			Log::error('Error updating client matter next stage: ' . $e->getMessage(), [
				'matter_id' => $request->input('matter_id'),
				'trace' => $e->getTraceAsString()
			]);

			return response()->json([
				'status' => false,
				'message' => 'An error occurred while updating the stage. Please try again.'
			], 500);
		}
	}

	// LEGACY METHOD - Still used by some JavaScript but outputs HTML directly (old pattern)
	// TODO: Refactor to return JSON and handle rendering in frontend
	public function getapplicationslogs(Request $request){
		$id = $request->id;
		$fetchData = Application::find($id);

		if (!$fetchData) {
			return response()->json(['error' => 'Application not found'], 404);
		}

		$stagesquery = \App\Models\WorkflowStage::where('w_id', $fetchData->workflow)->get();
		foreach($stagesquery as $stages){
			$stage1 = '';

			$workflowstagess = \App\Models\WorkflowStage::where('name', $fetchData->stage)->where('w_id', $fetchData->workflow)->first();

			$prevdata = \App\Models\WorkflowStage::where('id', '<', $workflowstagess->id)->where('w_id', $fetchData->workflow)->orderBy('id','Desc')->get();
			$stagearray = array();
			foreach($prevdata as $pre){
				$stagearray[] = $pre->id;
			}

			if(in_array($stages->id, $stagearray)){
				$stage1 = 'app_green';
			}
			if($fetchData->status == 1){
				$stage1 = 'app_green';
			}
			$stagname = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stages->name)));
			?>

			<div class="accordion cus_accrodian">
				<div class="accordion-header collapsed <?php echo $stage1; ?> <?php if($fetchData->stage == $stages->name && $fetchData->status != 1){ echo  'app_blue'; }  ?>" role="button" data-toggle="collapse" data-target="#<?php echo $stagname; ?>_accor" aria-expanded="false">
					<h4><?php echo $stages->name; ?></h4>
					<div class="accord_hover">
						<a title="Add Note" class="openappnote" data-app-type="<?php echo $stages->name; ?>" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
						<a title="Add Document" class="opendocnote" data-app-type="<?php echo $stagname; ?>" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-image"></i></a>
						<!-- REMOVED: Old appointment system reference (openappappoint) -->
						<a data-app-type="<?php echo $stages->name; ?>" title="Email" data-id="<?php echo $fetchData->id; ?>" data-email="" data-name="" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
					</div>
				</div>
				<?php
				$applicationlists = \App\Models\ActivitiesLog::where('client_id', $fetchData->client_id)
					->where('use_for', 'application')
					->where('subject', 'like', '%Stage: ' . $stages->name . '%')
					->orderby('created_at', 'DESC')->get();
				?>
				<div class="accordion-body collapse" id="<?php echo $stagname; ?>_accor" data-parent="#accordion" style="">
					<div class="activity_list">
					<?php foreach($applicationlists as $applicationlist){
						$admin = \App\Models\Admin::where('id',$applicationlist->created_by)->first();
					?>
						<div class="activity_col">
							<div class="activity_txt_time">
								<span class="span_txt"><b><?php echo $admin->first_name; ?></b> <?php echo $applicationlist->description; ?></span>
								<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
							</div>
							<?php if($applicationlist->subject != ''){ ?>
							<div class="app_description">
								<div class="app_card">
									<div class="app_title"><?php echo $applicationlist->subject; ?></div>
								</div>
								<?php if($applicationlist->description != ''){ ?>
								<div class="log_desc">
									<?php echo $applicationlist->description; ?>
								</div>
								<?php } ?>
							</div>
							<?php } ?>
						</div>
					<?php } ?>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php
	}

	public function addNote(Request $request){
		$noteid =  $request->noteid;
		$type =  $request->type;
		$application = Application::find($noteid);

		$obj = new ActivitiesLog;
		$obj->client_id = $application ? $application->client_id : null;
		$obj->created_by = Auth::user()->id;
		$obj->subject = $request->title;
		$obj->description = $request->description;
		$obj->activity_type = 'note';
		$obj->use_for = 'application';
		$saved = $obj->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'Note successfully added';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function getapplicationnotes(Request $request){
		$noteid =  $request->id;
		$application = Application::find($noteid);

		$lists = ActivitiesLog::where('activity_type','note')
			->where('use_for','application')
			->where('client_id', $application ? $application->client_id : null)
			->orderby('created_at', 'DESC')->get();

		ob_start();
			?>
			<div class="note_term_list">
				<?php
				foreach($lists as $list){
					$admin = \App\Models\Admin::where('id', $list->created_by)->first();
				?>
					<div class="note_col" id="note_id_<?php echo $list->id; ?>">
						<div class="note_content">
						<h4><a class="viewapplicationnote" data-id="<?php echo $list->id; ?>" href="javascript:;"><?php echo @$list->subject == "" ? config('constants.empty') : Str::limit(@$list->subject, 19, '...'); ?></a></h4>
						<p><?php echo @$list->description == "" ? config('constants.empty') : Str::limit(@$list->description, 15, '...'); ?></p>
						</div>
						<div class="extra_content">
							<div class="left">
								<div class="author">
									<a href="#"><?php echo substr($admin->first_name, 0, 1); ?></a>
								</div>
								<div class="note_modify">
									<small>Last Modified <span><?php echo date('Y-m-d', strtotime($list->updated_at)); ?></span></small>
								</div>
							</div>
							<div class="right">

							</div>
						</div>
					</div>
				<?php } ?>
				</div>
				<div class="clearfix"></div>
			<?php
			echo ob_get_clean();

	}

	public function applicationsendmail(Request $request){
		$requestData = $request->all();
		$user_id = @Auth::user()->id;
		$subject = $requestData['subject'];
		$message = $requestData['message'];
		$to = $requestData['to'];

	$client = \App\Models\Admin::where('email', $requestData['to'])->first();
		if (!$client) {
			return response()->json(['status' => false, 'message' => 'Client not found'], 404);
		}
		$subject = str_replace('{Client First Name}', $client->first_name, $subject);
		$message = str_replace('{Client First Name}', $client->first_name, $message);
		$message = str_replace('{Client Assignee Name}', $client->first_name, $message);
		$message = str_replace('{Company Name}', Auth::user()->company_name, $message);
		$message .= '<br><br>Consumer guide: <a href="https://www.mara.gov.au/get-help-visa-subsite/FIles/consumer_guide_english.pdf">https://www.mara.gov.au/get-help-visa-subsite/FIles/consumer_guide_english.pdf</a>';
			$array = array();
			$ccarray = array();
			if(isset($requestData['email_cc']) && !empty($requestData['email_cc'])){
				foreach($requestData['email_cc'] as $cc){
					$clientcc = \App\Models\Admin::Where('id', $cc)->first();
					$ccarray[] = $clientcc;
				}
			}
				$sent = $this->send_compose_template($to, $subject, 'support@digitrex.live', $message, 'digitrex', $array, $ccarray ?? []);
			if($sent){
				$application = Application::find($request->noteid);
				$objs = new ActivitiesLog;
				$objs->client_id = $application ? $application->client_id : null;
				$objs->created_by = Auth::user()->id;
				$objs->subject = '<b>Subject : '.$subject.'</b>';
				$objs->description = '<b>To: '.$to.'</b></br>'.$message;
				$objs->activity_type = 'email';
				$objs->use_for = 'application';
				$saved = $objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'Email Sent Successfully';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function updateintake(Request $request){
		$requestData = $request->all();
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->appid);
		$obj->intakedate = $request->from;
		$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Applied date successfully updated.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function updateexpectwin(Request $request){
		$requestData = $request->all();
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->appid);
		$obj->expect_win_date = $request->from;
		$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Date successfully updated.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function updatedates(Request $request){
		$requestData = $request->all();
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->appid);
		if($request->datetype == 'start'){
			$obj->start_date = $request->from;
		}else{
			$obj->end_date = $request->from;
		}
		$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Date successfully updated.';
				if($request->datetype == 'start'){
					$response['dates']	=	array(
						'date' => date('d',strtotime($obj->start_date)),
						'month' => date('M',strtotime($obj->start_date)),
						'year' => date('Y',strtotime($obj->start_date)),
					);
				}else{
					$response['dates']	=	array(
						'date' => date('d',strtotime($obj->end_date)),
						'month' => date('M',strtotime($obj->end_date)),
						'year' => date('Y',strtotime($obj->end_date)),
					);
				}

			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function discontinue_application(Request $request){
		$requestData = $request->all();
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->diapp_id);
		$obj->status = 2;
		$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully discontinued.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function revert_application(Request $request){
		$requestData = $request->all();
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->revapp_id);
		$obj->status = 0;
		$workflowstagecount = \App\Models\WorkflowStage::where('w_id', $obj->workflow)->count();
			$widthcount = 0;
			if($workflowstagecount !== 0){
				$s = 100 / $workflowstagecount;
				$widthcount = round($s);
			}
		$progresswidth = $obj->progresswidth - $widthcount;
		$obj->progresswidth = $progresswidth;
		$saved = $obj->save();
			if($saved){
			$displayback = false;
				$workflowstage = \App\Models\WorkflowStage::where('w_id', $obj->workflow)->orderBy('id','desc')->first();

				if($workflowstage->name == $obj->stage){
					$displayback = true;
				}
				$response['status'] 	= 	true;
				$response['width'] 	= 	$progresswidth;
				$response['displaycomplete'] 	= 	$displayback;
				$response['message']	=	'Application successfully reverted.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function spagent_application(Request $request){
		$requestData = $request->all();
		$flag = true;
		/* if(Application::where('super_agent',$request->super_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists';
		}
		if(Application::where('sub_agent',$request->super_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists in sub admin';
		} */
		if($flag){
			$user_id = @Auth::user()->id;
			$obj = Application::find($request->siapp_id);
			$obj->super_agent = $request->super_agent;
			$saved = $obj->save();
			if($saved){
				$agent = \App\Models\AgentDetails::where('id',$request->super_agent)->first();
				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				$response['data']	=	'<div class="client_info">
							<div class="cl_logo" style="display: inline-block;width: 30px;height: 30px; border-radius: 50%;background: #6777ef;text-align: center;color: #fff;font-size: 14px; line-height: 30px; vertical-align: top;">'.substr($agent->full_name, 0, 1).'</div>
							<div class="cl_name" style="display: inline-block;margin-left: 5px;width: calc(100% - 60px);">
								<span class="name">'.$agent->full_name.'</span>
								<span class="ui label zippyLabel alignMiddle yellow">
							  '.$agent->struture.'
							</span>
							</div>
							<div class="cl_del" style="display: inline-block;">
								<a href=""><i class="fa fa-times"></i></a>
							</div>
						</div>';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
		}

		echo json_encode($response);
	}

	public function sbagent_application(Request $request){
		$requestData = $request->all();
		$flag = true;
		/* if(Application::where('super_agent',$request->sub_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists in super admin';
		}
		if(Application::where('sub_agent',$request->sub_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists';
		} */
		if($flag){
			$user_id = @Auth::user()->id;
			$obj = Application::find($request->sbapp_id);
			$obj->sub_agent = $request->sub_agent;
			$saved = $obj->save();
			if($saved){
				$agent = \App\Models\AgentDetails::where('id',$request->sub_agent)->first();
				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				$response['data']	=	'<div class="client_info">
							<div class="cl_logo" style="display: inline-block;width: 30px;height: 30px; border-radius: 50%;background: #6777ef;text-align: center;color: #fff;font-size: 14px; line-height: 30px; vertical-align: top;">'.substr($agent->full_name, 0, 1).'</div>
							<div class="cl_name" style="display: inline-block;margin-left: 5px;width: calc(100% - 60px);">
								<span class="name">'.$agent->full_name.'</span>
								<span class="ui label zippyLabel alignMiddle yellow">
							  '.$agent->struture.'
							</span>
							</div>
							<div class="cl_del" style="display: inline-block;">
								<a href=""><i class="fa fa-times"></i></a>
							</div>
						</div>';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
		}

		echo json_encode($response);
	}

	public function superagent(Request $request){
		$requestData = $request->all();

			$user_id = @Auth::user()->id;
			$obj = Application::find($request->note_id);
			$obj->super_agent = '';
			$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';

			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function subagent(Request $request){
		$requestData = $request->all();

			$user_id = @Auth::user()->id;
			$obj = Application::find($request->note_id);
			$obj->sub_agent = '';
			$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';

			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	public function application_ownership(Request $request){
		$requestData = $request->all();

			$user_id = @Auth::user()->id;
			$obj = Application::find($request->mapp_id);
			$obj->ratio = $request->ratio;
			$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				$response['ratio']	=	$obj->ratio;

			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}

	// Removed legacy method: saleforcast

	// REMOVED - Unused method (no references found in views or JavaScript)
	// This method returned application dropdown options for a client but was never used
	// public function getapplicationbycid(Request $request){ ... }


	public function applicationsavefee(Request $request){
		// Fee options functionality has been removed
		$response = [
			'status' => false,
			'message' => 'Application fee options feature has been removed.'
		];
		return response()->json($response);
	}

	// REMOVED - Application PDF export functionality (view file deleted, was broken and unused)
	// public function exportapplicationpdf(Request $request, $id){
	// 	$applications = \App\Models\Application::where('id', $id)->first();
	// 	$cleintname = \App\Models\Admin::where('role',7)->where('id',@$applications->client_id)->first();
	// 	$pdf = PDF::setOptions([
	// 		'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
	// 		'logOutputFile' => storage_path('logs/log.htm'),
	// 		'tempDir' => storage_path('logs/')
	// 		])->loadView('emails.application',compact(['cleintname','applications','productdetail','PartnerBranch','partnerdetail']));
	// 	return $pdf->stream('application.pdf');
	// }

	public function addchecklists(Request $request){
		// Validate required fields
		$request->validate([
			'document_type' => 'required|string|max:255',
			'client_id' => 'required|integer',
			'app_id' => 'required|integer',
			'type' => 'required|string',
			'typename' => 'required|string'
		], [
			'document_type.required' => 'Checklist Name is required.',
			'document_type.string' => 'Checklist Name must be a valid text.',
			'document_type.max' => 'Checklist Name cannot exceed 255 characters.',
			'client_id.required' => 'Client ID is required.',
			'app_id.required' => 'Application ID is required.',
			'type.required' => 'Type is required.',
			'typename.required' => 'Type name is required.'
		]);
		
		$requestData = $request->all();
		$client_id = $requestData['client_id'];
		$app_id = $requestData['app_id'];
		$type = $requestData['type'];
		$typename = $requestData['typename'];
		$document_type = trim($request->document_type);
		
		// Double check document_type is not empty after trimming
		if (empty($document_type)) {
			$response['status'] = false;
			$response['message'] = 'Checklist Name is required.';
			echo json_encode($response);
			return;
		}
		
		$obj = new \App\Models\ApplicationDocumentList;
		$obj->type = $type;
		$obj->typename = $typename;
		$obj->client_id = $client_id;
		$obj->application_id = $app_id;
		$obj->document_type = $document_type;
		$obj->description = $request->description ?? null;
		$obj->allow_client = $request->allow_upload_docu ?? 0;
		$obj->make_mandatory = $request->proceed_next_stage ?? null;
		if(isset($requestData['due_date']) && $requestData['due_date'] == 1){
			$obj->date = $request->appoint_date ?? null;
			$obj->time = $request->appoint_time ?? null;
		}
		$obj->user_id = Auth::user()->id;

		$saved = $obj->save();
		if($saved){
			// Log activity (Client Portal Documents tab - website)
			DB::table('activities_logs')->insert([
				'client_id' => $client_id,
				'created_by' => Auth::user()->id,
				'subject' => 'Added checklist in Client Portal (Documents tab)',
				'description' => 'Checklist "' . $document_type . '" added via Client Portal tab (website) for application ID: ' . $app_id,
				'task_status' => 0,
				'pin' => 0,
				'source' => 'client_portal_web',
				'created_at' => now(),
				'updated_at' => now()
			]);

			$applicationdocuments = \App\Models\ApplicationDocumentList::where('application_id', $app_id)->where('client_id', $client_id)->where('type', $type)->get();
			$checklistdata = '<table class="table"><tbody>';
			foreach($applicationdocuments as $applicationdocument){
				$appcount = \App\Models\ApplicationDocument::where('list_id', $applicationdocument->id)->count();
				$checklistdata .= '<tr>';
				if($appcount >0){
					$checklistdata .= '<td><span class="check"><i class="fa fa-check"></i></span></td>';
				}else{
					$checklistdata .= '<td><span class="round"></span></td>';
				}

					$checklistdata .= '<td>'.@$applicationdocument->document_type.'</td>';
					$checklistdata .= '<td><div class="circular-box cursor-pointer"><button class="transparent-button paddingNone">'.$appcount.'</button></div></td>';
					$checklistdata .= '<td><a data-aid="'.$app_id.'" data-type="'.$type.'" data-typename="'.$typename.'" data-id="'.$applicationdocument->id.'" class="openfileupload" href="javascript:;"><i class="fa fa-plus"></i></a></td>';
				$checklistdata .= '</tr>';
			}
			$checklistdata .= '</tbody></table>';
			$response['status'] 	= 	true;
			$response['message']	=	'CHecklist added successfully';
			$response['data']	=	$checklistdata;
			$countchecklist = \App\Models\ApplicationDocumentList::where('application_id', $app_id)->count();
			$response['countchecklist']	=	$countchecklist;
		}else{
			$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
		}
		echo json_encode($response);
	}

	public function checklistupload(Request $request){
		 $imageData = '';
		if (isset($_FILES['file']['name'][0])) {
		  foreach ($_FILES['file']['name'] as $keys => $values) {
			$fileName = $_FILES['file']['name'][$keys];
			if (move_uploaded_file($_FILES['file']['tmp_name'][$keys], config('constants.documents').'/'. $fileName)) {
				$obj = new \App\Models\ApplicationDocument;
				$obj->type = $request->type;
				$obj->typename = $request->typename;
				$obj->list_id = $request->id;
				$obj->file_name = $fileName;
				$obj->user_id = Auth::user()->id;
				$obj->application_id = $request->application_id;
				$save = $obj->save();
			  $imageData .= '<li><i class="fa fa-file"></i> '.$fileName.'</li>';
			}
		  }
		}

		// Log activity when at least one document was uploaded (Client Portal Documents tab - website)
		if (!empty($imageData)) {
			$appRecord = DB::table('applications')->where('id', $request->application_id)->first();
			if ($appRecord && !empty($appRecord->client_id)) {
				DB::table('activities_logs')->insert([
					'client_id' => $appRecord->client_id,
					'created_by' => Auth::user()->id,
					'subject' => 'Uploaded document(s) to checklist in Client Portal (Documents tab)',
					'description' => 'Document(s) uploaded via Client Portal tab (website) for application ID: ' . $request->application_id,
					'task_status' => 0,
					'pin' => 0,
					'source' => 'client_portal_web',
					'created_at' => now(),
					'updated_at' => now()
				]);
			}
		}

		$doclists = \App\Models\ApplicationDocument::where('application_id',$request->application_id)->orderby('created_at','DESC')->get();
		$doclistdata = '';
		foreach($doclists as $doclist){
			$docdata = \App\Models\ApplicationDocumentList::where('id', $doclist->list_id)->first();
			$doclistdata .= '<tr id="">';
				$doclistdata .= '<td><i class="fa fa-file"></i> '. $doclist->file_name.'<br>'.@$docdata->document_type.'</td>';
				$doclistdata .= '<td>';
					$doclistdata .=  $doclist->typename;
				$doclistdata .= '</td>';
				$admin = \App\Models\Admin::where('id', @$doclist->user_id)->first();

			$doclistdata .= '<td><span style="    position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;">'.substr(@$admin->first_name, 0, 1).'</span>'.@$admin->first_name.'</td>';
			$doclistdata .= '<td>'.date('Y-m-d',strtotime($doclist->created_at)).'</td>';
			$doclistdata .= '<td>';
			if($doclist->status == 1){
				$doclistdata .= '<span class="check"><i class="fa fa-eye"></i></span>';
			}
				$doclistdata .= '<div class="dropdown d-inline">
					<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
					<div class="dropdown-menu">
						<a target="_blank" class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Preview</a>
						<a data-id="'.$doclist->id.'" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
						<a download class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Download</a>';
						if($doclist->status == 0){
							$doclistdata .= '<a data-id="'.$doclist->id.'" class="dropdown-item publishdoc" href="javascript:;">Publish Document</a>';
						}else{
							$doclistdata .= '<a data-id="'.$doclist->id.'"  class="dropdown-item unpublishdoc" href="javascript:;">Unpublish Document</a>';
						}

					$doclistdata .= '</div>
				</div>
			</td>';
			$doclistdata .= '</tr>';
		}
		$application_id = $request->application_id;
		$applicationuploadcount = DB::select("SELECT COUNT(DISTINCT list_id) AS cnt FROM application_documents where application_id = '$application_id'");
		$response['status'] 	= 	true;
		$response['imagedata']	=	$imageData;
		$response['doclistdata']	=	$doclistdata;
		$response['applicationuploadcount']	=	@$applicationuploadcount[0]->cnt;

		$applicationdocuments = \App\Models\ApplicationDocumentList::where('application_id', $application_id)->where('type', $request->type)->get();
			$checklistdata = '<table class="table"><tbody>';
			foreach($applicationdocuments as $applicationdocument){
				$appcount = \App\Models\ApplicationDocument::where('list_id', $applicationdocument->id)->count();
				$checklistdata .= '<tr>';
				if($appcount >0){
					$checklistdata .= '<td><span class="check"><i class="fa fa-check"></i></span></td>';
				}else{
					$checklistdata .= '<td><span class="round"></span></td>';
				}

					$checklistdata .= '<td>'.@$applicationdocument->document_type.'</td>';
					$checklistdata .= '<td><div class="circular-box cursor-pointer"><button class="transparent-button paddingNone">'.$appcount.'</button></div></td>';
					$checklistdata .= '<td><a data-aid="'.$application_id.'" data-type="'.$request->type.'" data-id="'.$applicationdocument->id.'" class="openfileupload" href="javascript:;"><i class="fa fa-plus"></i></a></td>';
				$checklistdata .= '</tr>';
			}
			$checklistdata .= '</tbody></table>';
		$response['checklistdata']	=	$checklistdata;
		$response['type']	=	$request->type;
		echo json_encode($response);
	}

	public function deleteapplicationdocs(Request $request){
		// Check if we're deleting by list_id (new method) or by id (old method for backward compatibility)
		if($request->has('list_id') && $request->list_id){
			// Delete all documents with the same list_id
			$listId = $request->list_id;
			
			// Get first document to get application_id for response
			$appdoc = \App\Models\ApplicationDocument::where('list_id', $listId)->first();
			
			if($appdoc){
				// Delete all documents with this list_id
				$res = \App\Models\ApplicationDocument::where('list_id', $listId)->delete();
				
				if($res){
				$response['status'] 	= 	true;
				$response['message'] 	= 	'Record removed successfully';

				$doclists = \App\Models\ApplicationDocument::where('application_id',$appdoc->application_id)->orderby('created_at','DESC')->get();
		$doclistdata = '';
		foreach($doclists as $doclist){
			$docdata = \App\Models\ApplicationDocumentList::where('id', $doclist->list_id)->first();
			$doclistdata .= '<tr id="">';
				$doclistdata .= '<td><i class="fa fa-file"></i> '. $doclist->file_name.'<br>'.@$docdata->document_type.'</td>';
				$doclistdata .= '<td>';
				if($doclist->type == 'application'){ $doclistdata .= 'Application'; }else if($doclist->type == 'acceptance'){ $doclistdata .=  'Acceptance'; }else if($doclist->type == 'payment'){ $doclistdata .=  'Payment'; }else if($doclist->type == 'formi20'){ $doclistdata .=  'Form I 20'; }else if($doclist->type == 'visaapplication'){ $doclistdata .=  'Visa Application'; }else if($doclist->type == 'interview'){ $doclistdata .=  'Interview'; }else if($doclist->type == 'enrolment'){ $doclistdata .=  'Enrolment'; }else if($doclist->type == 'courseongoing'){ $doclistdata .=  'Course Ongoing'; }
				$doclistdata .= '</td>';
				$admin = \App\Models\Admin::where('id', $doclist->user_id)->first();

			$doclistdata .= '<td><span style="    position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;">'.substr($admin->first_name, 0, 1).'</span>'.$admin->first_name.'</td>';
			$doclistdata .= '<td>'.date('Y-m-d',strtotime($doclist->created_at)).'</td>';
			$doclistdata .= '<td>';
			if($doclist->status == 1){
				$doclistdata .= '<span class="check"><i class="fa fa-eye"></i></span>';
			}
				$doclistdata .= '<div class="dropdown d-inline">
					<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
					<div class="dropdown-menu">
						<a target="_blank" class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Preview</a>
						<a data-id="'.$doclist->id.'" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
						<a download class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Download</a>';
						if($doclist->status == 0){
							$doclistdata .= '<a data-id="'.$doclist->id.'" class="dropdown-item publishdoc" href="javascript:;">Publish Document</a>';
						}else{
							$doclistdata .= '<a data-id="'.$doclist->id.'"  class="dropdown-item unpublishdoc" href="javascript:;">Unpublish Document</a>';
						}

					$doclistdata .= '</div>
				</div>
			</td>';
			$doclistdata .= '</tr>';
		}
		$application_id = $appdoc->application_id;
		$applicationuploadcount = DB::select("SELECT COUNT(DISTINCT list_id) AS cnt FROM application_documents where application_id = '$application_id'");
		$response['status'] 	= 	true;

		$response['doclistdata']	=	$doclistdata;
		$response['applicationuploadcount']	=	@$applicationuploadcount[0]->cnt;

		$applicationdocuments = \App\Models\ApplicationDocumentList::where('application_id', $application_id)->where('type', $appdoc->type)->get();
			$checklistdata = '<table class="table"><tbody>';
			foreach($applicationdocuments as $applicationdocument){
				$appcount = \App\Models\ApplicationDocument::where('list_id', $applicationdocument->id)->count();
				$checklistdata .= '<tr>';
				if($appcount >0){
					$checklistdata .= '<td><span class="check"><i class="fa fa-check"></i></span></td>';
				}else{
					$checklistdata .= '<td><span class="round"></span></td>';
				}

					$checklistdata .= '<td>'.@$applicationdocument->document_type.'</td>';
					$checklistdata .= '<td><div class="circular-box cursor-pointer"><button class="transparent-button paddingNone">'.$appcount.'</button></div></td>';
					$checklistdata .= '<td><a data-aid="'.$application_id.'" data-type="'.$appdoc->type.'"data-typename="'.$appdoc->typename.'" data-id="'.$applicationdocument->id.'" class="openfileupload" href="javascript:;"><i class="fa fa-plus"></i></a></td>';
				$checklistdata .= '</tr>';
			}
			$checklistdata .= '</tbody></table>';
		$response['checklistdata']	=	$checklistdata;
		$response['type']	=	$appdoc->type;
			}else{
				$response['status'] 	= 	false;
				$response['message'] 	= 	'Please try again';
			}
			}else{
				$response['status'] 	= 	false;
				$response['message'] 	= 	'No Record found with this list_id';
			}
			echo json_encode($response);
			return;
		}
		
		// Backward compatibility: Delete by document id (old method)
		if(\App\Models\ApplicationDocument::where('id', $request->note_id)->exists()){
			$appdoc = \App\Models\ApplicationDocument::where('id', $request->note_id)->first();
			$res = \App\Models\ApplicationDocument::where('id', $request->note_id)->delete();
			if($res){
				$response['status'] 	= 	true;
				$response['message'] 	= 	'Record removed successfully';

				$doclists = \App\Models\ApplicationDocument::where('application_id',$appdoc->application_id)->orderby('created_at','DESC')->get();
		$doclistdata = '';
		foreach($doclists as $doclist){
			$docdata = \App\Models\ApplicationDocumentList::where('id', $doclist->list_id)->first();
			$doclistdata .= '<tr id="">';
				$doclistdata .= '<td><i class="fa fa-file"></i> '. $doclist->file_name.'<br>'.@$docdata->document_type.'</td>';
				$doclistdata .= '<td>';
				if($doclist->type == 'application'){ $doclistdata .= 'Application'; }else if($doclist->type == 'acceptance'){ $doclistdata .=  'Acceptance'; }else if($doclist->type == 'payment'){ $doclistdata .=  'Payment'; }else if($doclist->type == 'formi20'){ $doclistdata .=  'Form I 20'; }else if($doclist->type == 'visaapplication'){ $doclistdata .=  'Visa Application'; }else if($doclist->type == 'interview'){ $doclistdata .=  'Interview'; }else if($doclist->type == 'enrolment'){ $doclistdata .=  'Enrolment'; }else if($doclist->type == 'courseongoing'){ $doclistdata .=  'Course Ongoing'; }
				$doclistdata .= '</td>';
				$admin = \App\Models\Admin::where('id', $doclist->user_id)->first();

			$doclistdata .= '<td><span style="    position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;">'.substr($admin->first_name, 0, 1).'</span>'.$admin->first_name.'</td>';
			$doclistdata .= '<td>'.date('Y-m-d',strtotime($doclist->created_at)).'</td>';
			$doclistdata .= '<td>';
			if($doclist->status == 1){
				$doclistdata .= '<span class="check"><i class="fa fa-eye"></i></span>';
			}
				$doclistdata .= '<div class="dropdown d-inline">
					<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
					<div class="dropdown-menu">
						<a target="_blank" class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Preview</a>
						<a data-id="'.$doclist->id.'" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
						<a download class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Download</a>';
						if($doclist->status == 0){
							$doclistdata .= '<a data-id="'.$doclist->id.'" class="dropdown-item publishdoc" href="javascript:;">Publish Document</a>';
						}else{
							$doclistdata .= '<a data-id="'.$doclist->id.'"  class="dropdown-item unpublishdoc" href="javascript:;">Unpublish Document</a>';
						}

					$doclistdata .= '</div>
				</div>
			</td>';
			$doclistdata .= '</tr>';
		}

		$response['status'] 	= 	true;

		$response['doclistdata']	=	$doclistdata;

			}else{
				$response['status'] 	= 	false;
				$response['message'] 	= 	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message'] 	= 	'No Record found';
		}
		echo json_encode($response);
	}

	public function publishdoc(Request $request){
		if(\App\Models\ApplicationDocument::where('id', $request->appid)->exists()){
			$appdoc = \App\Models\ApplicationDocument::where('id', $request->appid)->first();
			$obj = \App\Models\ApplicationDocument::find($request->appid);
			$obj->status = $request->status;
			$saved = $obj->save();
			if($saved){
				$response['status'] 	= 	true;
				$response['message'] 	= 	'Record updated successfully';
				$doclists = \App\Models\ApplicationDocument::where('application_id',$appdoc->application_id)->orderby('created_at','DESC')->get();
		$doclistdata = '';
		foreach($doclists as $doclist){
			$docdata = \App\Models\ApplicationDocumentList::where('id', $doclist->list_id)->first();
			$doclistdata .= '<tr id="">';
				$doclistdata .= '<td><i class="fa fa-file"></i> '. $doclist->file_name.'<br>'.@$docdata->document_type.'</td>';
				$doclistdata .= '<td>';
				if($doclist->type == 'application'){ $doclistdata .= 'Application'; }else if($doclist->type == 'acceptance'){ $doclistdata .=  'Acceptance'; }else if($doclist->type == 'payment'){ $doclistdata .=  'Payment'; }else if($doclist->type == 'formi20'){ $doclistdata .=  'Form I 20'; }else if($doclist->type == 'visaapplication'){ $doclistdata .=  'Visa Application'; }else if($doclist->type == 'interview'){ $doclistdata .=  'Interview'; }else if($doclist->type == 'enrolment'){ $doclistdata .=  'Enrolment'; }else if($doclist->type == 'courseongoing'){ $doclistdata .=  'Course Ongoing'; }
				$doclistdata .= '</td>';
				$admin = \App\Models\Admin::where('id', $doclist->user_id)->first();

			$doclistdata .= '<td><span style="    position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;">'.substr($admin->first_name, 0, 1).'</span>'.$admin->first_name.'</td>';
			$doclistdata .= '<td>'.date('Y-m-d',strtotime($doclist->created_at)).'</td>';
			$doclistdata .= '<td>';
			if($doclist->status == 1){
				$doclistdata .= '<span class="check"><i class="fa fa-eye"></i></span>';
			}
				$doclistdata .= '<div class="dropdown d-inline">
					<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
					<div class="dropdown-menu">
						<a target="_blank" class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Preview</a>
						<a data-id="'.$doclist->id.'" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
						<a download class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Download</a>';
						if($doclist->status == 0){
							$doclistdata .= '<a data-id="'.$doclist->id.'" class="dropdown-item publishdoc" href="javascript:;">Publish Document</a>';
						}else{
							$doclistdata .= '<a data-id="'.$doclist->id.'"  class="dropdown-item unpublishdoc" href="javascript:;">Unpublish Document</a>';
						}

					$doclistdata .= '</div>
				</div>
			</td>';
			$doclistdata .= '</tr>';
		}

		$response['status'] 	= 	true;

		$response['doclistdata']	=	$doclistdata;

			}else{
				$response['status'] 	= 	false;
				$response['message'] 	= 	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message'] 	= 	'No Record found';
		}
		echo json_encode($response);
	}

	public function getapplications(Request $request){
		$client_id = $request->client_id;
		$applications = Application::where('client_id', '=', $client_id)->get();
		ob_start();
		?>
		<option value="">Choose Application</option>
		<?php
		foreach($applications as $application){
			
			// Partner functionality removed
			?>
		<option value="<?php echo $application->id; ?>">(#<?php echo $application->id; ?>) Application (Partner)</option>
			<?php
		}
		return ob_get_clean();
	}

	// REMOVED - Standalone migration index page (not linked from anywhere, orphaned page)
	// public function migrationindex(Request $request)
	// {
	// }

	// REMOVED - Applications import functionality (only used by removed applications index page)
	// public function import(Request $request){
	// }

	public function approveDocument(Request $request){
		$response = ['status' => false, 'message' => 'Error approving document.'];
		
		try {
			$documentId = $request->input('document_id');
			
			if (!$documentId) {
				$response['message'] = 'Document ID is required.';
				return response()->json($response);
			}
			
			// Update document status to 1 (Approved)
			$updated = DB::table('application_documents')
				->where('id', $documentId)
				->update([
					'status' => 1,
					'updated_at' => now()
				]);
			
			if ($updated) {
				// Log activity (Client Portal Documents tab - website)
				$doc = DB::table('application_documents')->where('id', $documentId)->first();
				if ($doc) {
					$app = DB::table('applications')->where('id', $doc->application_id)->first();
					if ($app && !empty($app->client_id)) {
						DB::table('activities_logs')->insert([
							'client_id' => $app->client_id,
							'created_by' => Auth::guard('admin')->id(),
							'subject' => 'Approved document in Client Portal (Documents tab)',
							'description' => 'Document approved via Client Portal tab (website) for document ID: ' . $documentId,
							'task_status' => 0,
							'pin' => 0,
							'source' => 'client_portal_web',
							'created_at' => now(),
							'updated_at' => now()
						]);
					}
				}
				$response['status'] = true;
				$response['message'] = 'Document approved successfully!';
			} else {
				$response['message'] = 'Document not found or could not be updated.';
			}
		} catch (\Exception $e) {
			$response['message'] = 'An error occurred: ' . $e->getMessage();
		}
		
		return response()->json($response);
	}

	public function rejectDocument(Request $request){
		$response = ['status' => false, 'message' => 'Error rejecting document.'];
		
		try {
			$documentId = $request->input('document_id');
			$rejectReason = $request->input('reject_reason');
			
			if (!$documentId) {
				$response['message'] = 'Document ID is required.';
				return response()->json($response);
			}
			
			if (!$rejectReason || trim($rejectReason) === '') {
				$response['message'] = 'Rejection reason is required.';
				return response()->json($response);
			}
			
			// Update data with status and rejection reason
			$updateData = [
				'status' => 2,
				'updated_at' => now()
			];
			
			// Update doc_rejection_reason if column exists
			if (Schema::hasColumn('application_documents', 'doc_rejection_reason')) {
				$updateData['doc_rejection_reason'] = trim($rejectReason);
			} elseif (Schema::hasColumn('application_documents', 'reject_reason')) {
				// Fallback to reject_reason if doc_rejection_reason doesn't exist
				$updateData['reject_reason'] = trim($rejectReason);
			}
			
			// Update document status to 2 (Rejected)
			$updated = DB::table('application_documents')
				->where('id', $documentId)
				->update($updateData);
			
			if ($updated) {
				// Log activity (Client Portal Documents tab - website)
				$doc = DB::table('application_documents')->where('id', $documentId)->first();
				if ($doc) {
					$app = DB::table('applications')->where('id', $doc->application_id)->first();
					if ($app && !empty($app->client_id)) {
						DB::table('activities_logs')->insert([
							'client_id' => $app->client_id,
							'created_by' => Auth::guard('admin')->id(),
							'subject' => 'Rejected document in Client Portal (Documents tab)',
							'description' => 'Document rejected via Client Portal tab (website) for document ID: ' . $documentId . (trim($rejectReason ?? '') !== '' ? '. Reason: ' . trim($rejectReason) : ''),
							'task_status' => 0,
							'pin' => 0,
							'source' => 'client_portal_web',
							'created_at' => now(),
							'updated_at' => now()
						]);
					}
				}
				$response['status'] = true;
				$response['message'] = 'Document rejected successfully!';
			} else {
				$response['message'] = 'Document not found or could not be updated.';
			}
		} catch (\Exception $e) {
			$response['message'] = 'An error occurred: ' . $e->getMessage();
		}
		
		return response()->json($response);
	}

	public function downloadDocument(Request $request){
		$response = ['status' => false, 'message' => 'Error downloading document.'];
		
		try {
			$documentId = $request->input('document_id');
			
			if (!$documentId) {
				$response['message'] = 'Document ID is required.';
				return response()->json($response);
			}
			
			// Get document from database
			$document = DB::table('application_documents')
				->where('id', $documentId)
				->first();
			
			if (!$document || !$document->myfile) {
				$response['message'] = 'Document not found.';
				return response()->json($response);
			}
			
			$fileUrl = $document->myfile;
			$fileName = $document->file_name ?: 'document.pdf';
			
			// Fetch file from S3/URL
			$fileContent = @file_get_contents($fileUrl);
			
			if ($fileContent === false) {
				// Try using cURL if file_get_contents fails
				$ch = curl_init($fileUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$fileContent = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				
				if ($httpCode !== 200 || $fileContent === false) {
					$response['message'] = 'Failed to fetch file from URL.';
					return response()->json($response);
				}
			}
			
			// Determine content type based on file extension or file URL
			$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
			
			// If extension is empty, try to get it from URL
			if (empty($extension)) {
				$urlExtension = strtolower(pathinfo(parse_url($fileUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
				if (!empty($urlExtension)) {
					$extension = $urlExtension;
				}
			}
			
			// Default to PDF if extension still empty
			if (empty($extension)) {
				$extension = 'pdf';
			}
			
			$contentType = 'application/octet-stream';
			
			if ($extension === 'pdf') {
				$contentType = 'application/pdf';
			} elseif (in_array($extension, ['jpg', 'jpeg'])) {
				$contentType = 'image/jpeg';
			} elseif ($extension === 'png') {
				$contentType = 'image/png';
			} elseif ($extension === 'doc') {
				$contentType = 'application/msword';
			} elseif ($extension === 'docx') {
				$contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
			}
			
			// Ensure filename has proper extension
			if (empty(pathinfo($fileName, PATHINFO_EXTENSION))) {
				$fileName .= '.' . $extension;
			}
			
			// Ensure filename is properly encoded
			$encodedFileName = rawurlencode($fileName);
			
			// Return file as download with proper headers to force download
			return response($fileContent, 200)
				->header('Content-Type', $contentType)
				->header('Content-Disposition', 'attachment; filename="' . addslashes($fileName) . '"; filename*=UTF-8\'\'' . $encodedFileName)
				->header('Content-Length', strlen($fileContent))
				->header('Cache-Control', 'no-cache, no-store, must-revalidate')
				->header('Pragma', 'no-cache')
				->header('Expires', '0')
				->header('X-Content-Type-Options', 'nosniff');
				
		} catch (\Exception $e) {
			$response['message'] = 'An error occurred: ' . $e->getMessage();
			return response()->json($response);
		}
	}

	/**
     * Get Messages for a Client Matter
     * GET /clients/matter-messages
     * 
     * Retrieves all messages for a specific client matter for admin view
     * Used in the client portal application tab
     */
	public function getMatterMessages(Request $request)
	{
		try {
			$request->validate([
				'client_matter_id' => 'required|integer|min:1'
			]);

			$clientMatterId = $request->input('client_matter_id');
			$currentUserId = Auth::guard('admin')->id();

			if (!$currentUserId) {
				return response()->json([
					'success' => false,
					'message' => 'Unauthorized'
				], 401);
			}

			// Get all messages for this client matter, ordered by created_at ascending (oldest first)
			$messages = DB::table('messages')
				->where('client_matter_id', $clientMatterId)
				->orderBy('created_at', 'asc')
				->orderBy('id', 'asc')
				->get()
				->map(function ($message) use ($currentUserId) {
					// Get sender info
					$sender = null;
					if ($message->sender_id) {
						$sender = DB::table('admins')
							->where('id', $message->sender_id)
							->select('id', 'first_name', 'last_name', DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) as full_name"))
							->first();
					}

					// Get all recipients for this message
					$recipients = MessageRecipient::where('message_id', $message->id)
						->get()
						->map(function ($recipient) {
							$recipientUser = DB::table('admins')
								->where('id', $recipient->recipient_id)
								->select('id', 'first_name', 'last_name', DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) as full_name"))
								->first();
							
							return [
								'recipient_id' => $recipient->recipient_id,
								'recipient_name' => $recipient->recipient,
								'is_read' => $recipient->is_read,
								'read_at' => $recipient->read_at,
								'user' => $recipientUser
							];
						});

					// Determine if message is from current user (sent) or to current user (received)
					$isSent = ($message->sender_id == $currentUserId);
					
					// Generate sender initials
					$senderInitials = '';
					if ($sender) {
						$firstInitial = $sender->first_name ? strtoupper(substr($sender->first_name, 0, 1)) : '';
						$lastInitial = $sender->last_name ? strtoupper(substr($sender->last_name, 0, 1)) : '';
						$senderInitials = $firstInitial . $lastInitial;
					}

					// Safely handle attachments property (may not exist in all database schemas)
					$attachments = null;
					if (property_exists($message, 'attachments') && $message->attachments) {
						$attachments = json_decode($message->attachments, true);
					}

					return [
						'id' => $message->id,
						'message' => $message->message,
						'sender_id' => $message->sender_id,
						'sender_name' => $message->sender,
						'sender' => $sender,
						'sender_initials' => $senderInitials,
						'sent_at' => $message->sent_at ? $message->sent_at : $message->created_at,
						'created_at' => $message->created_at,
						'client_matter_id' => $message->client_matter_id,
						'recipients' => $recipients,
						'is_sent' => $isSent,
						'attachments' => $attachments
					];
				});

			return response()->json([
				'success' => true,
				'data' => [
					'messages' => $messages->values(), // Ensure it's a proper array
					'total' => $messages->count()
				]
			], 200);

		} catch (\Illuminate\Validation\ValidationException $e) {
			return response()->json([
				'success' => false,
				'message' => 'Validation failed',
				'errors' => $e->errors()
			], 422);
		} catch (\Exception $e) {
			Log::error('Get Matter Messages Error: ' . $e->getMessage(), [
				'client_matter_id' => $request->input('client_matter_id'),
				'trace' => $e->getTraceAsString()
			]);

			return response()->json([
				'success' => false,
				'message' => 'Failed to fetch messages',
				'error' => $e->getMessage()
			], 500);
		}
	}

	/**
     * Send Message to Client (Web Route)
     * POST /clients/send-message
     * 
     * Sends a message to the client associated with the client matter
     * Uses session-based authentication for web admin users
     */
	public function sendMessageToClient(Request $request)
	{
		try {
			$request->validate([
				'message' => 'required|string|max:5000',
				'client_matter_id' => 'required|integer|min:1'
			]);

			$admin = Auth::guard('admin')->user();
			if (!$admin) {
				return response()->json([
					'success' => false,
					'message' => 'Unauthorized'
				], 401);
			}

			$senderId = $admin->id;
			$message = $request->input('message');
			$clientMatterId = $request->input('client_matter_id');

			// Get client matter info to find the client_id
			$clientMatter = DB::table('client_matters')
				->where('id', $clientMatterId)
				->first();

			if (!$clientMatter) {
				return response()->json([
					'success' => false,
					'message' => 'Client matter not found'
				], 404);
			}

			$clientId = $clientMatter->client_id;
			
			if (!$clientId) {
				return response()->json([
					'success' => false,
					'message' => 'No client associated with this matter'
				], 422);
			}

			// Get sender information
			$sender = DB::table('admins')
				->where('id', $senderId)
				->select('id', 'first_name', 'last_name', DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) as full_name"))
				->first();

			$senderName = $sender ? $sender->full_name : 'Admin';
			$senderInitials = '';
			if ($sender) {
				$firstInitial = $sender->first_name ? strtoupper(substr($sender->first_name, 0, 1)) : '';
				$lastInitial = $sender->last_name ? strtoupper(substr($sender->last_name, 0, 1)) : '';
				$senderInitials = $firstInitial . $lastInitial;
			}

			// Get recipient information
			$recipientUser = DB::table('admins')
				->where('id', $clientId)
				->select('id', 'first_name', 'last_name', DB::raw("(COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')) as full_name"))
				->first();

			if (!$recipientUser) {
				return response()->json([
					'success' => false,
					'message' => 'Client user not found'
				], 404);
			}

			// Create message record
			$messageData = [
				'message' => $message,
				'sender' => $senderName,
				'sender_id' => $senderId,
				'sent_at' => now(),
				'client_matter_id' => $clientMatterId,
				'created_at' => now(),
				'updated_at' => now()
			];

			$messageId = DB::table('messages')->insertGetId($messageData);

			if ($messageId) {
				// Insert recipient into pivot table
				MessageRecipient::insert([
					'message_id' => $messageId,
					'recipient_id' => $clientId,
					'recipient' => $recipientUser->full_name,
					'is_read' => false,
					'read_at' => null,
					'created_at' => now(),
					'updated_at' => now()
				]);

				// Create activity log (message sent from website Client Portal tab)
				DB::table('activities_logs')->insert([
					'client_id' => $clientId,
					'created_by' => $senderId,
					'subject' => 'Message sent to client',
					'description' => 'Message sent from Client Portal tab (website) to client ' . $recipientUser->full_name . ' for matter ID: ' . $clientMatterId,
					'task_status' => 0,
					'pin' => 0,
					'source' => 'client_portal_web',
					'created_at' => now(),
					'updated_at' => now()
				]);

				// Broadcast message via Laravel Reverb (configured via BROADCAST_DRIVER in .env)
				$messageForBroadcast = [
					'id' => $messageId,
					'message' => $message,
					'sender' => $senderName,
					'sender_id' => $senderId,
					'sender_initials' => $senderInitials,
					'sent_at' => now()->toISOString(),
					'created_at' => now()->toISOString(),
					'client_matter_id' => $clientMatterId,
					'recipients' => [[
						'recipient_id' => $clientId,
						'recipient' => $recipientUser->full_name
					]]
				];

				// Broadcast to client and sender (with error handling)
				if (class_exists('\App\Events\MessageSent')) {
					try {
						broadcast(new \App\Events\MessageSent($messageForBroadcast, $clientId));
					} catch (\Exception $e) {
						Log::warning('Failed to broadcast message to client', [
							'client_id' => $clientId,
							'message_id' => $messageId,
							'error' => $e->getMessage(),
							'broadcast_driver' => config('broadcasting.default')
						]);
					}
					
					try {
						broadcast(new \App\Events\MessageSent($messageForBroadcast, $senderId));
					} catch (\Exception $e) {
						Log::warning('Failed to broadcast message to sender', [
							'sender_id' => $senderId,
							'message_id' => $messageId,
							'error' => $e->getMessage()
						]);
					}
				}

				// Send push notification to client
				try {
					$fcmService = new FCMService();
					$matterNo = $clientMatter ? ($clientMatter->client_unique_matter_no ?? 'ID: ' . $clientMatterId) : 'ID: ' . $clientMatterId;
					
					// Prepare notification title and body
					$notificationTitle = 'New Message';
					$notificationBody = $message ? (strlen($message) > 100 ? substr($message, 0, 100) . '...' : $message) : 'You have a new message';
					
					// Prepare notification data payload
					$notificationData = [
						'type' => 'chat',
						'userId' => (string)$senderId,
						'messageId' => (string)$messageId,
						'clientMatterId' => (string)$clientMatterId,
						'senderName' => $senderName,
						'matterNo' => $matterNo
					];
					
					// Send push notification to client
					try {
						$fcmService->sendToUser($clientId, $notificationTitle, $notificationBody, $notificationData);
					} catch (\Exception $e) {
						// Log error but don't fail the message sending
						Log::warning('Failed to send push notification to client', [
							'client_id' => $clientId,
							'message_id' => $messageId,
							'error' => $e->getMessage()
						]);
					}
				} catch (\Exception $e) {
					// Log error but don't fail the message sending
					Log::error('Failed to send push notification', [
						'message_id' => $messageId,
						'client_id' => $clientId,
						'error' => $e->getMessage(),
						'trace' => $e->getTraceAsString()
					]);
				}

				return response()->json([
					'success' => true,
					'message' => 'Message sent successfully',
					'data' => [
						'message_id' => $messageId,
						'message' => $messageForBroadcast
					]
				], 201);
			} else {
				return response()->json([
					'success' => false,
					'message' => 'Failed to send message'
				], 500);
			}

		} catch (\Exception $e) {
			Log::error('Send Message Error: ' . $e->getMessage(), [
				'user_id' => Auth::guard('admin')->id(),
				'client_matter_id' => $request->input('client_matter_id'),
				'trace' => $e->getTraceAsString()
			]);

			return response()->json([
				'success' => false,
				'message' => 'Failed to send message',
				'error' => $e->getMessage()
			], 500);
		}
	}
}
