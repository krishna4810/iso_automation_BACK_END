<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sub_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->string('sub_activity_name');
            $table->string('start_date');
            $table->string('completion_date');
            $table->string('routine_non');
            $table->text('workers_involved');
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_activities');
    }
};
