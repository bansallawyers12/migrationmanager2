<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SignatureField extends Authenticatable
{
    protected $fillable = ['document_id', 'signer_id', 'page_number', 'x_position', 'y_position','x_percent','y_percent','width_percent','height_percent'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
