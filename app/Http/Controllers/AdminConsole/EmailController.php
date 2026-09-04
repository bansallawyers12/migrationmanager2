<?php

namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\Staff;
use App\Services\SesSenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EmailController extends Controller
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
     * All Vendors.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $senders = $this->getVerifiedSenders();
        $emailRows = Email::query()->orderBy('id')->get();
        $metadataByEmail = $emailRows->keyBy(fn (Email $row): string => strtolower(trim((string) $row->email)));
        $staffNames = Staff::where('status', 1)
            ->get()
            ->keyBy('id')
            ->map(function ($staff) {
                return trim(($staff->first_name ?? '').' '.($staff->last_name ?? ''));
            });

        $listed = [];
        $lists = [];

        foreach ($senders as $sender) {
            $email = strtolower(trim((string) ($sender['email'] ?? '')));
            if ($email === '') {
                continue;
            }
            $metadata = $metadataByEmail->get($email);
            $lists[] = $this->presentEmailRow($email, (string) ($sender['name'] ?? ''), $metadata, $staffNames);
            $listed[$email] = true;
        }

        foreach ($emailRows as $row) {
            $email = strtolower(trim((string) $row->email));
            if ($email === '' || isset($listed[$email])) {
                continue;
            }
            $lists[] = $this->presentEmailRow($email, '', $row, $staffNames);
        }

        $totalData = count($lists);

        return view('AdminConsole.features.emails.index', compact(['lists', 'totalData']));
    }

    /**
     * From identities for Admin Console: emails table + SES_SENDERS env (not SendGrid).
     *
     * @return list<array{email: string, name: string, nickname?: string}>
     */
    private function getVerifiedSenders(): array
    {
        return app(SesSenderService::class)->getComposeSenders();
    }

    /**
     * @param  Collection<int|string, string>  $staffNames
     */
    private function presentEmailRow(string $email, string $senderName, ?Email $metadata, Collection $staffNames): object
    {
        $userNames = [];
        $userIds = json_decode($metadata->user_id ?? '[]', true);
        foreach ((array) $userIds as $userId) {
            $name = $staffNames->get((int) $userId);
            if (! empty($name)) {
                $userNames[] = $name;
            }
        }

        return (object) [
            'id' => $metadata->id ?? null,
            'email' => $metadata->email ?? $email,
            'display_name' => $metadata->display_name ?? $senderName,
            'email_signature' => $metadata->email_signature ?? '',
            'status' => isset($metadata->status) ? (int) $metadata->status : 0,
            'user_sharing' => implode(', ', $userNames),
        ];
    }

    public function create(Request $request)
    {
        return view('AdminConsole.features.emails.create');
    }

    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'email' => 'required|email|max:255|unique:emails',
                'display_name' => 'nullable|max:255',
                'password' => 'nullable|string|max:255',
                'smtp_host' => 'nullable|string|max:255',
                'smtp_port' => 'nullable|integer|min:1|max:65535',
                'smtp_encryption' => 'nullable|in:tls,ssl,starttls',
            ]);

            $requestData = $request->all();
            $obj = new Email;
            $obj->email = @$requestData['email'];
            $obj->email_signature = @$requestData['email_signature'];
            $obj->display_name = @$requestData['display_name'];
            $obj->status = @$requestData['status'];
            $obj->user_id = json_encode(@$requestData['users']);
            $this->applyOptionalMailboxCredentials($obj, $request);
            $saved = $obj->save();

            if (! $saved) {
                return redirect()->back()->with('error', config('constants.server_error'));
            }

            return redirect()->route('adminconsole.features.emails.index')->with('success', 'Email Added Successfully');
        }

        return view('AdminConsole.features.emails.create');
    }

    /**
     * Show the form for editing the specified email.
     */
    public function edit($id)
    {
        if (isset($id) && ! empty($id)) {
            $id = $this->decodeString($id);
            if (Email::where('id', '=', $id)->exists()) {
                $fetchedData = Email::find($id);

                return view('AdminConsole.features.emails.edit', compact(['fetchedData']));
            } else {
                return redirect()->route('adminconsole.features.emails.index')->with('error', 'Email Not Exist');
            }
        } else {
            return redirect()->route('adminconsole.features.emails.index')->with('error', config('constants.unauthorized'));
        }
    }

    /**
     * Update the specified email in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255|unique:emails,email,'.$id,
            'display_name' => 'nullable|max:255',
            'password' => 'nullable|string|max:255',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_encryption' => 'nullable|in:tls,ssl,starttls',
        ]);

        $requestData = $request->all();

        $obj = Email::find($id);
        if (! $obj) {
            return redirect()->route('adminconsole.features.emails.index')->with('error', 'Email Not Found');
        }

        $obj->email = @$requestData['email'];
        $obj->email_signature = @$requestData['email_signature'];
        $obj->display_name = @$requestData['display_name'];
        $obj->status = @$requestData['status'];
        $obj->user_id = json_encode(@$requestData['users']);
        $this->applyOptionalMailboxCredentials($obj, $request);
        $saved = $obj->save();

        if (! $saved) {
            return redirect()->back()->with('error', config('constants.server_error'));
        }

        return redirect()->route('adminconsole.features.emails.index')->with('success', 'Email Updated Successfully');
    }

    /**
     * Store Zoho mailbox credentials when provided. Empty password on edit keeps the current value.
     */
    private function applyOptionalMailboxCredentials(Email $email, Request $request): void
    {
        if ($request->filled('password')) {
            $email->password = (string) $request->input('password');
        }

        if ($request->filled('smtp_host')) {
            $email->smtp_host = (string) $request->input('smtp_host');
        }

        if ($request->filled('smtp_port')) {
            $email->smtp_port = (int) $request->input('smtp_port');
        }

        if ($request->filled('smtp_encryption')) {
            $email->smtp_encryption = (string) $request->input('smtp_encryption');
        }
    }
}
