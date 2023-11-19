<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arr extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'department',
        'doc_number',
        'creator_name',
        'user_id',
        'date',
        'year',
        'plant',
        'unit',
        'address',
        'asset_name',
        'asset_number',
        'installation_date',
        'make',
    ];


}
