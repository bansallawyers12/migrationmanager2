<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Signer;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\DocumentNote;
use App\Models\ActivitiesLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SignatureService
{
    protected EmailConfigService $emailConfigService;

    /**
     * Constructor with dependency injection
     */
    public function __construct(EmailConfigService $emailConfigService)
    {
        $this->emailConfigService = $emailConfigService;
    }
    /**
     * Send a document for signature
     *
     * @param Document $document
     * @param array $signers Array of ['email' => '', 'name' => '']
     * @param array $options Additional options (subject, message, from_email, template, attachments)
     * @return bool
     */
    public function send(Document $document, array $signers, array $options = []): bool
    {
        try {
            $createdSigners = [];

            foreach ($signers as $signerData) {
                $signer = $document->signers()->create([
                    'email' => $signerData['email'],
                    'name' => $signerData['name'],
                    'token' => Str::random(64),
                    'status' => 'pending',
                ]);

                $createdSigners[] = $signer;
            }

            // Update document status and tracking
            $document->update([
                'status' => 'sent',
                'primary_signer_email' => $signers[0]['email'] ?? null,
                'signer_count' => count($signers),
                'last_activity_at' => now(),
            ]);

            // Send emails to all signers
            foreach ($createdSigners as $signer) {
                $this->sendSigningEmail($document, $signer, $options);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send document for signature', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send signing email to a signer using branded templates and custom SMTP
     */
    protected function sendSigningEmail(Document $document, Signer $signer, array $options = []): void
    {
        try {
            // ALWAYS use .env FROM EMAIL credentials for sending emails
            $envConfig = $this->emailConfigService->getEnvAccount();
            
            if (!$envConfig) {
                throw new \Exception('Email configuration not found in .env file');
            }
            
            $this->emailConfigService->applyConfig($envConfig);

            $signingUrl = url("/sign/{$document->id}/{$signer->token}");
            
            // Determine template based on document type or options
            $template = $options['template'] ?? 'emails.signature.send';
            if ($document->document_type === 'agreement') {
                $template = 'emails.signature.send_agreement';
            }
            
            $subject = $options['subject'] ?? 'Document Signature Request from Bansal Migration';
            $message = $options['message'] ?? "Please review and sign the attached document.";
            
            // Prepare template data
            $templateData = [
                'signerName' => $signer->name,
                'documentTitle' => $document->display_title ?? $document->title,
                'signingUrl' => $signingUrl,
                'emailMessage' => $message,
                'documentType' => $document->document_type ?? 'document',
                'dueDate' => $document->due_at ? $document->due_at->format('F j, Y') : null,
            ];

            // Send email with template
            Mail::send($template, $templateData, function ($message) use ($signer, $subject, $options) {
                $message->to($signer->email, $signer->name)
                       ->subject($subject);
                
                // Add attachments if provided
                if (isset($options['attachments']) && is_array($options['attachments'])) {
                    foreach ($options['attachments'] as $attachment) {
                        if (isset($attachment['path']) && file_exists($attachment['path'])) {
                            $message->attach($attachment['path'], [
                                'as' => $attachment['name'] ?? basename($attachment['path']),
                                'mime' => $attachment['mime'] ?? 'application/octet-stream',
                            ]);
                        }
                    }
                }
            });

            Log::info('Signing email sent', [
                'document_id' => $document->id,
                'signer_email' => $signer->email,
                'template' => $template
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send signing email', [
                'document_id' => $document->id,
                'signer_id' => $signer->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send reminder to a signer using branded template
     */
    public function remind(Signer $signer, array $options = []): bool
    {
        try {
            // Check reminder limits
            if ($signer->reminder_count >= 3) {
                throw new \Exception('Maximum reminders already sent');
            }

            if ($signer->last_reminder_sent_at && $signer->last_reminder_sent_at->diffInHours(now()) < 24) {
                throw new \Exception('Please wait 24 hours between reminders');
            }

            // ALWAYS use .env FROM EMAIL credentials for sending emails
            $envConfig = $this->emailConfigService->getEnvAccount();
            
            if (!$envConfig) {
                throw new \Exception('Email configuration not found in .env file');
            }
            
            $this->emailConfigService->applyConfig($envConfig);

            $document = $signer->document;
            $signingUrl = url("/sign/{$document->id}/{$signer->token}");

            $templateData = [
                'signerName' => $signer->name,
                'documentTitle' => $document->display_title ?? $document->title,
                'signingUrl' => $signingUrl,
                'reminderNumber' => $signer->reminder_count + 1,
                'dueDate' => $document->due_at ? $document->due_at->format('F j, Y') : null,
            ];

            Mail::send('emails.signature.reminder', $templateData, function ($message) use ($signer) {
                $message->to($signer->email, $signer->name)
                       ->subject('Reminder: Please Sign Your Document - Bansal Migration');
            });

            // Update reminder tracking
            $signer->update([
                'last_reminder_sent_at' => now(),
                'reminder_count' => $signer->reminder_count + 1
            ]);

            Log::info('Reminder sent', [
                'signer_id' => $signer->id,
                'reminder_count' => $signer->reminder_count
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send reminder', [
                'signer_id' => $signer->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Void a document
     */
    public function void(Document $document, string $reason = null): bool
    {
        try {
            $document->update([
                'status' => 'voided',
                'last_activity_at' => now(),
            ]);

            // Optionally log the reason
            if ($reason) {
                Log::info('Document voided', [
                    'document_id' => $document->id,
                    'reason' => $reason
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to void document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Associate a document with an entity (Client or Lead)
     */
    public function associate(Document $document, string $entityType, int $entityId, string $note = null): bool
    {
        try {
            $documentableType = match($entityType) {
                'client' => Admin::class,
                'lead' => Lead::class,
                default => throw new \InvalidArgumentException("Invalid entity type: {$entityType}")
            };

            $document->update([
                'documentable_type' => $documentableType,
                'documentable_id' => $entityId,
                'origin' => $entityType,
            ]);

            // Create audit trail entry in document_notes
            DocumentNote::create([
                'document_id' => $document->id,
                'created_by' => auth('admin')->id() ?? 1,
                'action_type' => 'associated',
                'note' => $note ?? "Document associated with {$entityType}",
                'metadata' => [
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
                    'documentable_type' => $documentableType
                ]
            ]);

            // Create activity log on Client/Lead timeline
            if ($entityType === 'client') {
                ActivitiesLog::create([
                    'client_id' => $entityId,
                    'created_by' => auth('admin')->id() ?? 1,
                    'activity_type' => 'document',
                    'subject' => "Document #{$document->id} attached",
                    'description' => $note ?? "Document '{$document->display_title}' was attached to this client"
                ]);
            }

            // Log the association
            Log::info('Document associated', [
                'document_id' => $document->id,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'note' => $note
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to associate document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Associate a document with category-specific storage and matter
     */
    public function associateWithCategory(Document $document, string $entityType, int $entityId, ?int $matterId, string $docCategory, string $note = null): bool
    {
        try {
            $documentableType = match($entityType) {
                'client' => Admin::class,
                'lead' => Lead::class,
                default => throw new \InvalidArgumentException("Invalid entity type: {$entityType}")
            };

            // Determine document type based on category
            $docType = match($docCategory) {
                'visa' => 'visa_documents',
                'personal' => 'personal_documents',
                default => 'general'
            };

            $document->update([
                'documentable_type' => $documentableType,
                'documentable_id' => $entityId,
                'client_matter_id' => $matterId,
                'doc_type' => $docType,
                'origin' => $entityType,
            ]);

            // Create audit trail entry in document_notes
            DocumentNote::create([
                'document_id' => $document->id,
                'created_by' => auth('admin')->id() ?? 1,
                'action_type' => 'associated',
                'note' => $note ?? "Document associated with {$entityType} ({$docCategory})",
                'metadata' => [
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
                    'matter_id' => $matterId,
                    'doc_category' => $docCategory,
                    'doc_type' => $docType,
                    'documentable_type' => $documentableType
                ]
            ]);

            // Create activity log on Client/Lead timeline
            if ($entityType === 'client') {
                $matterText = $matterId ? " (Matter: #{$matterId})" : '';
                ActivitiesLog::create([
                    'client_id' => $entityId,
                    'created_by' => auth('admin')->id() ?? 1,
                    'activity_type' => 'document',
                    'subject' => "Document #{$document->id} attached to {$docCategory} documents{$matterText}",
                    'description' => $note ?? "Document '{$document->display_title}' was attached to this client's {$docCategory} documents"
                ]);
            }

            // Log the association
            Log::info('Document associated with category', [
                'document_id' => $document->id,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'matter_id' => $matterId,
                'doc_category' => $docCategory,
                'doc_type' => $docType,
                'note' => $note
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to associate document with category', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Detach a document from its association
     */
    public function detach(Document $document, string $reason = null): bool
    {
        try {
            $oldEntityType = $document->documentable_type;
            $oldEntityId = $document->documentable_id;
            $entityType = $document->documentable_type === Admin::class ? 'client' : 'lead';

            $document->update([
                'documentable_type' => null,
                'documentable_id' => null,
                'origin' => 'ad_hoc',
            ]);

            // Create audit trail entry
            DocumentNote::create([
                'document_id' => $document->id,
                'created_by' => auth('admin')->id() ?? 1,
                'action_type' => 'detached',
                'note' => $reason ?? "Document detached from {$entityType}",
                'metadata' => [
                    'old_entity_type' => $entityType,
                    'old_entity_id' => $oldEntityId,
                    'old_documentable_type' => $oldEntityType
                ]
            ]);

            // Create activity log on Client/Lead timeline
            if ($oldEntityType === Admin::class && $oldEntityId) {
                ActivitiesLog::create([
                    'client_id' => $oldEntityId,
                    'created_by' => auth('admin')->id() ?? 1,
                    'activity_type' => 'document',
                    'subject' => "Document #{$document->id} detached",
                    'description' => $reason ?? "Document '{$document->display_title}' was detached from this client"
                ]);
            }

            // Log the detachment
            if ($reason) {
                Log::info('Document detached', [
                    'document_id' => $document->id,
                    'reason' => $reason
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to detach document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Auto-suggest association based on signer email
     */
    public function suggestAssociation(string $email): ?array
    {
        // Try to find matching client or lead (both are in admins table with role = 7)
        $entity = Admin::where('email', $email)
            ->where('role', '=', 7)
            ->whereNull('is_deleted')
            ->first();

        if ($entity) {
            // Determine if it's a client or lead based on type field
            $entityType = ($entity->type === 'lead') ? 'lead' : 'client';
            
            if ($entityType === 'client') {
                // Get client's matters
                $matters = \DB::table('client_matters')
                    ->where('client_id', $entity->id)
                    ->join('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                    ->select(
                        'client_matters.id',
                        'client_matters.client_unique_matter_no',
                        'matters.title as matter_title',
                        'client_matters.matter_status'
                    )
                    ->orderBy('client_matters.created_at', 'desc')
                    ->get()
                    ->map(function($matter) {
                        return [
                            'id' => $matter->id,
                            'label' => $matter->client_unique_matter_no . ' - ' . $matter->matter_title,
                            'status' => $matter->matter_status
                        ];
                    })
                    ->toArray();
            } else {
                // Leads don't have matters
                $matters = [];
            }

            return [
                'type' => $entityType,
                'id' => $entity->id,
                'name' => trim("{$entity->first_name} {$entity->last_name}"),
                'email' => $entity->email,
                'matters' => $matters,
                'has_matters' => count($matters) > 0
            ];
        }

        return null;
    }

    /**
     * Archive old drafts
     */
    public function archiveOldDrafts(int $daysOld = 30): int
    {
        $count = Document::where('status', 'draft')
            ->where('created_at', '<', now()->subDays($daysOld))
            ->whereNull('archived_at')
            ->update(['archived_at' => now()]);

        Log::info("Archived {$count} old draft documents");

        return $count;
    }

    /**
     * Get pending count for a user
     */
    public function getPendingCount(int $userId): int
    {
        return Document::forUser($userId)
            ->byStatus('sent')
            ->notArchived()
            ->count();
    }
}

