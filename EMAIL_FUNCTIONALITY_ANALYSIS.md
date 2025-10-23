# Email Functionality Analysis - SMTP Configuration & Usage Across CRM

## Overview
This document provides a comprehensive analysis of how **SMTP email sending** is implemented across different sections of the Migration Manager CRM. It covers configuration, email address management, and the various workflows where emails are sent to clients, leads, and other stakeholders.

---

## 1. SMTP Configuration Architecture

### 1.1 Configuration Files

#### **config/mail.php**
- **Default Mailer**: `env('MAIL_MAILER', 'log')` - defaults to logging emails
- **Supported Transports**: SMTP, Sendmail, Mailgun, SES, Postmark, Log, Array
- **SMTP Configuration**:
  ```php
  'smtp' => [
      'host' => env('MAIL_HOST', '127.0.0.1'),
      'port' => env('MAIL_PORT', 2525),
      'username' => env('MAIL_USERNAME'),
      'password' => env('MAIL_PASSWORD'),
      'encryption' => env('MAIL_ENCRYPTION', 'tls'),
  ]
  ```
- **Global From Address**: Configured via `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`
- **Custom SMTP Mailer**: Additional `custom_smtp` mailer configured for dynamic SMTP settings

#### **Environment Variables (Fallback Configuration)**
The application falls back to these environment variables when no database configuration exists:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.zoho.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="Your Name"
```

---

## 2. Database Schema for SMTP Accounts

### 2.1 emails Table (Primary SMTP Accounts)

**Purpose**: Stores SMTP email accounts for **sending outgoing emails**

**Schema**:
```sql
- id (primary key)
- email (unique) - The email address
- password (plain text - ⚠️ SECURITY CONCERN)
- display_name - Display name for from field
- smtp_host (default: 'smtp.zoho.com')
- smtp_port (default: 587)
- smtp_encryption (default: 'tls')
- status (boolean - active/inactive)
- user_id (JSON array of assigned user IDs)
- email_signature (text)
- created_at, updated_at
```

**Key Features**:
- Managed by administrators in Admin Console
- Supports multiple SMTP accounts
- Each account can have custom SMTP settings
- Assigned to specific users via JSON array
- **Default Provider**: Zoho Mail (smtp.zoho.com:587 TLS)

### 2.2 email_templates Table

**Purpose**: Stores reusable email templates with variables

**Schema**:
```sql
- id, title, subject, variables, alias, email_from, description
- created_at, updated_at
```

**Usage**:
- Templates accessed by `alias` field
- Support variable placeholders (e.g., `{Client First Name}`, `{Company Name}`)
- String replacement performed at send time

### 2.3 mail_reports Table

**Purpose**: Logs all sent emails for tracking and auditing

**Schema**:
```sql
- user_id, from_mail, to_mail, cc, template_id, receipt_id
- subject, message, mail_type, client_id, client_matter_id
- attachments (JSON), created_at, updated_at
```

---

## 3. Email Models

### 3.1 Email Model (`app/Models/Email.php`)
- **Purpose**: Represents SMTP email accounts for sending
- **Table**: `emails`
- **Fillable**: `email`, `password`, `display_name`, `smtp_host`, `smtp_port`, `smtp_encryption`, `status`
- **Hidden**: `password`
- **Default Attribute Values**:
  ```php
  smtp_host → 'smtp.zoho.com'
  smtp_port → 587
  smtp_encryption → 'tls'
  ```

### 3.2 EmailTemplate Model (`app/Models/EmailTemplate.php`)
- **Purpose**: Reusable email templates
- **Fillable**: `title`, `subject`, `variables`, `alias`, `email_from`, `description`

---

## 4. Email Services (Core SMTP Logic)

### 4.1 EmailService (`app/Services/EmailService.php`)

**Primary service for sending emails with database SMTP configurations.**

#### Key Methods:

##### `getAllActiveEmails()`
Returns all active SMTP email accounts for dropdowns/selection.

##### `sendEmail($view, $data, $to, $subject, $fromEmailId, $attachments = [], $cc = [])`
**Main email sending method**:
1. Retrieves email config from database by email address
2. Dynamically configures Laravel's SMTP settings at runtime:
   ```php
   config([
       'mail.mailers.smtp.host' => 'smtp.zoho.com',
       'mail.mailers.smtp.port' => 587,
       'mail.mailers.smtp.encryption' => 'tls',
       'mail.mailers.smtp.username' => $emailConfig->email,
       'mail.mailers.smtp.password' => $emailConfig->password,
       'mail.from.address' => $emailConfig->email,
       'mail.from.name' => $emailConfig->display_name,
   ]);
   ```
3. Sends email via Laravel Mail facade
4. Supports attachments and CC

**Usage**: Used in the modern email sending workflow from client details page.

---

### 4.2 EmailConfigService (`app/Services/EmailConfigService.php`)

**Centralized service for managing SMTP configurations (especially for signature/document emails).**

#### Key Methods:

##### `forAccountById(int $emailId): array`
Gets SMTP config by email record ID.

##### `forAccount(string $email): array`
Gets SMTP config by email address.

##### `buildConfig(Email $emailConfig): array`
Builds standardized config array:
```php
return [
    'host' => $emailConfig->smtp_host ?? 'smtp.zoho.com',
    'port' => $emailConfig->smtp_port ?? 587,
    'encryption' => $emailConfig->smtp_encryption ?? 'tls',
    'username' => $emailConfig->email,
    'password' => $emailConfig->password,
    'from_address' => $emailConfig->email,
    'from_name' => $emailConfig->display_name ?? 'Bansal Migration',
    'timeout' => 30,
];
```

##### `applyConfig(array $config): void`
Applies SMTP configuration to Laravel mail config at runtime:
```php
config([
    'mail.default' => 'smtp',
    'mail.mailers.smtp.host' => $config['host'],
    'mail.mailers.smtp.port' => $config['port'],
    'mail.mailers.smtp.encryption' => $config['encryption'],
    'mail.mailers.smtp.username' => $config['username'],
    'mail.mailers.smtp.password' => $config['password'],
    'mail.from.address' => $config['from_address'],
    'mail.from.name' => $config['from_name'],
]);
```

##### `getActiveAccounts()`
Returns all active email accounts for dropdown selection.

##### `getDefaultAccount(): ?array`
Gets default SMTP account (first active) or falls back to env vars.

##### `validateConfig(array $config): bool`
Validates SMTP configuration by testing connection.

**Usage**: Primary service used for signature emails and document-related communications.

---

### 4.3 CustomMailService (`app/Services/CustomMailService.php`)

**Advanced service for per-email custom SMTP with Symfony Mailer.**

#### Key Methods:

##### `sendWithCustomSmtp($to, $mailable, $senderConfig = null)`
- Creates custom Symfony SMTP transport on-the-fly
- Allows different SMTP settings per email
- Falls back to default mailer if no custom config

##### `sendEmailTemplate($replace, $replace_with, $alias, $to, $subject, $sender, $sendername)`
- Retrieves template from database
- Performs string replacements
- Sends via custom SMTP or default

**Usage**: Used in template-based email sending (legacy method from base Controller).

---

## 5. Base Controller Email Methods

Located in `app/Http/Controllers/Controller.php`:

### 5.1 `send_email_template(...)`

**Signature**:
```php
protected function send_email_template(
    $replace = array(),        // Variables to replace (e.g., ['{Client First Name}'])
    $replace_with = array(),   // Replacement values
    $alias = null,             // Template alias
    $to = null,                // Recipient email
    $subject = null,           // Email subject
    $sender = null,            // From email address
    $sendername = null         // From name
)
```

**Process**:
1. Retrieves email template by alias from `email_templates` table
2. Performs string replacements
3. Uses `CustomMailService::sendEmailTemplate()` to send
4. Returns true/false

**Usage**: Available to all controllers extending base Controller. Used for template-based emails.

---

### 5.2 `send_compose_template(...)`

**Signature**:
```php
protected function send_compose_template(
    $to = null,                // Recipient(s) - semicolon separated
    $subject = null,           // Email subject
    $sender = null,            // From email
    $content,                  // HTML content
    $sendername,               // From name
    $array = array(),          // Attachments array
    $cc = array()              // CC recipients
)
```

**Process**:
1. Explodes recipients by semicolon
2. Creates `CommonMail` mailable with content
3. Sends via `Mail::to()->send()`
4. Supports CC and attachments

**Usage**: For custom composed emails without templates.

---

### 5.3 `send_attachment_email_template(...)`

**Signature**:
```php
protected function send_attachment_email_template(
    $replace = array(),
    $replace_with = array(),
    $alias = null,
    $to = null,
    $subject = null,
    $sender = null,
    $invoicearray               // Contains file, file_name, view, name, etc.
)
```

**Process**:
1. Retrieves template and performs replacements
2. Creates `InvoiceEmailManager` mailable
3. Queues email with attachment (PDF invoices/receipts)

**Usage**: Specifically for invoice and receipt emails with PDF attachments.

---

### 5.4 `send_multipleattachment_email_template(...)`

Similar to above but uses `MultipleattachmentEmailManager` for multiple file attachments.

---

## 6. Email Sending Across CRM Sections

### 6.1 Client Personal Details Page Email

**Location**: Client detail page → Compose Email modal
**Route**: `POST /admin/sendmail`
**Controller**: `App\Http\Controllers\Admin\AdminController@sendmail`
**View**: `resources/views/Admin/clients/detail.blade.php` (line 469)

#### Workflow:

1. **User Action**: Click email icon on client detail page
2. **Modal Opens**: Compose email modal displays
3. **From Email Selection**: Dropdown populated from active SMTP accounts:
   ```php
   $emails = \App\Models\Email::select('email')->where('status', 1)->get();
   ```
4. **Recipient Selection**: AJAX-powered select for clients/leads
5. **CC Selection**: Optional CC field
6. **Attachments**:
   - Upload new files (`attach[]`)
   - Select from checklists (`checklistfile[]`)
   - Select from documents (`checklistfile_document[]`)
7. **Template Selection**: Optional CRM email template
8. **Compose & Send**

#### Backend Process (`AdminController@sendmail`):

```php
public function sendmail(Request $request) {
    // 1. Save to mail_reports table for tracking
    $obj = new \App\Models\MailReport;
    $obj->user_id = Auth::user()->id;
    $obj->from_mail = $requestData['email_from'];
    $obj->to_mail = implode(',', $requestData['email_to']);
    $obj->cc = implode(',', $requestData['email_cc']);
    $obj->template_id = $requestData['template'];
    $obj->subject = $requestData['subject'];
    $obj->message = $requestData['message'];
    $obj->mail_type = $requestData['mail_type'];
    $obj->client_id = $requestData['client_id'];
    $obj->client_matter_id = $requestData['compose_client_matter_id'];
    $obj->attachments = json_encode($attachments);
    $obj->save();
    
    // 2. Process attachments (checklist files, documents, uploads)
    $attachments = [];
    // ... attachment processing ...
    
    // 3. Perform variable replacements
    $subject = str_replace('{Client First Name}', $client->first_name, $subject);
    $message = str_replace('{Client First Name}', $client->first_name, $message);
    $message = str_replace('{Company Name}', Auth::user()->company_name, $message);
    
    // 4. Send email using EmailService
    $this->emailService->sendEmail(
        'emails.common',
        ['content' => $message],
        $client->email,
        $subject,
        $requestData['email_from'],  // From email (SMTP account)
        $attachments,
        $ccarray
    );
    
    // 5. Log activity
    $objs = new \App\Models\ActivitiesLog;
    $objs->client_id = $client->id;
    $objs->created_by = Auth::user()->id;
    $objs->subject = "Email sent to client";
    $objs->save();
}
```

#### Features:
- ✅ Multiple SMTP account selection
- ✅ Dynamic SMTP configuration per send
- ✅ Attachment support (files, checklists, documents)
- ✅ CC support
- ✅ Variable replacements
- ✅ Email logging/tracking
- ✅ Activity log integration
- ✅ Template support

#### SMTP Configuration Method:
Uses **EmailService** which dynamically configures SMTP settings from the selected "From Email" account.

---

### 6.2 Signature Link Emails (E-Signature System)

**Location**: Signature Dashboard → Add Signer → Send Link
**Route**: `POST /admin/signatures/{document}/add-signer`
**Controller**: `App\Http\Controllers\Admin\SignatureDashboardController@addSigner`

#### Workflow:

1. **Admin Action**: Adds signer to document
2. **Email Configuration**: Optional "From Email" selection
3. **Template Selection**: Uses `emails.signature.send` or custom template
4. **Signing URL Generation**: Unique token-based URL generated
5. **Email Sent**: Signing link sent to signer's email

#### Backend Process:

```php
public function addSigner(Request $request, $documentId) {
    // 1. Create signer record
    $signer = Signer::create([
        'document_id' => $documentId,
        'name' => $request->name,
        'email' => $request->email,
        'token' => Str::random(64),
        'from_email' => $request->from_email,
        'email_template' => $request->email_template,
        'email_subject' => $request->email_subject,
        'email_message' => $request->email_message,
    ]);
    
    // 2. Apply SMTP configuration
    if ($request->from_email) {
        $emailConfig = app(\App\Services\EmailConfigService::class)
                          ->forAccount($request->from_email);
        app(\App\Services\EmailConfigService::class)->applyConfig($emailConfig);
    } else {
        // Use default SMTP account
        $defaultConfig = app(\App\Services\EmailConfigService::class)
                            ->getDefaultAccount();
        if ($defaultConfig) {
            app(\App\Services\EmailConfigService::class)->applyConfig($defaultConfig);
        }
    }
    
    // 3. Generate signing URL
    $signingUrl = url("/sign/{$document->id}/{$signer->token}");
    
    // 4. Prepare template data
    $templateData = [
        'signerName' => $signer->name,
        'documentTitle' => $document->display_title,
        'signingUrl' => $signingUrl,
        'message' => $request->email_message,
        'documentType' => $document->document_type,
    ];
    
    // 5. Send email
    Mail::send($template, $templateData, function ($message) use ($signer, $subject) {
        $message->to($signer->email, $signer->name)
               ->subject($subject);
    });
}
```

#### Send to All Signers Feature:

**Route**: `POST /admin/signatures/{document}/send`
**Method**: `sendToSigners()`

```php
public function sendToSigners($documentId) {
    $document = Document::findOrFail($documentId);
    $pendingSigners = $document->signers()->where('status', 'pending')->get();
    
    foreach ($pendingSigners as $signer) {
        // Apply signer's stored SMTP config or default
        if ($signer->from_email) {
            $emailConfig = app(\App\Services\EmailConfigService::class)
                              ->forAccount($signer->from_email);
            app(\App\Services\EmailConfigService::class)->applyConfig($emailConfig);
        }
        
        // Send email with signing link
        Mail::send($template, $templateData, function ($message) use ($signer, $subject) {
            $message->to($signer->email, $signer->name)->subject($subject);
        });
    }
    
    $document->update(['status' => 'sent']);
}
```

#### Features:
- ✅ Per-signer SMTP account configuration
- ✅ Custom email templates per signer
- ✅ Custom email subject/message per signer
- ✅ Signing URL with secure token
- ✅ Batch sending to all signers
- ✅ Document type-specific templates

#### SMTP Configuration Method:
Uses **EmailConfigService** with optional per-signer SMTP account, falls back to default.

---

### 6.3 Signature Reminders

**Service**: `App\Services\SignatureService@remind`
**Usage**: Send reminder to signers who haven't signed

#### Process:

```php
public function remind(Signer $signer, array $options = []): bool {
    // 1. Check reminder limits (max 3, 24 hours between)
    if ($signer->reminder_count >= 3) {
        throw new \Exception('Maximum reminders already sent');
    }
    
    // 2. Apply SMTP configuration
    if (isset($options['from_email'])) {
        $emailConfig = $this->emailConfigService->forAccount($options['from_email']);
        $this->emailConfigService->applyConfig($emailConfig);
    } else {
        $defaultConfig = $this->emailConfigService->getDefaultAccount();
        if ($defaultConfig) {
            $this->emailConfigService->applyConfig($defaultConfig);
        }
    }
    
    // 3. Send reminder email
    Mail::send('emails.signature.reminder', $templateData, function ($message) use ($signer) {
        $message->to($signer->email, $signer->name)
               ->subject('Reminder: Please Sign Your Document - Bansal Migration');
    });
    
    // 4. Update reminder tracking
    $signer->update([
        'last_reminder_sent_at' => now(),
        'reminder_count' => $signer->reminder_count + 1
    ]);
}
```

---

### 6.4 Application Emails

**Location**: Application detail page → Email section
**Route**: `POST /application-sendmail`
**Controller**: `App\Http\Controllers\Admin\ApplicationsController@applicationsendmail`

#### Workflow:

Similar to client emails but application-specific:

```php
public function applicationsendmail(Request $request) {
    $to = $requestData['to'];
    $subject = $requestData['subject'];
    $message = $requestData['message'];
    
    // Variable replacements
    $client = \App\Models\Admin::Where('email', $to)->first();
    $subject = str_replace('{Client First Name}', $client->first_name, $subject);
    $message = str_replace('{Client First Name}', $client->first_name, $message);
    $message = str_replace('{Company Name}', Auth::user()->company_name, $message);
    
    // Send email using base controller method
    $sent = $this->send_compose_template(
        $message,              // Content
        'digitrex',            // Subject
        $to,                   // Recipient
        $subject,              // Subject
        'support@digitrex.live',  // From email
        $array,                // Attachments
        $ccarray               // CC
    );
    
    // Log activity
    $objs = new \App\Models\ApplicationActivitiesLog;
    $objs->stage = $request->type;
    $objs->type = 'appointment';
    $objs->comment = 'sent an email';
    $objs->title = '<b>Subject : '.$subject.'</b>';
    $objs->description = '<b>To: '.$to.'</b></br>'.$message;
    $objs->app_id = $request->noteid;
    $objs->user_id = Auth::user()->id;
    $objs->save();
}
```

**Note**: This uses a hardcoded sender email. Could be improved to use EmailService.

---

### 6.5 Appointment Confirmation Emails

**Location**: Appointment booking system
**Controller**: `App\Http\Controllers\Admin\AppointmentsController`
**Service**: `App\Services\BansalAppointmentSync\NotificationService`

#### Workflow:

##### Stripe Payment Confirmation:
```php
// In AppointmentsController
$details = [
    'name' => $adminInfo->first_name,
    'phone' => $adminInfo->phone,
    'date' => $appointment_date,
    'time' => $time,
    'paymentId' => $charge->id,
    'amount' => $charge->amount / 100,
];

Mail::to($adminInfo->email)->send(new \App\Mail\AppointmentStripeMail($details));
```

##### Detailed Confirmation Email:
```php
// In NotificationService
public function sendDetailedConfirmationEmail(BookingAppointment $appointment): bool {
    $details = [
        'client_name' => $appointment->client_name,
        'appointment_datetime' => $appointment->appointment_datetime,
        'timeslot_full' => $appointment->timeslot_full,
        'location' => $appointment->location,
        'consultant' => $appointment->consultant?->name,
        'service_type' => $appointment->service_type,
        'admin_notes' => $appointment->admin_notes,
    ];
    
    Mail::to($appointment->client_email)
        ->send(new \App\Mail\AppointmentDetailedConfirmation($details));
    
    $appointment->update([
        'confirmation_email_sent' => true,
        'confirmation_email_sent_at' => now()
    ]);
}
```

#### SMTP Configuration:
Uses **default configuration** from `config('mail.from.address')` and `config('mail.from.name')`.

**Improvement Opportunity**: Could integrate EmailConfigService for dynamic SMTP selection.

---

### 6.6 Invoice & Receipt Emails

**Location**: Client Accounts → Send Invoice/Receipt
**Controller**: `App\Http\Controllers\Admin\ClientsController@sendmail` (handles invoice attachments)

#### Workflow:

```php
// Generate PDF invoice
if(isset($requestData['invreceipt'])){
    $invoicedetail = \App\Models\Invoice::where('id', '=', $requestData['invreceipt'])->first();
    $pdf = PDF::loadView('emails.invoice', compact([...]));
    $output = $pdf->output();
    $invoicefilename = 'invoice_'.$reciept_id.'.pdf';
    file_put_contents(public_path('invoices/'.$invoicefilename), $output);
    
    $array['file'] = public_path() . '/invoices/'.$invoicefilename;
    $array['file_name'] = $invoicefilename;
}

// Generate PDF receipt
if(isset($requestData['receipt'])){
    $fetchedData = InvoicePayment::where('id', '=', $requestData['receipt'])->first();
    $pdf = PDF::loadView('emails.reciept', compact('fetchedData'));
    $output = $pdf->output();
    $invoicefilename = 'receipt_'.$reciept_id.'.pdf';
    file_put_contents(public_path('invoices/'.$invoicefilename), $output);
    
    $array['file'] = public_path() . '/invoices/'.$invoicefilename;
    $array['file_name'] = $invoicefilename;
}

// Send email with attachment using EmailService
$this->emailService->sendEmail(
    'emails.common',
    ['content' => $message],
    $client->email,
    $subject,
    $requestData['email_from'],
    [$array['file']],
    $ccarray
);
```

#### Features:
- ✅ Dynamic PDF generation
- ✅ Invoice/receipt as attachment
- ✅ SMTP account selection
- ✅ Email logging

---

### 6.7 Hubdoc Invoice Integration

**Purpose**: Automatically forward invoices to Hubdoc for accounting

```php
// In ClientsController
$invoiceData = [
    'invoice' => $invoice,
    'client' => $client,
    'file_path' => $pdfPath,
];

Mail::to(env('HUBDOC_EMAIL', 'easyvisa.1ae4@app.hubdoc.com'))
    ->send(new HubdocInvoiceMail($invoiceData));
```

**SMTP**: Uses default configuration.

---

### 6.8 Client Portal Access Emails

**Location**: Client detail → Client Portal toggle
**Controller**: `App\Http\Controllers\Admin\ClientPortalController@toggleStatus`

#### Activation Email:

```php
private function sendClientPortalActivationEmail($client, $password) {
    $subject = 'Client Portal Access Activated - Bansal Immigration';
    $message = "
        <h2>Client Portal Access Activated</h2>
        <p>Dear {$client->first_name} {$client->last_name},</p>
        <p>Your client portal has been activated successfully.</p>
        <p><strong>Email:</strong> {$client->email}</p>
        <p><strong>Password:</strong> {$password}</p>
        <p>Download the mobile app: [link]</p>
    ";
    
    Mail::send('emails.client_portal_active_email', ['content' => $message], 
        function($message) use ($emailAddress, $subject) {
            $message->to($emailAddress)
                   ->subject($subject)
                   ->from(config('mail.from.address'), config('mail.from.name'));
        });
}
```

#### Deactivation Email:

Similar structure but notifies about portal deactivation.

#### Password Reset Verification Code:

```php
// In API ClientPortalController
$verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

Mail::raw("Your password reset verification code is: {$verificationCode}\n\n" .
          "This code will expire in 10 minutes.", 
    function ($message) use ($email) {
        $message->to($email)->subject('Client Portal - Password Reset Verification Code');
    });
```

**SMTP**: Uses default configuration from env/config files.

---

### 6.9 Email Verification (User Registration)

**Location**: User registration process
**Controller**: `App\Http\Controllers\Auth\RegisterController`

```php
$replaceav = ['[NAME]', '[EMAIL]', '[VERIFY_LINK]'];
$replace_withav = [
    $result->first_name,
    $result->email,
    route('verify.email', ['token' => $result->email_verification_token])
];

$this->send_email_template(
    $replaceav,
    $replace_withav,
    'verify-email',  // Template alias
    $result->email,
    'Verify Your Email Address',
    config('mail.from.address')
);
```

---

### 6.10 Visa Expiry Reminders

**Mailable**: `App\Mail\VisaExpireReminderMail`

Used for automated visa expiry notifications to clients.

---

## 7. Mailable Classes

### Summary of All Mailables:

| Mailable Class | Purpose | Attachments | SMTP Config |
|---|---|---|---|
| **CommonMail** | General-purpose dynamic content | ✅ Yes (files, multiple) | Via EmailService |
| **InvoiceEmailManager** | Invoice with PDF | ✅ Yes (PDF) | Via EmailService |
| **MultipleattachmentEmailManager** | Multiple attachments | ✅ Yes (multiple) | Via EmailService |
| **AppointmentMail** | Appointment notifications | ❌ No | Default |
| **AppointmentDetailedConfirmation** | Detailed appointment info | ❌ No | Default |
| **AppointmentStripeMail** | Payment confirmation | ❌ No | Default |
| **HubdocInvoiceMail** | Hubdoc integration | ✅ Yes (PDF) | Default |
| **VisaExpireReminderMail** | Visa expiry reminders | ❌ No | Default |
| **EmailVerificationMail** | Email verification | ❌ No | Default |

---

## 8. SMTP Configuration Methods Comparison

### Method 1: EmailService (Modern Approach)

**Used in**: Client details page, invoice emails

```php
// Retrieve SMTP config from database
$emailConfig = Email::where('email', $fromEmailId)->firstOrFail();

// Apply config dynamically
config([
    'mail.mailers.smtp.host' => 'smtp.zoho.com',
    'mail.mailers.smtp.port' => 587,
    'mail.mailers.smtp.encryption' => 'tls',
    'mail.mailers.smtp.username' => $emailConfig->email,
    'mail.mailers.smtp.password' => $emailConfig->password,
    'mail.from.address' => $emailConfig->email,
    'mail.from.name' => $emailConfig->display_name,
]);

// Send email
$this->emailService->sendEmail($view, $data, $to, $subject, $fromEmailId, $attachments, $cc);
```

**Advantages**:
- ✅ Service-based, clean architecture
- ✅ Easy to test and maintain
- ✅ Dynamic SMTP per email
- ✅ Supports attachments and CC

---

### Method 2: EmailConfigService (Signature System)

**Used in**: Signature emails, document sending

```php
// Get config by email address
$emailConfig = app(\App\Services\EmailConfigService::class)->forAccount($email);

// Apply config to Laravel Mail
app(\App\Services\EmailConfigService::class)->applyConfig($emailConfig);

// Send email
Mail::send($template, $data, function ($message) use ($to, $subject) {
    $message->to($to)->subject($subject);
});
```

**Advantages**:
- ✅ Centralized configuration management
- ✅ Validation support
- ✅ Fallback to default account
- ✅ Clean separation of concerns

---

### Method 3: CustomMailService (Legacy Template Method)

**Used in**: Template-based emails via base Controller

```php
CustomMailService::sendEmailTemplate($replace, $replace_with, $alias, $to, $subject, $sender, $sendername);
```

**Advantages**:
- ✅ Template support built-in
- ✅ Variable replacement
- ✅ Custom Symfony mailer support

**Disadvantages**:
- ⚠️ Less flexible than modern approaches
- ⚠️ Static method, harder to test

---

### Method 4: Direct Mail Facade (Simplest)

**Used in**: Appointment emails, portal emails

```php
Mail::send($view, $data, function ($message) use ($to, $subject) {
    $message->to($to)
           ->subject($subject)
           ->from(config('mail.from.address'), config('mail.from.name'));
});
```

**Advantages**:
- ✅ Simple and straightforward
- ✅ Good for one-off emails

**Disadvantages**:
- ⚠️ Uses default SMTP only
- ⚠️ No dynamic SMTP selection
- ⚠️ Not suitable for multi-account scenarios

---

## 9. Email Sending Workflow Patterns

### Pattern 1: User-Selected SMTP (Client Emails)

```
User Action (Compose Email) 
    ↓
Select "From Email" (SMTP Account)
    ↓
EmailService retrieves SMTP credentials
    ↓
SMTP config applied dynamically
    ↓
Email sent via Laravel Mail
    ↓
Logged to mail_reports table
```

**Key Feature**: User chooses which email account to send from.

---

### Pattern 2: Pre-Configured SMTP (Signature Emails)

```
Admin adds signer with optional "From Email"
    ↓
Signer record stores from_email preference
    ↓
When sending: EmailConfigService applies stored config
    ↓
Falls back to default if not specified
    ↓
Email sent to signer
```

**Key Feature**: SMTP config stored per signer, allows different accounts for different signers.

---

### Pattern 3: Default SMTP (System Emails)

```
System event (e.g., appointment booked)
    ↓
Uses default SMTP from config/env
    ↓
Mail::send() with default config
    ↓
Email sent
```

**Key Feature**: No dynamic configuration, uses app defaults.

---

### Pattern 4: Template-Based with Variables (Legacy)

```
Controller calls send_email_template()
    ↓
Template retrieved by alias
    ↓
String replacements performed
    ↓
CustomMailService sends email
    ↓
SMTP from sender parameter
```

**Key Feature**: Reusable templates with variable placeholders.

---

## 10. Email Templates & Variables

### Common Variables Across Templates:

| Variable | Replacement | Usage |
|---|---|---|
| `{Client First Name}` | `$client->first_name` | Client emails |
| `{Client Last Name}` | `$client->last_name` | Client emails |
| `{Client Assignee Name}` | `$assignee->first_name` | Client communication |
| `{Company Name}` | `Auth::user()->company_name` | All emails |
| `[NAME]` | User name | Verification emails |
| `[EMAIL]` | User email | Verification emails |
| `[VERIFY_LINK]` | Verification URL | Verification emails |

### Template Storage:

Templates stored in `email_templates` table with:
- `alias` - Unique identifier (e.g., 'verify-email', 'welcome_email')
- `description` - HTML content with variables
- `subject` - Email subject
- `variables` - List of available variables

### Template Categories:

1. **CRM Email Templates** (`crm_email_templates`)
   - General client communication
   - Managed by admins
   
2. **Matter Email Templates** (`matter_email_templates`)
   - Matter-specific templates
   - Linked to specific matters
   
3. **Other Email Templates** (`matter_other_email_templates`)
   - Miscellaneous templates

---

## 11. Admin Console Email Management

### Managing SMTP Accounts

**Route**: `/adminconsole/features/emails`
**Controller**: `App\Http\Controllers\AdminConsole\EmailController`

#### CRUD Operations:

##### List All Email Accounts
```php
public function index(Request $request) {
    $lists = Email::where('id', '!=', '')
                  ->sortable(['id' => 'desc'])
                  ->paginate(config('constants.limit'));
    
    return view('AdminConsole.features.emails.index', compact(['lists', 'totalData']));
}
```

##### Create Email Account
```php
public function store(Request $request) {
    $this->validate($request, ['email' => 'required|max:255|unique:emails']);
    
    $obj = new Email;
    $obj->email = $requestData['email'];
    $obj->email_signature = $requestData['email_signature'];
    $obj->display_name = $requestData['display_name'];
    $obj->password = $requestData['password'];  // ⚠️ Plain text
    $obj->status = $requestData['status'];
    $obj->user_id = json_encode($requestData['users']);  // Assigned users
    $obj->save();
}
```

##### Edit & Update Email Account
```php
public function update(Request $request, $id) {
    $this->validate($request, ['email' => 'required|max:255|unique:emails,email,'.$id]);
    
    $obj = Email::find($id);
    $obj->email = $requestData['email'];
    $obj->display_name = $requestData['display_name'];
    $obj->password = $requestData['password'];
    $obj->status = $requestData['status'];
    $obj->user_id = json_encode($requestData['users']);
    $obj->save();
}
```

#### Features:
- ✅ Add/edit/delete SMTP accounts
- ✅ Set display name
- ✅ Email signature support
- ✅ Active/inactive status
- ✅ Assign accounts to specific users

---

## 12. Default Email Provider

### Zoho Mail Configuration

**Primary Provider**: Zoho Mail is the default and primary email provider.

**Default Settings**:
```php
'smtp_host' => 'smtp.zoho.com'
'smtp_port' => 587
'smtp_encryption' => 'tls'
```

**Why Zoho?**:
- Hardcoded in `EmailService` (line 42)
- Used across signature and client email systems
- Reliable for business emails
- Supports custom domains

**Extensibility**:
The system supports custom SMTP settings in the database, so other providers (Gmail, Outlook, etc.) can be configured by:
1. Adding email account in admin console
2. Setting custom `smtp_host`, `smtp_port`, `smtp_encryption`
3. System will use those settings instead of defaults

---

## 13. Email Logging & Tracking

### 13.1 mail_reports Table

Every email sent through the `AdminController@sendmail` method is logged:

```php
$obj = new \App\Models\MailReport;
$obj->user_id = Auth::user()->id;           // Who sent it
$obj->from_mail = $requestData['email_from']; // From email
$obj->to_mail = implode(',', $requestData['email_to']); // Recipients
$obj->cc = implode(',', $requestData['email_cc']);       // CC
$obj->template_id = $requestData['template'];  // Template used
$obj->subject = $requestData['subject'];
$obj->message = $requestData['message'];
$obj->mail_type = $requestData['mail_type'];   // Email type
$obj->client_id = $requestData['client_id'];   // Associated client
$obj->client_matter_id = $requestData['compose_client_matter_id']; // Matter
$obj->attachments = json_encode($attachments); // Attachment metadata
$obj->save();
```

**Benefits**:
- 📊 Complete email audit trail
- 📊 Track communication history per client
- 📊 Monitor which templates are used
- 📊 Attachment tracking

---

### 13.2 Activity Logs

Client-facing emails also create activity log entries:

```php
$objs = new \App\Models\ActivitiesLog;
$objs->client_id = $client->id;
$objs->created_by = Auth::user()->id;
$objs->subject = "Email sent to client";
$objs->save();
```

**Integration**:
- Appears in client timeline
- Visible on client detail page
- Helps track all interactions

---

## 14. Security Analysis

### 14.1 Password Storage

⚠️ **CRITICAL SECURITY ISSUE**: SMTP passwords in `emails` table are stored in **plain text**.

**Current Implementation**:
```php
$obj->password = $requestData['password'];  // Plain text storage
```

**Risk Level**: 🔴 HIGH

**Impact**:
- Database compromise exposes all SMTP credentials
- Potential email account hijacking
- Compliance issues (GDPR, SOC 2)

**Recommended Fix**:
```php
// Encrypt before storing
$obj->password = encrypt($requestData['password']);

// Decrypt when retrieving
$password = decrypt($emailConfig->password);
```

---

### 14.2 Email Injection Prevention

**Current Status**: ✅ Generally safe

Laravel's Mail facade automatically escapes headers and prevents injection, but input validation is important:

```php
// Validate email addresses
$request->validate([
    'email_to' => 'required|email',
    'email_from' => 'required|email|exists:emails,email',
    'subject' => 'required|string|max:255',
]);
```

---

### 14.3 Attachment Security

**Concerns**:
- Uploaded files processed without virus scanning
- Files stored in public directories
- S3 URLs may be accessible without authentication

**Recommendations**:
- Implement virus scanning
- Use private storage with signed URLs
- Limit file types and sizes
- Sanitize file names

---

## 15. Best Practices & Recommendations

### 15.1 Immediate Actions (High Priority)

1. **Encrypt SMTP Passwords** 🔴
   - Use Laravel's `encrypt()` function
   - Update EmailService to decrypt
   - Migrate existing passwords

2. **Implement Email Queuing** 🟡
   - Use `Mail::queue()` instead of `Mail::send()`
   - Improves performance
   - Better error handling
   - Retry failed emails

3. **Standardize SMTP Configuration** 🟡
   - Consolidate on EmailConfigService
   - Remove hardcoded 'smtp.zoho.com'
   - Make all email sending use service layer

---

### 15.2 Feature Improvements

1. **Email Templates**
   - Visual template editor (WYSIWYG)
   - Template versioning
   - Preview before sending
   - More variables

2. **Email Tracking**
   - Open tracking
   - Click tracking
   - Bounce handling
   - Delivery status

3. **Better Attachment Handling**
   - File size limits per email
   - Total attachment size warnings
   - Virus scanning
   - Preview attachments before sending

4. **SMTP Testing**
   - Test connection button in admin panel
   - Verify credentials before saving
   - Send test email feature

---

### 15.3 Architectural Improvements

1. **Consolidate Email Sending**
   ```php
   // Single unified service for all email sending
   app(EmailService::class)->send([
       'from' => 'info@example.com',
       'to' => 'client@example.com',
       'subject' => 'Welcome',
       'template' => 'emails.welcome',
       'data' => ['name' => 'John'],
       'attachments' => [],
       'cc' => [],
   ]);
   ```

2. **Event-Driven Emails**
   ```php
   // Dispatch events, listeners handle emails
   event(new SignerAdded($signer));
   event(new InvoiceGenerated($invoice));
   event(new AppointmentBooked($appointment));
   ```

3. **Email Preferences**
   - Allow clients to manage email preferences
   - Unsubscribe from certain types
   - Frequency control

---

## 16. Configuration Checklist

### For New Installation:

- [ ] Set `MAIL_MAILER=smtp` in `.env`
- [ ] Configure default SMTP (Zoho or custom)
- [ ] Add SMTP accounts in admin console
- [ ] Test email sending
- [ ] Set up email templates
- [ ] Configure queue worker for email queue
- [ ] Set up email logging
- [ ] Test all email workflows:
  - [ ] Client compose email
  - [ ] Signature link sending
  - [ ] Invoice emails
  - [ ] Appointment confirmations
  - [ ] Portal activation emails

---

## 17. Troubleshooting Guide

### Email Not Sending

1. **Check SMTP Credentials**
   - Verify email account exists in database
   - Check password is correct
   - Test SMTP connection manually

2. **Check Laravel Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Check Mail Configuration**
   ```php
   dd(config('mail'));
   ```

4. **Test with Log Driver**
   ```env
   MAIL_MAILER=log
   ```
   Check `storage/logs/laravel.log` for email output

---

### SMTP Authentication Failed

**Common Causes**:
- Wrong password
- 2FA enabled on email account (use app password)
- SMTP not enabled on email provider
- Firewall blocking port 587

**Solutions**:
- Use app-specific passwords
- Enable SMTP in email provider settings
- Check firewall rules
- Try port 465 with SSL

---

### Attachments Not Working

**Check**:
- File exists at specified path
- File permissions
- File size (PHP upload limit)
- MIME type correct

---

## 18. API Documentation

### SendEmail via EmailService

```php
app(EmailService::class)->sendEmail(
    string $view,           // Blade view template
    array $data,            // Data for view
    string $to,             // Recipient email
    string $subject,        // Email subject
    string $fromEmailId,    // From email (SMTP account email)
    array $attachments = [], // File paths
    array $cc = []          // CC email addresses
): bool
```

**Example**:
```php
app(EmailService::class)->sendEmail(
    'emails.welcome',
    ['name' => 'John Doe'],
    'john@example.com',
    'Welcome to Our Service',
    'info@company.com',
    ['/path/to/file.pdf'],
    ['manager@company.com']
);
```

---

### SendEmail via EmailConfigService

```php
// Get config
$config = app(EmailConfigService::class)->forAccount('info@company.com');

// Apply config
app(EmailConfigService::class)->applyConfig($config);

// Send email
Mail::send($view, $data, function($message) use ($to, $subject) {
    $message->to($to)->subject($subject);
});
```

---

## 19. Files & Directories Reference

### Configuration
- `config/mail.php` - Laravel mail configuration
- `.env` - Environment variables

### Models
- `app/Models/Email.php` - SMTP accounts
- `app/Models/EmailTemplate.php` - Email templates
- `app/Models/MailReport.php` - Email logs

### Services
- `app/Services/EmailService.php` - Main email service
- `app/Services/EmailConfigService.php` - SMTP config management
- `app/Services/CustomMailService.php` - Custom SMTP service
- `app/Services/SignatureService.php` - Signature emails

### Controllers
- `app/Http/Controllers/Controller.php` - Base email methods
- `app/Http/Controllers/Admin/AdminController.php` - Client email sending
- `app/Http/Controllers/Admin/SignatureDashboardController.php` - Signature emails
- `app/Http/Controllers/AdminConsole/EmailController.php` - SMTP account management
- `app/Http/Controllers/Admin/ClientPortalController.php` - Portal emails
- `app/Http/Controllers/Admin/AppointmentsController.php` - Appointment emails

### Mailables
- `app/Mail/CommonMail.php` - General purpose
- `app/Mail/InvoiceEmailManager.php` - Invoice emails
- `app/Mail/AppointmentMail.php` - Appointments
- `app/Mail/AppointmentStripeMail.php` - Payment confirmations
- `app/Mail/HubdocInvoiceMail.php` - Hubdoc integration
- `app/Mail/VisaExpireReminderMail.php` - Reminders

### Views
- `resources/views/emails/` - Email templates
- `resources/views/Admin/clients/detail.blade.php` - Client email modal

### Routes
- `routes/clients.php` - Client email routes
- `routes/applications.php` - Application email routes
- `routes/documents.php` - Signature email routes

---

## 20. Summary

### Email Sending Methods in CRM:

| Section | Controller Method | SMTP Config Method | Features |
|---|---|---|---|
| **Client Details** | `AdminController@sendmail` | EmailService (user-selected) | Attachments, CC, Templates, Logging |
| **Signature Links** | `SignatureDashboardController@addSigner` | EmailConfigService (optional select) | Per-signer config, Custom templates |
| **Signature Reminders** | `SignatureService@remind` | EmailConfigService (fallback) | Reminder limits, Tracking |
| **Applications** | `ApplicationsController@applicationsendmail` | send_compose_template (hardcoded) | Activity logging |
| **Appointments** | `AppointmentsController` | Default config | Payment confirmations |
| **Invoices** | `AdminController@sendmail` | EmailService | PDF generation, Attachments |
| **Client Portal** | `ClientPortalController@sendClientPortalActivationEmail` | Default config | Password generation |
| **Registration** | `RegisterController` | send_email_template | Email verification |

---

### Key Strengths:
✅ Flexible multi-account SMTP system
✅ Database-driven configuration
✅ Dynamic SMTP selection per email
✅ Comprehensive email logging
✅ Template system with variables
✅ Attachment support
✅ CC/BCC support

### Key Weaknesses:
⚠️ **SMTP passwords stored in plain text** (critical)
⚠️ Inconsistent SMTP configuration methods
⚠️ Some emails use hardcoded sender addresses
⚠️ No email queuing in most places
⚠️ Limited error handling

---

### Recommended Architecture:

```
All Email Sending → EmailService (unified)
    ↓
EmailConfigService (SMTP config)
    ↓
Laravel Mail Queue (async)
    ↓
SMTP Server (Zoho/Custom)
    ↓
Logging (mail_reports + activity_logs)
```

This provides consistency, better error handling, and improved performance.

---

**End of Document**
