<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('eais', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('creator_name');
            $table->string('user_id');
            $table->string('doc_number');
            $table->string('department');
            $table->string('plant');
            $table->string('unit');
            $table->string('address');
            $table->string('date');
            $table->string('year');
            $table->string('activity_name');
            $table->string('sub_activity_name')->nullable();
            $table->string('hazard');
            $table->string('start_date');
            $table->string('gross_likelihood');
            $table->string('gross_impact');
            $table->string('gross_ranking_value');
            $table->string('gross_ranking');
            $table->string('existing_control')->nullable();
            $table->string('completion_date')->nullable();
            $table->string('mitigation_measures')->nullable();
            $table->string('further_action_required')->nullable();
            $table->string('routine_activity')->nullable();
            $table->string('workers_involved')->nullable();
            $table->string('residual_likelihood')->nullable();
            $table->string('residual_impact')->nullable();
            $table->string('residual_ranking_value')->nullable();
            $table->string('residual_ranking')->nullable();
            $table->string('status');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE eais AUTO_INCREMENT = 1");
        DB::unprepared('
        CREATE TRIGGER set_eai_id
        BEFORE INSERT ON eais
        FOR EACH ROW
        BEGIN
            SET NEW.id = CONCAT("E", (SELECT COUNT(*) FROM eais) + 1);
        END
    ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eais');
    }
};
