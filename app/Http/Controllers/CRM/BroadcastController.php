<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Services\BroadcastNotificationService;

class BroadcastController extends Controller
{
    /**
     * Display the broadcast notification console.
     */
    public function index()
    {
        return view('crm.broadcasts.index', [
            'broadcastReadDelaySeconds' => BroadcastNotificationService::readDelaySeconds(),
        ]);
    }
}


