<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        // Check if this is an API request
        if ($request->is('api/*')) {
            return null; // API requests should not redirect
        }
        
        // Check if this is an email user request
        if ($request->is('email_users/*')) {
            return route('email_users.login');
        }
        
        // Default to CRM login (formerly admin.login)
        return route('crm.login');
    }
}
