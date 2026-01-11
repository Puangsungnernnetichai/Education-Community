<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->string('author_name')->default('Student');
            $table->text('content');
            $table->timestamps();

            $table->index('comment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replies');
    }
};
