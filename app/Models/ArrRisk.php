<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArrRisk extends Model
{
    use HasFactory;
    protected $primaryKey = 'risk_id';
    protected $fillable = [
        'asset_id',
        'risk_statement',
        'gross_likelihood',
        'gross_impact',
        'gross_ranking',
        'gross_ranking_value',
        'existing_control',
        'further_action_required',
        'residual_likelihood',
        'residual_impact',
        'residual_ranking_value',
        'residual_ranking',
        'status',
    ];
    }
