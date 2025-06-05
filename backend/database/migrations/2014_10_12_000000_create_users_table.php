<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            // Notification preferences
            $table->boolean('notify_on_downtime')->default(true);
            $table->boolean('notify_on_ssl_expiry')->default(true);
            $table->boolean('notify_on_domain_expiry')->default(true);
            $table->json('notification_channels')->nullable();
            $table->string('slack_webhook_url')->nullable();
            $table->string('custom_webhook_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
