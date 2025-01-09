<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id', 'sub_activity_name', 'start_date',
        'completion_date', 'routine_non', 'workers_involved'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function hazards()
    {
        return $this->hasMany(Hazard::class);
    }
}
