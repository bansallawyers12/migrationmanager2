<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientEoiReference extends Model
{
    protected $table = 'client_eoi_references'; // The name of the table

    protected $fillable = [
        'client_id',
        'admin_id',
        'EOI_number',
        'EOI_subclass',
        'EOI_occupation',
        'EOI_point',
        'EOI_state',
        'EOI_submission_date',
        'EOI_ROI',
        'EOI_password'
    ];
}


