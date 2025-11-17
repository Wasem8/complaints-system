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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('type', [
                'service_missing',
                'power_outage',
                'corruption',
                'employee_misconduct',
                'technical_issue'
            ]);

            $table->enum('authority', [
                'municipality',
                'electric_company',
                'water_authority',
                'health_directorate',
                'other'
            ]);

            $table->text('description');
            $table->string('location_text')->nullable();

            $table->enum('status', ['pending', 'processing', 'done', 'rejected'])->default('pending');
            $table->string('tracking_number')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
