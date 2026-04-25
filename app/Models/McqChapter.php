<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Used by {@see \App\Http\Controllers\CRM\CRMUtilityController::getChapters} (post /get_chapters).
 * Table name follows Laravel plural convention; adjust if your schema differs.
 */
class McqChapter extends Model
{
    protected $table = 'mcq_chapters';
}
