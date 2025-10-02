<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientTravelInformation extends Model
{
    protected $table = 'client_travel_informations'; // The name of the table

    protected $fillable = [
        'client_id',
        'admin_id',
        'travel_country_visited',
        'travel_arrival_date',
        'travel_departure_date',
        'travel_purpose'
    ];
}


