<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
            $table->boolean('add_user');
            $table->boolean('master_data');
            $table->boolean('make_forms');
            $table->boolean('change_workflow');
            $table->boolean('can_comment');
            $table->boolean('generate_report');
            $table->boolean('create_function');
            $table->boolean('view_function');
            $table->boolean('edit_function');
            $table->boolean('create_creators');
            $table->boolean('can_approve');
            $table->boolean('view_report');
            $table->boolean('dashboard');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
