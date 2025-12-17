<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Models\Admin;
use App\Models\EmailRecord;
use App\Models\ActivitiesLog;
use Auth;
use Hfig\MAPI;
use Hfig\MAPI\OLE\Pear;
use Hfig\MAPI\Message\Msg;
use Hfig\MAPI\MapiMessageFactory;
use GuzzleHttp\Client;

/**
 * ClientEmailController
 * 
 * Handles email management including inbox, sent items, email filtering,
 * and AI-powered message enhancement.
 */
class ClientEmailController extends Controller
{
    protected $openAiClient;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        
        $this->openAiClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Methods to be moved from ClientsController:
     * 
     * - uploadmail() - Upload mail
     * - uploadfetchmail() - Upload/fetch inbox email
     * - uploadsentfetchmail() - Upload/fetch sent email
     * - previewMsgFile() - Preview MSG file
     * - convertMsgToHtml() (private) - Convert MSG to HTML
     * - updatemailreadbit() - Update email read status
     * - reassiginboxemail() - Reassign inbox email
     * - reassigsentemail() - Reassign sent email
     * - filterEmails() - Filter inbox emails
     * - filterSentEmails() - Filter sent emails
     * - enhanceMessage() - AI-powered message enhancement
     */
}

