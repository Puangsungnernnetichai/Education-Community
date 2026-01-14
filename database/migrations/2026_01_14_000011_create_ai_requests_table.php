<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('feature')->default('advisor');
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->foreignId('prompt_version_id')->nullable()->constrained('prompt_versions')->nullOnDelete();

            $table->string('status')->default('ok'); // ok|error
            $table->unsignedInteger('http_status')->nullable();
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();

            $table->unsignedInteger('latency_ms')->nullable();
            $table->unsignedInteger('input_chars')->nullable();
            $table->unsignedInteger('output_chars')->nullable();

            $table->timestamps();

            $table->index(['feature', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_requests');
    }
};
