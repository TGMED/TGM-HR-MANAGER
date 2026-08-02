<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // The site this day was measured against, kept even if staff move.
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->date('work_date');

            $table->timestamp('clocked_in_at')->nullable();
            $table->decimal('clock_in_latitude', 10, 7)->nullable();
            $table->decimal('clock_in_longitude', 10, 7)->nullable();
            $table->unsignedInteger('clock_in_accuracy')->nullable();
            $table->unsignedInteger('clock_in_distance')->nullable();

            $table->timestamp('clocked_out_at')->nullable();
            $table->decimal('clock_out_latitude', 10, 7)->nullable();
            $table->decimal('clock_out_longitude', 10, 7)->nullable();
            $table->unsignedInteger('clock_out_accuracy')->nullable();
            $table->unsignedInteger('clock_out_distance')->nullable();

            $table->string('status')->default('on_time');
            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedInteger('worked_minutes')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'work_date']);
            $table->index('work_date');
            $table->index(['status', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
