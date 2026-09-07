<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Frozen workflow stages (Admin Console)
    |--------------------------------------------------------------------------
    |
    | Stages whose names match these rules cannot be renamed or deleted.
    | Exact matches are compared case-insensitively after trimming.
    | "Contains" rules match if the stage name contains the substring (any case).
    |
    */
    'frozen_stage_names' => [
        'Checklist',
        'Decision Received',
        'Ready to Close',
        'File Closed',
    ],

    /*
     * Stage names that start with this text (any case) are frozen.
     * Matches e.g. "Verification: Payment, Service Agreement, Forms"
     * without locking unrelated names like "Pre-verification review".
     */
    'frozen_stage_name_starts_with' => [
        'verification',
    ],

    /*
    |--------------------------------------------------------------------------
    | Scope protected stages to the General workflow only
    |--------------------------------------------------------------------------
    |
    | When true, stages matching the frozen rules above are only locked on the
    | General workflow. Custom workflows may rename or delete those stages.
    |
    */
    'freeze_protected_stages_only_on_general_workflow' => true,

    /*
    |--------------------------------------------------------------------------
    | Default workflow for new client matters (by matter type title)
    |--------------------------------------------------------------------------
    |
    | Applied only when creating a new client_matters row. Existing matters are
    | not changed. matters.workflow_id (Admin → Matter List) still overrides this.
    | Unmapped matter types use default_workflow_name (General).
    |
    */
    'matter_default_workflows' => [
        'Administrative Review Tribunal' => 'Administrative Review Tribunal',
        'Bridging Visa B- (020)' => 'Bridging visa B (BV-B) and Work rights',
        'Expression Of Interest' => 'EOI /ROI',
        'Skill assessment - Australian Physiotherapy Council' => 'Skill assessment',
    ],

    'default_workflow_name' => 'General',

    /*
    |--------------------------------------------------------------------------
    | Workflow tab — stage display defaults (UI)
    |--------------------------------------------------------------------------
    |
    | Optional metadata for the redesigned Workflow tab. Keys are matched
    | case-insensitively against workflow_stages.name. When cp_doc_checklists
    | exist for a matter+stage, those take precedence over checklist_items.
    |
    */
    'stage_display_defaults' => [
        'checklist & agreement sent' => [
            'completion_rule' => 'Checklist, service agreement and forms sent, and a follow-up date recorded.',
            'pending_from' => 'Client',
            'file_note_section' => true,
            'checklist_items' => [
                ['label' => 'Initial assessment recorded', 'required' => true],
                ['label' => 'Specific checklist sent', 'required' => true],
                ['label' => 'Cost / service agreement sent', 'required' => true],
                ['label' => 'Form 956 / 956A sent (if applicable)', 'required' => true],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Client Portal Activities — CRM → client mapping (UI)
    |--------------------------------------------------------------------------
    |
    | Shown only on Client Portal → Activities when a stage is selected.
    | Keys are matched case-insensitively against workflow_stages.name.
    | This is separate from stage_display_defaults (Workflow tab).
    | "Client tasks created" are loaded from workflow_stage_portal_tasklists.
    |
    */
    'portal_stage_mapping' => [
        'checklist & agreement sent' => [
            'client_label' => 'Getting started',
            'tag' => 'bansal',
            'pct' => 5,
            'silent' => false,
            'notif_title' => 'Your file is open',
            'notif_body' => 'Welcome! Your file has been opened. We\'re preparing your agreement and checklist.',
            'app_note' => 'Timeline starts. No tasks yet — agreement is being prepared.',
            'rule' => 'Forward trigger: agent advances CRM to stage 2 once all items sent.',
        ],
        'awaiting client action' => [
            'client_label' => 'Sign your agreement',
            'tag' => 'action',
            'pct' => 12,
            'silent' => false,
            'notif_title' => 'Action needed',
            'notif_body' => 'Your service agreement is ready. Please review, sign and complete payment in the app.',
            'app_note' => 'Payment task unlocks only after the agreement is signed — mirrors the CRM s51 gate.',
            'rule' => 'Signing in app ticks \'Service agreement signed\' in CRM automatically. Payment stays locked until signature exists (s51).',
        ],
        'verification & file setup' => [
            'client_label' => 'Setting up your file',
            'tag' => 'bansal',
            'pct' => 20,
            'silent' => false,
            'notif_title' => 'Payment received',
            'notif_body' => 'Thanks — your payment is confirmed and your receipt is in the app. We\'re setting up your file.',
            'app_note' => 'Receipt/invoice appears in the client\'s Documents tab (s49).',
            'rule' => 'Accounts confirms payment in CRM; receipt is pushed to the app document store.',
        ],
        'documents review & completion' => [
            'client_label' => 'Documents needed',
            'tag' => 'action',
            'pct' => 30,
            'silent' => false,
            'notif_title' => 'Documents needed',
            'notif_body' => 'We need a few documents to prepare your application. See your task list.',
            'app_note' => 'Each upload lands against the named request in CRM — no anonymous bulk uploads.',
            'rule' => 'When the client completes all upload tasks, the assigned agent is notified in CRM. Stage advancement stays manual.',
        ],
        'draft preparation' => [
            'client_label' => 'Preparing your application',
            'tag' => 'bansal',
            'pct' => 42,
            'silent' => false,
            'notif_title' => 'Application in preparation',
            'notif_body' => 'All documents received. We\'re now preparing your application.',
            'app_note' => 'Quiet stage — client just sees steady progress.',
            'rule' => 'Forward trigger only. No client tasks.',
        ],
        'internal review' => [
            'client_label' => 'Preparing your application',
            'tag' => 'bansal',
            'pct' => 48,
            'silent' => true,
            'notif_title' => null,
            'notif_body' => null,
            'app_note' => 'Client view is identical to Draft Preparation — internal QA is invisible.',
            'rule' => 'SILENT: progress ticks from 42% to 48% but no stage change or push. Senior RMA review is internal only.',
        ],
        'client draft approval' => [
            'client_label' => 'Review your draft',
            'tag' => 'action',
            'pct' => 55,
            'silent' => false,
            'notif_title' => 'Draft ready for your review',
            'notif_body' => 'Your draft application is ready. Please review and approve in the app.',
            'app_note' => 'Approve action captures written approval with timestamp — satisfies the CRM \'final approval received\' item.',
            'rule' => 'In-app approval ticks \'Final approval received from client\' (s39). Change requests route to the agent as a message.',
        ],
        'lodgement completed' => [
            'client_label' => 'Lodged with Immigration',
            'tag' => 'dept',
            'pct' => 70,
            'silent' => false,
            'notif_title' => 'Application lodged 🎉',
            'notif_body' => 'Great news — your application has been lodged with the Department.',
            'app_note' => 'Milestone styling. Lodgement email is also sent per CRM checklist (s39).',
            'rule' => 'Forward trigger. Milestone notification.',
        ],
        'awaiting outcome' => [
            'client_label' => 'Lodged with Immigration',
            'tag' => 'dept',
            'pct' => 75,
            'silent' => true,
            'notif_title' => null,
            'notif_body' => null,
            'app_note' => 'Same client stage as Lodgement — waiting is not a new stage for the client.',
            'rule' => 'SILENT: no push. Hero sub-text reads \'Your file is with the Department of Home Affairs.\'',
        ],
        'additional request, if any' => [
            'client_label' => 'More info requested',
            'tag' => 'action',
            'pct' => 78,
            'silent' => false,
            'notif_title' => 'Immigration needs more information',
            'notif_body' => 'The Department has requested further information. Please check your tasks — a deadline applies.',
            'app_note' => 'Red banner + hard deadline countdown. Reminder pushes at T-7 and T-2 days.',
            'rule' => 'DETOUR: overlays \'More info requested\' on the Lodged step. When CRM returns to Awaiting Outcome, the app silently reverts — no notification.',
        ],
        'decision received' => [
            'client_label' => 'Decision received',
            'tag' => 'done',
            'pct' => 90,
            'silent' => false,
            'notif_title' => 'Decision received',
            'notif_body' => 'A decision has been made on your application. Open the app for details.',
            'app_note' => 'Push never reveals the outcome. Grant/refusal is shown in-app after login; refusals get a \'Next steps\' card (review rights are time-limited).',
            'rule' => 'PRIVACY RULE: neutral push text. Outcome details in-app only or by phone.',
        ],
        'outcome / ready to close' => [
            'client_label' => 'Decision received',
            'tag' => 'done',
            'pct' => 95,
            'silent' => true,
            'notif_title' => null,
            'notif_body' => null,
            'app_note' => 'Client view unchanged while the team finalises the file.',
            'rule' => 'SILENT: internal closing checks (documents returned per instructions, records retained).',
        ],
        'file closed' => [
            'client_label' => 'File completed',
            'tag' => 'done',
            'pct' => 100,
            'silent' => false,
            'notif_title' => 'File complete',
            'notif_body' => 'Your file is now complete. Thank you for choosing Bansal Immigration.',
            'app_note' => 'Timeline fully green. Client keeps read-only access to their documents.',
            'rule' => 'Forward trigger. Final notification; portal switches to read-only archive mode.',
        ],
    ],

];
