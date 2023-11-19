<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('arrs', function (Blueprint $table) {
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
            $table->string('asset_name');
            $table->string('asset_number');
            $table->string('installation_date');
            $table->string('make');
        });

        DB::statement("ALTER TABLE arrs AUTO_INCREMENT = 1");
        DB::unprepared('
        CREATE TRIGGER set_arr_id
        BEFORE INSERT ON arrs
        FOR EACH ROW
        BEGIN
            SET NEW.id = CONCAT("A", (SELECT COUNT(*) FROM arrs) + 1);
        END
    ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arrs');
    }
};
