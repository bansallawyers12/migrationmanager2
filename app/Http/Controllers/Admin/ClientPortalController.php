<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use Auth;
use Config;

/**
 * ClientPortalController
 * 
 * Handles client portal user management including creating portal users,
 * activating/deactivating access, and sending portal credentials.
 * 
 * Maps to: resources/views/Admin/clients/tabs/client_portal.blade.php
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
            Mail::send('emails.client_portal_active_email', ['content' => $message], function($mail) use ($emailAddress, $subject) {
                $mail->to($emailAddress)
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
            Mail::send('emails.client_portal_active_email', ['content' => $message], function($mail) use ($emailAddress, $subject) {
                $mail->to($emailAddress)
                     ->subject($subject)
                     ->from(config('mail.from.address'), config('mail.from.name'));
            });

        } catch (\Exception $e) {
            Log::error('Failed to send client portal deactivation email: ' . $e->getMessage());
            throw $e;
        }
    }
}

