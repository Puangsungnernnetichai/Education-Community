<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->boolean('is_private')->default(false)->after('author_name');

            $table->index(['user_id', 'is_private']);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_private']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('is_private');
        });
    }
};
