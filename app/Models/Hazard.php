<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hazard extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id', 'sub_activity_id', 'hazard_name',
        'gross_likelihood', 'g_impact', 'g_ranking', 'g_ranking_value',
        'existing_control', 'further_action_required',
        'mitigation_measures', 'residual_likelihood',
        'residual_impact', 'residual_ranking', 'residual_ranking_value'
    ];

    public function subActivity()
    {
        return $this->belongsTo(SubActivity::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}

