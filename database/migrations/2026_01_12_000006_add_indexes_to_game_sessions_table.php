<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->index(['game_id', 'score'], 'game_sessions_game_score_idx');
            $table->index(['user_id', 'score'], 'game_sessions_user_score_idx');
        });
    }

    public function down(): void
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->dropIndex('game_sessions_game_score_idx');
            $table->dropIndex('game_sessions_user_score_idx');
        });
    }
};
