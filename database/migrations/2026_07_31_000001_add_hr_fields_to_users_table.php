<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id')->nullable()->unique()->after('id');
            $table->string('role')->default('staff')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('department')->nullable()->after('phone');
            $table->string('position')->nullable()->after('department');
            $table->date('hired_at')->nullable()->after('position');
            $table->boolean('is_active')->default(true)->after('hired_at');
            $table->timestamp('deactivated_at')->nullable()->after('is_active');

            // The site this person clocks in at. Nullable so a location can be
            // retired without deleting its staff; the app requires one at signup.
            $table->foreignId('location_id')
                ->nullable()
                ->after('deactivated_at')
                ->constrained()
                ->nullOnDelete();

            $table->index(['role', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'is_active']);
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn([
                'employee_id',
                'role',
                'phone',
                'department',
                'position',
                'hired_at',
                'is_active',
                'deactivated_at',
            ]);
        });
    }
};
