<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hira extends Model
{
    use HasFactory;
    protected $fillable = [
        'department',
        'doc_number',
        'date',
        'year',
        'plant',
        'unit',
        'address',
        'activity_name',
        'sub_activity_name',
        'hazard',
        'start_date',
        'gross_likelihood',
        'gross_impact',
        'gross_ranking',
    ];
}
