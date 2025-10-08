<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\DeviceToken;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ClientPortalController extends Controller
{
    
    /**
     * Login
     * POST /api/login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'device_token' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = Admin::where('email', $request->email)
                     ->where('role', 7)
                     ->where('cp_status', 1)
                     ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Create Sanctum token with device information
        $deviceName = $request->device_name ?? 'client-portal-app';
        $token = $admin->createToken($deviceName)->plainTextToken;

        // Handle device token for push notifications
        if ($request->device_token) {
            $this->handleDeviceToken($admin->id, $request->device_token, $deviceName);
        }

        // Generate refresh token
        $refreshToken = RefreshToken::generateToken($admin->id, $deviceName);

        // Update last login timestamp (if you have this field)
        $admin->touch();

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'refresh_token' => $refreshToken->token,
                'user' => [
                    'id' => $admin->id,
                    'name' => $admin->first_name . ' ' . $admin->last_name,
                    'email' => $admin->email,
                    'client_id' => $admin->client_id
                ]
            ]
        ], 200);
    }

    /**
     * Admin Login
     * POST /api/admin-login
     * 
     * Login for admin users with roles 1, 12, 13, 16
     */
    public function adminLogin(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'device_token' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = Admin::where('email', $request->email)
                     ->whereIn('role', [1, 12, 13, 16]) // Admin roles
                     ->where('status', 1) // Active status
                     ->first(); 

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Create Sanctum token with device information
        $deviceName = $request->device_name ?? 'admin-portal-app';
        $token = $admin->createToken($deviceName)->plainTextToken;

        // Handle device token for push notifications
        if ($request->device_token) {
            $this->handleDeviceToken($admin->id, $request->device_token, $deviceName);
        }

        // Generate refresh token
        $refreshToken = RefreshToken::generateToken($admin->id, $deviceName);

        // Update last login timestamp
        $admin->touch();

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'refresh_token' => $refreshToken->token,
                'user' => [
                    'id' => $admin->id,
                    'name' => $admin->first_name . ' ' . $admin->last_name,
                    'email' => $admin->email,
                    'role' => $admin->role
                ]
            ]
        ], 200);
    }

    /**
     * Logout
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            // Delete current access token
            $user->currentAccessToken()->delete();

            // Revoke all refresh tokens for this user
            RefreshToken::where('user_id', $user->id)->update(['is_revoked' => true]);

            // Optionally deactivate device token (but keep it for potential re-login)
            // DeviceToken::where('user_id', $user->id)->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout from all devices
     * POST /api/logout-all
     */
    public function logoutAll(Request $request)
    {
        try {
            $user = $request->user();
            
            // Delete all access tokens for the user
            $user->tokens()->delete();

            // Revoke all refresh tokens for the user
            RefreshToken::where('user_id', $user->id)->update(['is_revoked' => true]);

            // Deactivate all device tokens for the user
            DeviceToken::where('user_id', $user->id)->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Logged out from all devices successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout from all devices failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forgot Password
     * POST /api/forgot-password
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        
        // Check if admin exists
        $admin = Admin::where('email', $email)->first();
        
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'This user does not exist in our records'
            ], 404);
        }

        // Check if user role is client (7)
        if ($admin->role != 7) {
            return response()->json([
                'success' => false,
                'message' => 'Your record exists in DB but your role is not Client. So you cannot access this mobile app.'
            ], 403);
        }
        
        // Check if client portal status is active
        if ($admin->cp_status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to access this mobile app because your client portal is not active from website. Please contact Administrator.'
            ], 403);
        }
        
        // Generate 6-digit random code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update existing admin with new verification code and timestamp
        $admin->update([
            'cp_random_code' => $verificationCode,
            'cp_code_verify' => 0,
            'cp_token_generated_at' => now() // Store when code was generated
        ]);

        // Send verification email
        try {
            Mail::raw("Your password reset verification code is: {$verificationCode}\n\nThis code will expire in 10 minutes.\n\nIf you did not request this password reset, please ignore this email.", function ($message) use ($email) {
                $message->to($email)
                        ->subject('Client Portal - Password Reset Verification Code');
            });

            return response()->json([
                'success' => true,
                'message' => 'Password reset verification code sent successfully',
                'data' => [
                    'email' => $email,
                    'expires_in' => 600 // 10 minutes in seconds
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset Password
     * POST /api/reset-password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = Admin::where('email', $request->email)
                     ->where('cp_random_code', $request->code)
                     ->where('role', 7)
                     ->where('cp_status', 1)
                     ->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code or email'
            ], 400);
        }

        // Check if verification code has expired (10 minutes)
        if ($admin->cp_token_generated_at) {
            $tokenGeneratedAt = \Carbon\Carbon::parse($admin->cp_token_generated_at);
            if ($tokenGeneratedAt->diffInMinutes(now()) > 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification code has expired. Please request a new code.'
                ], 400);
            }
        }

        // Update password and clear verification code
        $admin->update([
            'password' => Hash::make($request->password),
            'cp_random_code' => null,
            'cp_code_verify' => 0,
            'cp_token_generated_at' => null // Clear the timestamp
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now login with your new password.',
            'data' => [
                'email' => $admin->email,
                'status' => 'password_reset'
            ]
        ], 200);
    }

    /**
     * Update Password
     * POST /api/update-password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Update password
        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ], 200);
    }


    /**
     * Get Client Profile
     * GET /api/profile
     */
    public function getProfile(Request $request)
    {
        $admin = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $admin->id,
                'client_id' => $admin->client_id,
                'first_name' => $admin->first_name,
                'last_name' => $admin->last_name,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'address' => $admin->address,
                'city' => $admin->city,
                'state' => $admin->state,
                'zip' => $admin->zip,
                'country' => $admin->country,
                'profile_img' => $admin->profile_img,
                'status' => $admin->status,
                'role' => $admin->role,
                'cp_status' => $admin->cp_status,
                'cp_code_verify' => $admin->cp_code_verify,
                'email_verified_at' => $admin->email_verified_at,
                'created_at' => $admin->created_at,
                'updated_at' => $admin->updated_at
            ]
        ], 200);
    }

    /**
     * Refresh Token
     * POST /api/refresh
     */
    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the refresh token
        $refreshToken = RefreshToken::where('token', $request->refresh_token)
                                   ->active()
                                   ->first();

        if (!$refreshToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired refresh token'
            ], 401);
        }

        $admin = $refreshToken->user;

        // Check if user is still active
        if (!$admin || $admin->role != 7 || $admin->cp_status != 1) {
            $refreshToken->revoke();
            return response()->json([
                'success' => false,
                'message' => 'User account is no longer active'
            ], 401);
        }

        // Revoke old refresh token
        $refreshToken->revoke();

        // Create new access token
        $deviceName = $refreshToken->device_name ?? 'client-portal-app';
        $newAccessToken = $admin->createToken($deviceName)->plainTextToken;

        // Generate new refresh token
        $newRefreshToken = RefreshToken::generateToken($admin->id, $deviceName);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $newAccessToken,
                'refresh_token' => $newRefreshToken->token,
                'user' => [
                    'id' => $admin->id,
                    'name' => $admin->first_name . ' ' . $admin->last_name,
                    'email' => $admin->email,
                    'client_id' => $admin->client_id
                ]
            ]
        ], 200);
    }

    /**
     * Handle device token registration/update
     */
    private function handleDeviceToken($userId, $deviceToken, $deviceName = null)
    {
        try {
            // Check if device token already exists
            $existingToken = DeviceToken::where('device_token', $deviceToken)->first();
            
            if ($existingToken) {
                // Update existing token
                $existingToken->update([
                    'user_id' => $userId,
                    'device_name' => $deviceName,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            } else {
                // Create new device token
                DeviceToken::create([
                    'user_id' => $userId,
                    'device_token' => $deviceToken,
                    'device_name' => $deviceName,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the login
            Log::error('Failed to handle device token: ' . $e->getMessage(), [
                'user_id' => $userId,
                'device_token' => substr($deviceToken, 0, 20) . '...'
            ]);
        }
    }

    /**
     * Update Profile
     * PUT /api/profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // Validate the request
            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:255',
                'address' => 'sometimes|string|max:500',
                'city' => 'sometimes|string|max:255',
                'state' => 'sometimes|string|max:255',
                'post_code' => 'sometimes|string|max:20',
                'country' => 'sometimes|string|max:255',
                'dob' => 'sometimes|date|before:today',
                'gender' => 'sometimes|string|in:Male,Female,Other',
                'marital_status' => 'sometimes|string|in:Single,Married,Divorced,Widowed,Other'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get the client record
            $client = DB::table('admins')->where('id', $clientId)->first();
            
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            // Prepare update data
            $updateData = [];
            $allowedFields = [
                'first_name', 'last_name', 'phone', 'address', 'city', 
                'state', 'post_code', 'country', 'dob', 'gender', 'marital_status'
            ];

            foreach ($allowedFields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->input($field);
                }
            }

            // Add updated_at timestamp
            $updateData['updated_at'] = now();

            // Update the client record
            $updated = DB::table('admins')
                ->where('id', $clientId)
                ->update($updateData);

            if ($updated) {
                // Get updated client data
                $updatedClient = DB::table('admins')->where('id', $clientId)->first();

                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'data' => [
                        'id' => $updatedClient->id,
                        'client_id' => $updatedClient->client_id,
                        'first_name' => $updatedClient->first_name,
                        'last_name' => $updatedClient->last_name,
                        'email' => $updatedClient->email,
                        'phone' => $updatedClient->phone,
                        'address' => $updatedClient->address,
                        'city' => $updatedClient->city,
                        'state' => $updatedClient->state,
                        'zip' => $updatedClient->zip,
                        'country' => $updatedClient->country,
                        'profile_img' => $updatedClient->profile_img,
                        'status' => $updatedClient->status,
                        'role' => $updatedClient->role,
                        'cp_status' => $updatedClient->cp_status,
                        'cp_code_verify' => $updatedClient->cp_code_verify,
                        'email_verified_at' => $updatedClient->email_verified_at,
                        'created_at' => $updatedClient->created_at,
                        'updated_at' => $updatedClient->updated_at
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update profile'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Update Profile API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}