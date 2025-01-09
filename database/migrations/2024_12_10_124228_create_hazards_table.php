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
        Schema::create('hazards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('sub_activity_id');
            $table->string('hazard_name');
            $table->integer('gross_likelihood');
            $table->integer('g_impact');
            $table->string('g_ranking');
            $table->integer('g_ranking_value');
            $table->text('existing_control');
            $table->text('further_action_required');
            $table->text('mitigation_measures');
            $table->integer('residual_likelihood');
            $table->integer('residual_impact');
            $table->string('residual_ranking');
            $table->integer('residual_ranking_value');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('sub_activity_id')->references('id')->on('sub_activities')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hazards');
    }
};
