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
        Schema::create('ai_chat_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->enum('role', ['user', 'assistant']);
            $table->text('content');
            $table->json('tool_calls')->nullable();   // audit log tool calls
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_logs');
    }
};
