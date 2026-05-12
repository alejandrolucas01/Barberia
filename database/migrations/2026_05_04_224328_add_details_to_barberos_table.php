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
        Schema::table('barberos', function (Blueprint $table) {
            $table->string('correo')->nullable();
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barberos', function (Blueprint $table) {
            $table->dropColumn(['correo', 'hora_entrada', 'hora_salida']);
        });
    }
};
