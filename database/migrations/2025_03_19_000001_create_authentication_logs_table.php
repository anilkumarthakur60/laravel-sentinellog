<?php

declare(strict_types=1);

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
        Schema::create(config('sentinel-log.table_name', 'authentication_logs'), function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable');
            $table->string('event_name', 50);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('device_info')->nullable();
            $table->json('location')->nullable();
            $table->boolean('is_successful')->default(false);
            $table->timestamp('event_at')->nullable()->useCurrent();
            $table->timestamp('cleared_at')->nullable();
            $table->timestamps();
            $table->index(['authenticatable_type', 'authenticatable_id'], 'auth_logs_auth_type_auth_id_idx');
            $table->index('event_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('sentinel-log.table_name', 'authentication_logs'));
    }
};