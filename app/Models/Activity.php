<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_name', 'plant', 'department', 'division', 'section',
        'unit', 'status', 'year', 'user_id', 'creator_name'
    ];

    public function subActivities()
    {
        return $this->hasMany(SubActivity::class);
    }
}
