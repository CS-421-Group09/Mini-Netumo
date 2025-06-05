<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->boolean('is_active')->default(true);
            $table->integer('check_frequency')->default(5); // minutes
            $table->timestamp('last_check_at')->nullable();
            $table->timestamp('ssl_checked_at')->nullable();
            $table->integer('ssl_expiry_days')->nullable();
            $table->timestamp('domain_checked_at')->nullable();
            $table->integer('domain_expiry_days')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
