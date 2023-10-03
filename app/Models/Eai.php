<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eai extends Model
{
    use HasFactory;
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
        'activity_name',
        'sub_activity_name',
        'hazard',
        'start_date',
        'gross_likelihood',
        'gross_impact',
        'gross_ranking',
        'gross_ranking_value',
        'existing_control',
        'completion_date',
        'mitigation_measures',
        'further_action_required',
        'routine_activity',
        'workers_involved',
        'residual_likelihood',
        'residual_impact',
        'residual_ranking_value',
        'residual_ranking',
        'status',
    ];
}
