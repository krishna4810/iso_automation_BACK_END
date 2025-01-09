<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'activity',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    public function logs()
    {
        return $this->hasMany(ActivityLog::class);
    }

}
