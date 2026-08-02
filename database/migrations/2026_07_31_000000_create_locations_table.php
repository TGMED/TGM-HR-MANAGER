<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('radius_meters')->default(150);
            $table->unsignedInteger('max_accuracy_meters')->default(200);

            // Each site keeps its own working day, so lateness is judged locally.
            $table->time('work_starts_at')->default('09:00:00');
            $table->time('work_ends_at')->default('17:00:00');
            $table->unsignedInteger('grace_minutes')->default(10);
            $table->json('workdays')->nullable();
            $table->string('timezone')->default('Africa/Lagos');

            $table->boolean('is_active')->default(true);
            $table->boolean('accepts_signups')->default(true);

            $table->timestamps();

            $table->index(['is_active', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
