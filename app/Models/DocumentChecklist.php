<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;

class DocumentChecklist extends Authenticatable
{
    use Notifiable;
    use Sortable;

    protected $table = 'portal_document_checklists';

    protected $fillable = ['id', 'name', 'doc_type', 'status', 'created_at', 'updated_at'];

    public $sortable = ['id', 'created_at', 'updated_at'];

    public const DOC_TYPE_PERSONAL = 1;

    public const DOC_TYPE_VISA = 2;

    public const DOC_TYPE_NOMINATION = 3;

    public const DOC_TYPE_DIBP_RECEIPT = 4;

    /**
     * @return list<string>
     */
    public static function adminDocTypeValues(): array
    {
        return [
            (string) self::DOC_TYPE_PERSONAL,
            (string) self::DOC_TYPE_VISA,
            (string) self::DOC_TYPE_NOMINATION,
            (string) self::DOC_TYPE_DIBP_RECEIPT,
        ];
    }

    /**
     * Active DIBP Receipt checklist names for the Account tab picker.
     *
     * @return Collection<int, DocumentChecklist>
     */
    public static function activeDibpReceipts(): Collection
    {
        return static::query()
            ->where('status', 1)
            ->where('doc_type', self::DOC_TYPE_DIBP_RECEIPT)
            ->orderBy('name')
            ->get();
    }
}
