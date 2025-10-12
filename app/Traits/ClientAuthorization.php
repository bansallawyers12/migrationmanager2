<?php

namespace App\Traits;

use Auth;
use Illuminate\Support\Facades\Redirect;

trait ClientAuthorization
{
    /**
     * Check if user has access to client module (module 20)
     *
     * @return array
     */
    protected function checkClientModuleAccess()
    {
        $roles = \App\Models\UserRole::find(Auth::user()->role);
        $newarray = json_decode($roles->module_access);
        $module_access = (array) $newarray;
        
        return $module_access;
    }

    /**
     * Check if user has access to a specific module
     *
     * @param string $moduleId
     * @return bool
     */
    protected function hasModuleAccess($moduleId = '20')
    {
        $module_access = $this->checkClientModuleAccess();
        return array_key_exists($moduleId, $module_access);
    }

    /**
     * Get module access or return empty result
     *
     * @param string $moduleId
     * @return bool
     */
    protected function requireModuleAccess($moduleId = '20')
    {
        if (!$this->hasModuleAccess($moduleId)) {
            return false;
        }
        return true;
    }
}

