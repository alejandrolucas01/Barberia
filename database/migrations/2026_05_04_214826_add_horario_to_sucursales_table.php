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
        Schema::table('sucursales', function (Blueprint $table) {
            $table->json('dias_apertura')->nullable()->after('telefono');
            $table->time('hora_apertura')->nullable()->after('dias_apertura');
            $table->time('hora_cierre')->nullable()->after('hora_apertura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sucursales', function (Blueprint $table) {
            $table->dropColumn(['dias_apertura', 'hora_apertura', 'hora_cierre']);
        });
    }
};
