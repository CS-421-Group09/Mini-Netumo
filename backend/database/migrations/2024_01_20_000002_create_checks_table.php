<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_id')->constrained()->cascadeOnDelete();
            $table->integer('status_code')->nullable();
            $table->integer('latency_ms')->nullable();
            $table->boolean('is_success')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['target_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};
