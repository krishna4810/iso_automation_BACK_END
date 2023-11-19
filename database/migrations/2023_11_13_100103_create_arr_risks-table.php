<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('arr_risks', function (Blueprint $table) {
            $table->id('risk_id');
            $table->string('asset_id');
            $table->string('risk_statement');
            $table->string('gross_likelihood');
            $table->string('gross_impact');
            $table->string('gross_ranking_value');
            $table->string('gross_ranking');
            $table->string('existing_control');
            $table->text('continues_update')->nullable();
            $table->string('further_action_required');
            $table->string('residual_likelihood');
            $table->string('residual_impact');
            $table->string('residual_ranking_value');
            $table->string('residual_ranking');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void

    {
        Schema::dropIfExists('arr_risks');
    }
};
