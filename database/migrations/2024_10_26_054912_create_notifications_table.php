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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Notification recipient
            $table->foreignId('conversation_id')->nullable()->constrained()->onDelete('cascade'); // Optional: link to a conversation
            $table->foreignId('message_id')->nullable()->constrained()->onDelete('cascade'); // Optional: link to a message
            $table->string('type'); // e.g., new_message, user_mentioned, etc.
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
