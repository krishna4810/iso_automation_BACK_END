<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'add_user',
        'role_id',
        'role_name',
        'add_user',
        'master_data',
        'make_forms',
        'change_workflow',
        'can_comment',
        'generate_report',
        'create_function',
        'view_function',
        'create_creators',
        'edit_function',
        'can_approve',
        'view_report' ,
    ];
}
