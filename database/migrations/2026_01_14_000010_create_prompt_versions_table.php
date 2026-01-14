<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prompt_id')->constrained('prompts')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->longText('content');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['prompt_id', 'version']);
        });

        Schema::table('prompts', function (Blueprint $table) {
            $table->foreign('active_prompt_version_id')
                ->references('id')
                ->on('prompt_versions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->dropForeign(['active_prompt_version_id']);
        });

        Schema::dropIfExists('prompt_versions');
    }
};
