<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // downtime, ssl, domain
            $table->text('message');
            $table->json('meta')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->index(['target_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
