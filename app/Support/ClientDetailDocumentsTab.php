<?php

namespace App\Support;

use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Document tab query helpers for client detail fragments.
 * One Document query per tab (eager staff); blades group by category in memory.
 */
final class ClientDetailDocumentsTab
{
    public const DIBP_RECEIPT_DOC_TYPE = 'dibp_receipt';

    public const DIBP_RECEIPT_FOLDER_NAME = 'general';

    /**
     * Active personal documents for a client, keyed by folder_name (string).
     *
     * @return Collection<string, Collection<int, Document>>
     */
    public static function personalDocumentsByFolder(int $clientId): Collection
    {
        return Document::with('staff')
            ->where('client_id', $clientId)
            ->whereNull('not_used_doc')
            ->where('doc_type', 'personal')
            ->where('type', 'client')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(static fn (Document $doc): string => (string) ($doc->folder_name ?? ''));
    }

    /**
     * Active visa documents for a client, keyed by folder_name (string).
     * Includes signers for Form 956 / signing UI.
     *
     * @return Collection<string, Collection<int, Document>>
     */
    public static function visaDocumentsByFolder(int $clientId): Collection
    {
        return Document::with(['staff', 'signers'])
            ->where('client_id', $clientId)
            ->whereNull('not_used_doc')
            ->where('doc_type', 'visa')
            ->where('type', 'client')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(static fn (Document $doc): string => (string) ($doc->folder_name ?? ''));
    }

    /**
     * Active DIBP receipt documents for a client and selected matter.
     * Excludes visa/personal/nomination rows via doc_type.
     *
     * @return Collection<int, Document>
     */
    public static function dibpReceiptDocuments(int $clientId, ?int $matterId): Collection
    {
        return Document::with('staff')
            ->where('client_id', $clientId)
            ->whereNull('not_used_doc')
            ->where('doc_type', self::DIBP_RECEIPT_DOC_TYPE)
            ->where('folder_name', self::DIBP_RECEIPT_FOLDER_NAME)
            ->where('type', 'client')
            ->where(function ($query) use ($matterId) {
                if ($matterId !== null) {
                    $query->where('client_matter_id', $matterId);
                } else {
                    $query->whereNull('client_matter_id');
                }
            })
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Always persists as dibp_receipt / general. Caller-supplied doc types are ignored.
     */
    public static function addChecklist(int $clientId, int $userId, string $checklist, ?int $matterId): Document
    {
        $document = new Document;
        $document->user_id = $userId;
        $document->client_id = $clientId;
        $document->type = 'client';
        $document->doc_type = self::DIBP_RECEIPT_DOC_TYPE;
        $document->folder_name = self::DIBP_RECEIPT_FOLDER_NAME;
        $document->client_matter_id = $matterId;
        $document->checklist = $checklist;
        $document->save();

        return $document;
    }

    public static function storageFolder(): string
    {
        return self::DIBP_RECEIPT_DOC_TYPE;
    }

    /**
     * @param  array{file_name: string, filetype: string, myfile: string, myfile_key: string, file_size: int, user_id: int}  $file
     */
    public static function attachFile(Document $document, array $file): Document
    {
        $document->file_name = $file['file_name'];
        $document->filetype = $file['filetype'];
        $document->myfile = $file['myfile'];
        $document->myfile_key = $file['myfile_key'];
        $document->file_size = $file['file_size'];
        $document->user_id = $file['user_id'];
        $document->type = 'client';
        $document->doc_type = self::DIBP_RECEIPT_DOC_TYPE;
        $document->folder_name = self::DIBP_RECEIPT_FOLDER_NAME;
        $document->save();

        return $document;
    }

    public static function isReceipt(Document $document): bool
    {
        return $document->doc_type === self::DIBP_RECEIPT_DOC_TYPE;
    }

    public static function emptyChecklistForName(int $clientId, string $checklist, ?int $matterId): ?Document
    {
        $query = Document::query()
            ->where('client_id', $clientId)
            ->where('doc_type', self::DIBP_RECEIPT_DOC_TYPE)
            ->where('folder_name', self::DIBP_RECEIPT_FOLDER_NAME)
            ->where('type', 'client')
            ->where('checklist', $checklist)
            ->whereNull('not_used_doc')
            ->where(function ($fileQuery) {
                $fileQuery->whereNull('file_name')->orWhere('file_name', '');
            });
        self::constrainReceiptMatter($query, $matterId);

        return $query->first();
    }

    public static function hasChecklistName(int $clientId, string $checklist, ?int $matterId): bool
    {
        $query = Document::query()
            ->where('client_id', $clientId)
            ->where('doc_type', self::DIBP_RECEIPT_DOC_TYPE)
            ->where('folder_name', self::DIBP_RECEIPT_FOLDER_NAME)
            ->where('type', 'client')
            ->where('checklist', $checklist)
            ->whereNull('not_used_doc');
        self::constrainReceiptMatter($query, $matterId);

        return $query->exists();
    }

    /**
     * Prefer an empty receipt row; otherwise create one. Visa/personal rows are ignored.
     * Catalog names with no receipts row yet are created instead of failing as "not found".
     *
     * @param  array{type?: string, name?: string}  $mapping
     */
    public static function resolveChecklistForBulk(int $clientId, int $userId, array $mapping, ?int $matterId): ?Document
    {
        $name = trim((string) ($mapping['name'] ?? ''));
        if ($name === '') {
            return null;
        }

        $empty = self::emptyChecklistForName($clientId, $name, $matterId);
        if ($empty instanceof Document) {
            return $empty;
        }

        return self::addChecklist($clientId, $userId, $name, $matterId);
    }

    /**
     * Resolve a visa/nomination checklist row for bulk upload.
     * Prefers an empty row on this matter, then an empty legacy row with no matter.
     * Creates a new row on this matter when no empty slot exists (including names that
     * only exist on another matter — those previously failed as "not found").
     *
     * @param  array{type?: string, name?: string}  $mapping
     */
    public static function resolveFolderChecklistForBulk(
        int $clientId,
        int $userId,
        string $docType,
        string $folderName,
        string $recordType,
        array $mapping,
        int|string|null $matterId
    ): ?Document {
        $name = trim((string) ($mapping['name'] ?? ''));
        if ($name === '') {
            return null;
        }

        $empty = self::emptyFolderChecklistForName(
            $clientId,
            $docType,
            $folderName,
            $recordType,
            $name,
            $matterId
        );
        if ($empty instanceof Document) {
            return $empty;
        }

        return self::addFolderChecklist(
            $clientId,
            $userId,
            $docType,
            $folderName,
            $recordType,
            $name,
            $matterId
        );
    }

    private static function emptyFolderChecklistForName(
        int $clientId,
        string $docType,
        string $folderName,
        string $recordType,
        string $checklist,
        int|string|null $matterId
    ): ?Document {
        $query = Document::query()
            ->where('client_id', $clientId)
            ->where('doc_type', $docType)
            ->where('folder_name', $folderName)
            ->where('type', $recordType)
            ->where('checklist', $checklist)
            ->whereNull('not_used_doc')
            ->whereNull('file_name');

        if (self::hasMatterId($matterId)) {
            $query->where(function ($inner) use ($matterId) {
                $inner->where('client_matter_id', $matterId)
                    ->orWhereNull('client_matter_id');
            })->orderByRaw('CASE WHEN client_matter_id = ? THEN 0 ELSE 1 END', [$matterId]);
        }

        return $query->first();
    }

    private static function addFolderChecklist(
        int $clientId,
        int $userId,
        string $docType,
        string $folderName,
        string $recordType,
        string $checklist,
        int|string|null $matterId
    ): Document {
        $document = new Document;
        $document->user_id = $userId;
        $document->client_id = $clientId;
        $document->type = $recordType;
        $document->doc_type = $docType;
        $document->folder_name = $folderName;
        $document->checklist = $checklist;
        $document->client_matter_id = self::hasMatterId($matterId) ? $matterId : null;
        $document->save();

        return $document;
    }

    private static function hasMatterId(int|string|null $matterId): bool
    {
        return ! ($matterId === null || $matterId === '' || $matterId === false);
    }

    /**
     * @param  Builder<Document>  $query
     */
    private static function constrainReceiptMatter($query, ?int $matterId): void
    {
        $query->where(function ($inner) use ($matterId) {
            if ($matterId !== null) {
                $inner->where('client_matter_id', $matterId);
            } else {
                $inner->whereNull('client_matter_id');
            }
        });
    }

    /**
     * @return array{id: int, checklist: mixed, file_name: mixed, filetype: string, myfile: mixed, myfile_key: mixed, display_name: ?string}
     */
    public static function jsonPayload(Document $document): array
    {
        $fileName = $document->file_name;

        $hubdocSentAt = self::hasHubdocColumns() ? $document->hubdoc_sent_at : null;

        return [
            'id' => (int) $document->id,
            'checklist' => $document->checklist,
            'file_name' => $fileName,
            'filetype' => $document->getPreviewFileExtension(),
            'myfile' => $document->myfile,
            'myfile_key' => $document->myfile_key,
            'display_name' => $fileName ? $document->getFilenameWithExtensionForDisplay() : null,
            'hubdoc_sent' => self::hasHubdocColumns() ? (bool) $document->hubdoc_sent : false,
            'hubdoc_sent_at' => $hubdocSentAt ? $hubdocSentAt->toIso8601String() : null,
            'hubdoc_sent_at_formatted' => $hubdocSentAt ? $hubdocSentAt->format('d/m/Y H:i') : null,
        ];
    }

    public static function markHubdocSent(Document $document): Document
    {
        if (! self::hasHubdocColumns()) {
            return $document;
        }

        $document->hubdoc_sent = true;
        $document->hubdoc_sent_at = now();
        $document->save();

        return $document;
    }

    public static function hasHubdocColumns(): bool
    {
        return Schema::hasTable('documents') && Schema::hasColumn('documents', 'hubdoc_sent');
    }

    /**
     * Not-used personal/visa/nomination documents for a client (eager staff).
     *
     * @return Collection<int, Document>
     */
    public static function notUsedDocuments(int $clientId): Collection
    {
        return Document::with('staff')
            ->where('client_id', $clientId)
            ->where('not_used_doc', 1)
            ->where('type', 'client')
            ->where(function ($query) {
                $query->where('doc_type', 'personal')
                    ->orWhere('doc_type', 'visa')
                    ->orWhere('doc_type', 'nomination');
            })
            ->orderBy('type', 'DESC')
            ->get();
    }
}
