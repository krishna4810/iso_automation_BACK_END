<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'id' => 1,
            'role_name' => 'Viewer',
            'add_user' => false,
            'master_data' => false,
            'make_forms' => false,
            'change_workflow' => false,
            'can_comment' => false,
            'generate_report' => false,
            'create_function' => false,
            'view_function' => false,
            'edit_function' => false,
            'create_creators' => false,
            'can_approve' => false,
            'view_report' => false,
            'dashboard' => true
        ]);

        Role::create([
            'id' => 2,
            'role_name' => 'Admin',
            'add_user' => true,
            'master_data' => true,
            'make_forms' => true,
            'change_workflow' => true,
            'can_comment' => true,
            'generate_report' => true,
            'create_function' => false,
            'view_function' => false,
            'edit_function' => false,
            'create_creators' => false,
            'can_approve' => false,
            'view_report' => false,
            'dashboard' => true

        ]);

        Role::create([
            'id' => 3,
            'role_name' => 'Creator',
            'add_user' => false,
            'master_data' => false,
            'make_forms' => false,
            'change_workflow' => false,
            'can_comment' => false,
            'generate_report' => false,
            'create_function' => true,
            'view_function' => true,
            'edit_function' => true,
            'create_creators' => false,
            'can_approve' => false,
            'view_report' => false,
            'dashboard' => true

        ]);

        Role::create([
            'id' => 4,
            'role_name' => 'IMS Focal Person',
            'add_user' => false,
            'master_data' => false,
            'make_forms' => false,
            'change_workflow' => false,
            'can_comment' => true,
            'generate_report' => false,
            'create_function' => false,
            'view_function' => true,
            'edit_function' => false,
            'create_creators' => false,
            'can_approve' => true,
            'view_report' => false,
            'dashboard' => true

        ]);

        Role::create([
            'id' => 5,
            'role_name' => 'Reviewer',
            'add_user' => false,
            'master_data' => false,
            'make_forms' => false,
            'change_workflow' => false,
            'can_comment' => false,
            'generate_report' => false,
            'create_function' => false,
            'view_function' => true,
            'edit_function' => false,
            'create_creators' => false,
            'can_approve' => true,
            'view_report' => false,
            'dashboard' => true

        ]);

        Role::create([
            'id' => 6,
            'role_name' => 'Acceptance',
            'add_user' => false,
            'master_data' => false,
            'make_forms' => false,
            'change_workflow' => false,
            'can_comment' => true,
            'generate_report' => false,
            'create_function' => false,
            'view_function' => true,
            'create_creators' => false,
            'edit_function' => true,
            'can_approve' => true,
            'view_report' => false,
            'dashboard' => true

        ]);

        Role::create([
            'id' => 7,
            'role_name' => 'Approval',
            'add_user' => false,
            'master_data' => false,
            'make_forms' => false,
            'change_workflow' => false,
            'can_comment' => false,
            'generate_report' => false,
            'create_function' => false,
            'view_function' => true,
            'create_creators' => false,
            'edit_function' => false,
            'can_approve' => true,
            'view_report' => false,
            'dashboard' => true

        ]);

        Role::create([
            'id' => 8,
            'role_name' => 'Final Approved',
            'add_user' => false,
            'master_data' => false,
            'make_forms' => false,
            'change_workflow' => false,
            'can_comment' => false,
            'generate_report' => false,
            'create_function' => false,
            'view_function' => false,
            'create_creators' => false,
            'edit_function' => false,
            'can_approve' => false,
            'view_report' => true,
            'dashboard' => true

        ]);
    }
}
