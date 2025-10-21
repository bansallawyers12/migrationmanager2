# Phase 6 — Analytics & Housekeeping Assessment

**Date:** October 21, 2025  
**Status:** 🟡 **PARTIALLY COMPLETE** (40%)

---

## 📊 PHASE 6 OVERVIEW

**Goals:**
- Dashboard metrics & analytics
- Auto-cleanup of stale drafts
- Reminder automation
- Bulk actions & export capabilities

---

## ✅ COMPLETED FEATURES (40%)

### 1. ✅ Basic Dashboard Metrics
**Location:** `app/Http/Controllers/Admin/SignatureDashboardController.php:70-81`

**What's Working:**
```php
$counts = [
    'sent_by_me' => Document::forUser($user->id)->notArchived()->count(),
    'visible_to_me' => Document::visible($user)->notArchived()->count(),
    'pending' => Document::visible($user)->byStatus('sent')->notArchived()->count(),
    'signed' => Document::visible($user)->byStatus('signed')->notArchived()->count(),
    'overdue' => Document::visible($user)
        ->whereNotNull('due_at')
        ->where('due_at', '<', now())
        ->where('status', '!=', 'signed')
        ->notArchived()
        ->count(),
];
```

**Dashboard UI:** `resources/views/Admin/signatures/dashboard.blade.php:356-382`
- ✅ Shows counts for: My Documents, Pending, Signed, Overdue
- ✅ Gradient stat cards with modern design
- ✅ Responsive grid layout

---

### 2. ✅ Manual Reminder Functionality
**Service Method:** `app/Services/SignatureService.php:149-207`

**Features:**
- ✅ Rate limiting: Max 3 reminders per signer
- ✅ Cooldown period: 24 hours between reminders
- ✅ Reminder counter tracking
- ✅ Branded email template: `emails.signature.reminder`
- ✅ Dashboard button to send reminder

**Controller:** `app/Http/Controllers/Admin/SignatureDashboardController.php:213-231`
```php
public function sendReminder(Request $request, $id)
{
    $document = Document::findOrFail($id);
    $this->authorize('sendReminder', $document);
    
    $signer = $document->signers()->findOrFail($signerId);
    $success = $this->signatureService->remind($signer);
    
    return back()->with('success', 'Reminder sent successfully!');
}
```

---

### 3. ✅ Archive Infrastructure
**Database Field:** `documents.archived_at` (nullable datetime)

**Model Scopes:**
```php
public function scopeNotArchived($query)
{
    return $query->whereNull('archived_at');
}
```

**Service Method:** `app/Services/SignatureService.php:416-428`
```php
public function archiveOldDrafts(int $daysOld = 30): int
{
    $count = Document::where('status', 'draft')
        ->where('created_at', '<', now()->subDays($daysOld))
        ->whereNull('archived_at')
        ->update(['archived_at' => now()]);
    
    Log::info("Archived {$count} old draft documents");
    return $count;
}
```

**Note:** ✅ Method exists but ❌ NOT scheduled to run automatically

---

### 4. ✅ Visibility & Permission System (Phase 4)
- ✅ Role-based access control
- ✅ Document visibility scopes
- ✅ Policy authorization
- ✅ Team vs Organization filtering

---

## ❌ MISSING FEATURES (60%)

### 1. ❌ Scheduled Auto-Cleanup Job
**Status:** NOT IMPLEMENTED

**Requirements:**
```php
// Required: app/Console/Commands/ArchiveOldDrafts.php
class ArchiveOldDrafts extends Command
{
    protected $signature = 'signatures:archive-drafts {--days=30}';
    protected $description = 'Archive draft documents older than specified days';
    
    public function handle(SignatureService $service)
    {
        $days = $this->option('days');
        $count = $service->archiveOldDrafts($days);
        
        $this->info("Archived {$count} old draft(s)");
    }
}
```

**Scheduler Registration:**
```php
// app/Console/Kernel.php
$schedule->command('signatures:archive-drafts --days=30')
    ->daily()
    ->at('02:00')
    ->appendOutputTo(storage_path('logs/signature-archive.log'));
```

---

### 2. ❌ Automatic Reminder Job
**Status:** NOT IMPLEMENTED

**Requirements:**
- Auto-send reminders for documents pending >7 days
- Max 3 reminders per signer
- Configurable reminder intervals
- Should run daily

**Needed Command:**
```php
// app/Console/Commands/SendSignatureReminders.php
class SendSignatureReminders extends Command
{
    protected $signature = 'signatures:send-auto-reminders';
    protected $description = 'Automatically send reminders for pending signatures >7 days';
    
    public function handle(SignatureService $service)
    {
        // Find pending documents older than 7 days
        $documents = Document::where('status', 'sent')
            ->where('created_at', '<', now()->subDays(7))
            ->notArchived()
            ->with('signers')
            ->get();
        
        $sent = 0;
        foreach ($documents as $doc) {
            foreach ($doc->signers as $signer) {
                if ($signer->status === 'pending' && $signer->reminder_count < 3) {
                    // Check 24h cooldown
                    if (!$signer->last_reminder_sent_at || 
                        $signer->last_reminder_sent_at->diffInHours(now()) >= 24) {
                        if ($service->remind($signer)) {
                            $sent++;
                        }
                    }
                }
            }
        }
        
        $this->info("Sent {$sent} automatic reminder(s)");
    }
}
```

**Scheduler:**
```php
$schedule->command('signatures:send-auto-reminders')
    ->daily()
    ->at('10:00')
    ->timezone('Australia/Melbourne')
    ->appendOutputTo(storage_path('logs/signature-reminders.log'));
```

---

### 3. ❌ Advanced Analytics Queries
**Status:** NOT IMPLEMENTED

**Missing Analytics:**

#### a) Median Time-to-Sign
```php
// Needed in SignatureService or new SignatureAnalyticsService

public function getMedianTimeToSign($documentType = null, $ownerId = null)
{
    $query = Document::where('status', 'signed')
        ->whereNotNull('last_activity_at');
    
    if ($documentType) {
        $query->where('document_type', $documentType);
    }
    
    if ($ownerId) {
        $query->where('created_by', $ownerId);
    }
    
    $documents = $query->get()->map(function($doc) {
        return $doc->created_at->diffInHours($doc->last_activity_at);
    })->sort()->values();
    
    $count = $documents->count();
    if ($count === 0) return 0;
    
    $middle = floor($count / 2);
    
    if ($count % 2 == 0) {
        return ($documents[$middle - 1] + $documents[$middle]) / 2;
    }
    
    return $documents[$middle];
}
```

#### b) Top Signers (Repeat Recipients)
```php
public function getTopSigners($limit = 10)
{
    return Signer::select('email', 'name')
        ->selectRaw('COUNT(*) as total_signed')
        ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, signed_at)) as avg_time_hours')
        ->where('status', 'signed')
        ->groupBy('email', 'name')
        ->orderByDesc('total_signed')
        ->limit($limit)
        ->get();
}
```

#### c) Document Type Analytics
```php
public function getDocumentTypeStats()
{
    return Document::select('document_type')
        ->selectRaw('COUNT(*) as total')
        ->selectRaw('SUM(CASE WHEN status = "signed" THEN 1 ELSE 0 END) as signed')
        ->selectRaw('SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as pending')
        ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, last_activity_at)) as avg_time_hours')
        ->groupBy('document_type')
        ->get();
}
```

#### d) Overdue Document Tracking
```php
public function getOverdueAnalytics()
{
    return Document::where('status', 'sent')
        ->whereNotNull('due_at')
        ->where('due_at', '<', now())
        ->notArchived()
        ->with(['creator', 'signers'])
        ->get()
        ->map(function($doc) {
            return [
                'id' => $doc->id,
                'title' => $doc->display_title,
                'owner' => $doc->creator->first_name . ' ' . $doc->creator->last_name,
                'signer' => $doc->primary_signer_email,
                'days_overdue' => now()->diffInDays($doc->due_at),
                'reminder_count' => $doc->signers->first()->reminder_count ?? 0,
            ];
        });
}
```

---

### 4. ❌ Analytics Dashboard Tab
**Status:** NOT IMPLEMENTED

**Requirements:**
- Separate analytics tab in dashboard
- Visual charts (Chart.js or ApexCharts)
- Date range filter
- Metrics cards with trends

**Needed UI:** `resources/views/Admin/signatures/analytics.blade.php`
```blade
@extends('layouts.admin_client_detail')
@section('title', 'Signature Analytics')

@section('content')
<div class="analytics-dashboard">
    <!-- Date Range Filter -->
    <div class="filter-bar">
        <input type="date" id="start_date">
        <input type="date" id="end_date">
        <button onclick="loadAnalytics()">Update</button>
    </div>
    
    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <h3>Median Time to Sign</h3>
            <div class="value">{{ $medianHours }} hours</div>
        </div>
        <div class="kpi-card">
            <h3>Completion Rate</h3>
            <div class="value">{{ $completionRate }}%</div>
        </div>
        <div class="kpi-card">
            <h3>Avg Reminders Sent</h3>
            <div class="value">{{ $avgReminders }}</div>
        </div>
        <div class="kpi-card">
            <h3>Currently Overdue</h3>
            <div class="value">{{ $overdueCount }}</div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="charts-row">
        <div class="chart-container">
            <canvas id="signaturesTrendChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="documentTypeChart"></canvas>
        </div>
    </div>
    
    <!-- Top Signers Table -->
    <div class="top-signers">
        <h3>🏆 Top Signers (Most Active)</h3>
        <table>
            <thead>
                <tr>
                    <th>Signer</th>
                    <th>Email</th>
                    <th>Total Signed</th>
                    <th>Avg Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSigners as $signer)
                <tr>
                    <td>{{ $signer->name }}</td>
                    <td>{{ $signer->email }}</td>
                    <td>{{ $signer->total_signed }}</td>
                    <td>{{ round($signer->avg_time_hours, 1) }}h</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```

**Controller Method Needed:**
```php
// SignatureDashboardController.php
public function analytics(Request $request)
{
    $analyticsService = app(SignatureAnalyticsService::class);
    
    $startDate = $request->get('start_date', now()->subDays(30));
    $endDate = $request->get('end_date', now());
    
    return view('Admin.signatures.analytics', [
        'medianHours' => $analyticsService->getMedianTimeToSign(),
        'completionRate' => $analyticsService->getCompletionRate($startDate, $endDate),
        'avgReminders' => $analyticsService->getAverageReminders($startDate, $endDate),
        'overdueCount' => $analyticsService->getOverdueCount(),
        'topSigners' => $analyticsService->getTopSigners(10),
        'trendData' => $analyticsService->getSignatureTrend($startDate, $endDate),
        'documentTypeData' => $analyticsService->getDocumentTypeStats(),
    ]);
}
```

---

### 5. ❌ Configurable Reminder Settings
**Status:** NOT IMPLEMENTED

**Requirements:**
- Per-document reminder configuration
- Set custom reminder intervals
- Enable/disable auto-reminders
- Custom reminder message templates

**Needed Migration:**
```php
Schema::table('documents', function (Blueprint $table) {
    $table->boolean('auto_reminder_enabled')->default(true);
    $table->integer('reminder_interval_days')->default(7);
    $table->integer('max_reminders')->default(3);
    $table->text('reminder_custom_message')->nullable();
});
```

**UI in Create/Edit Form:**
```blade
<!-- In resources/views/Admin/signatures/create.blade.php -->
<div class="form-group">
    <label>
        <input type="checkbox" name="auto_reminder_enabled" checked>
        Enable automatic reminders
    </label>
</div>

<div class="form-group">
    <label>Send reminder after (days)</label>
    <input type="number" name="reminder_interval_days" value="7" min="1" max="30">
</div>

<div class="form-group">
    <label>Maximum reminders</label>
    <input type="number" name="max_reminders" value="3" min="1" max="5">
</div>
```

---

### 6. ❌ Bulk Actions
**Status:** NOT IMPLEMENTED

**Missing Features:**
- Bulk archive
- Bulk void
- Bulk resend
- Checkbox selection in table
- "Select All" functionality

**Needed UI Changes to Dashboard:**
```blade
<!-- Add to dashboard.blade.php table -->
<thead>
    <tr>
        <th><input type="checkbox" id="select-all"></th>
        <th>Document</th>
        <!-- ... rest of columns ... -->
    </tr>
</thead>
<tbody>
    @foreach($documents as $doc)
    <tr>
        <td><input type="checkbox" name="selected[]" value="{{ $doc->id }}"></td>
        <td>{{ $doc->display_title }}</td>
        <!-- ... rest of row ... -->
    </tr>
    @endforeach
</tbody>

<!-- Bulk Actions Bar -->
<div id="bulk-actions-bar" style="display: none;">
    <button onclick="bulkArchive()">Archive Selected</button>
    <button onclick="bulkVoid()">Void Selected</button>
    <button onclick="bulkResend()">Resend Selected</button>
</div>
```

**Controller Methods Needed:**
```php
public function bulkArchive(Request $request)
{
    $request->validate(['ids' => 'required|array']);
    
    $count = Document::whereIn('id', $request->ids)
        ->whereNull('archived_at')
        ->update(['archived_at' => now()]);
    
    return back()->with('success', "Archived {$count} document(s)");
}

public function bulkVoid(Request $request)
{
    $request->validate(['ids' => 'required|array', 'reason' => 'nullable|string']);
    
    $documents = Document::whereIn('id', $request->ids)->get();
    $count = 0;
    
    foreach ($documents as $doc) {
        if ($this->signatureService->void($doc, $request->reason)) {
            $count++;
        }
    }
    
    return back()->with('success', "Voided {$count} document(s)");
}

public function bulkResend(Request $request)
{
    $request->validate(['ids' => 'required|array']);
    
    $documents = Document::with('signers')->whereIn('id', $request->ids)->get();
    $count = 0;
    
    foreach ($documents as $doc) {
        foreach ($doc->signers as $signer) {
            if ($this->signatureService->remind($signer)) {
                $count++;
            }
        }
    }
    
    return back()->with('success', "Sent {$count} reminder(s)");
}
```

---

### 7. ❌ Export Functionality
**Status:** NOT IMPLEMENTED

**Requirements:**
- Export signature audit report (CSV)
- Export signature audit report (PDF)
- Date range filter
- Include all activities, signers, status changes

**Needed Controller Method:**
```php
use Illuminate\Support\Facades\Response;
use League\Csv\Writer;

public function exportAudit(Request $request)
{
    $request->validate([
        'format' => 'required|in:csv,pdf',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
    ]);
    
    $query = Document::with(['creator', 'signers', 'documentable', 'notes'])
        ->notArchived();
    
    if ($request->filled('start_date')) {
        $query->where('created_at', '>=', $request->start_date);
    }
    
    if ($request->filled('end_date')) {
        $query->where('created_at', '<=', $request->end_date);
    }
    
    $documents = $query->get();
    
    if ($request->format === 'csv') {
        return $this->exportCSV($documents);
    } else {
        return $this->exportPDF($documents);
    }
}

protected function exportCSV($documents)
{
    $csv = Writer::createFromString('');
    
    $csv->insertOne([
        'Document ID',
        'Title',
        'Status',
        'Created By',
        'Signer Email',
        'Signer Name',
        'Sent At',
        'Signed At',
        'Reminders Sent',
        'Document Type',
        'Associated With'
    ]);
    
    foreach ($documents as $doc) {
        foreach ($doc->signers as $signer) {
            $csv->insertOne([
                $doc->id,
                $doc->display_title,
                $doc->status,
                $doc->creator->first_name . ' ' . $doc->creator->last_name,
                $signer->email,
                $signer->name,
                $doc->created_at->format('Y-m-d H:i:s'),
                $signer->signed_at?->format('Y-m-d H:i:s') ?? 'N/A',
                $signer->reminder_count,
                $doc->document_type,
                $doc->documentable ? get_class($doc->documentable) : 'Ad-hoc'
            ]);
        }
    }
    
    return Response::make($csv->toString(), 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="signature_audit_' . date('Y-m-d') . '.csv"',
    ]);
}

protected function exportPDF($documents)
{
    $pdf = \PDF::loadView('Admin.signatures.audit_report', compact('documents'));
    return $pdf->download('signature_audit_' . date('Y-m-d') . '.pdf');
}
```

**Route:**
```php
Route::get('/signatures/export', [SignatureDashboardController::class, 'exportAudit'])
    ->name('admin.signatures.export');
```

**UI Button:**
```blade
<a href="{{ route('admin.signatures.export', ['format' => 'csv']) }}" 
   class="btn btn-success">
    <i class="fas fa-download"></i> Export CSV
</a>
<a href="{{ route('admin.signatures.export', ['format' => 'pdf']) }}" 
   class="btn btn-danger">
    <i class="fas fa-file-pdf"></i> Export PDF
</a>
```

---

## 📋 PHASE 6 COMPLETION CHECKLIST

| Feature | Status | Priority |
|---------|--------|----------|
| ✅ Basic dashboard metrics (pending, signed, overdue) | DONE | High |
| ✅ Manual reminder sending | DONE | High |
| ✅ Archive infrastructure (DB field + service method) | DONE | Medium |
| ❌ Scheduled auto-cleanup job | TODO | High |
| ❌ Scheduled auto-reminder job | TODO | High |
| ❌ Median time-to-sign analytics | TODO | Medium |
| ❌ Top signers analytics | TODO | Low |
| ❌ Document type analytics | TODO | Medium |
| ❌ Analytics dashboard tab with charts | TODO | Medium |
| ❌ Configurable reminder settings per document | TODO | Medium |
| ❌ Bulk archive action | TODO | Medium |
| ❌ Bulk void action | TODO | Low |
| ❌ Bulk resend action | TODO | Medium |
| ❌ CSV export | TODO | Medium |
| ❌ PDF export | TODO | Low |

---

## 🎯 ACCEPTANCE CRITERIA STATUS

| Criteria | Status | Notes |
|----------|--------|-------|
| ✅ Dashboard shows summary metrics | **PASS** | Pending, signed, overdue counts working |
| ❌ Stale drafts auto-archived | **FAIL** | Method exists but not scheduled |
| ❌ Auto-reminders sent | **FAIL** | Only manual reminders implemented |

---

## 🚀 IMPLEMENTATION PRIORITY

### **P0 - Critical (Complete Phase 6 Core)**
1. ❌ Create `ArchiveOldDrafts` command
2. ❌ Create `SendSignatureReminders` command
3. ❌ Register both commands in Kernel scheduler

### **P1 - High (Analytics Foundation)**
4. ❌ Create `SignatureAnalyticsService.php`
5. ❌ Implement median time-to-sign method
6. ❌ Implement top signers query
7. ❌ Add analytics controller method

### **P2 - Medium (UI & UX)**
8. ❌ Create analytics dashboard view
9. ❌ Implement CSV export
10. ❌ Add bulk archive action
11. ❌ Add bulk resend action

### **P3 - Nice to Have**
12. ❌ PDF export with styling
13. ❌ Configurable reminder settings UI
14. ❌ Charts & visualizations
15. ❌ Bulk void action

---

## 📁 FILES TO CREATE

1. **Commands:**
   - `app/Console/Commands/ArchiveOldDrafts.php`
   - `app/Console/Commands/SendSignatureReminders.php`

2. **Services:**
   - `app/Services/SignatureAnalyticsService.php`

3. **Views:**
   - `resources/views/Admin/signatures/analytics.blade.php`
   - `resources/views/Admin/signatures/audit_report.blade.php` (PDF template)

4. **Migrations:**
   - `database/migrations/[timestamp]_add_reminder_config_to_documents.php`

5. **Tests:**
   - `tests/Feature/SignatureAnalyticsTest.php`
   - `tests/Feature/BulkActionsTest.php`
   - `tests/Feature/ExportAuditTest.php`

---

## 📝 NOTES & RECOMMENDATIONS

### Current Strengths
1. ✅ Strong foundation with Phase 4 visibility system
2. ✅ Clean service architecture
3. ✅ Good reminder rate limiting
4. ✅ Archive field already in database

### Technical Debt
1. ⚠️ No analytics service - queries scattered
2. ⚠️ Missing scheduled task infrastructure
3. ⚠️ No bulk action support

### Quick Wins
1. 🚀 Add scheduled commands (1-2 hours)
2. 🚀 Implement CSV export (2-3 hours)
3. 🚀 Add bulk archive (1-2 hours)

### Future Enhancements
- Email digest: Weekly summary of pending signatures
- Slack/Teams integration for overdue alerts
- Signature analytics API for external reporting
- Custom reminder templates per client
- Document template library

---

## 🏁 CONCLUSION

**Phase 6 Progress: 40% Complete**

**What Works:**
- Basic dashboard metrics
- Manual reminders with rate limiting
- Archive infrastructure ready

**What's Missing:**
- Automation (scheduled jobs) ← **CRITICAL**
- Advanced analytics
- Bulk actions
- Export functionality

**Estimated Time to Complete:**
- **P0 (Scheduled Jobs):** 3-4 hours
- **P1 (Analytics):** 6-8 hours
- **P2 (UI/UX):** 8-10 hours
- **Total:** ~20-25 hours

**Recommendation:** Focus on P0 items first to meet core acceptance criteria, then progressively add analytics and bulk operations.

---

**Last Updated:** October 21, 2025  
**Next Review:** After P0 implementation

