<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cita_servicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->onDelete('cascade');
            $table->foreignId('servicio_id')->constrained('servicios')->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate existing appointments to the pivot table (if database runs in local sqlite, etc.)
        try {
            $citas = DB::table('citas')->get();
            foreach ($citas as $cita) {
                // Check if already exists in pivot to avoid duplicate migration
                $exists = DB::table('cita_servicio')
                    ->where('cita_id', $cita->id)
                    ->where('servicio_id', $cita->servicio_id)
                    ->exists();

                if (!$exists) {
                    DB::table('cita_servicio')->insert([
                        'cita_id' => $cita->id,
                        'servicio_id' => $cita->servicio_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log warning or ignore if tables do not exist yet (e.g. fresh migration runs)
            logger()->warning('No existing appointments migrated: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cita_servicio');
    }
};
