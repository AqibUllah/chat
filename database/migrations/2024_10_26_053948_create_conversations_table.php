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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('private'); // 'private' for 1-1 or 'group' for group chat
            $table->foreignId('created_by')->constrained('users'); // User who created the conversation
            $table->string('name')->nullable(); // Optional for group chat
            $table->softDeletes(); // Adds the `deleted_at` column for soft deletes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};