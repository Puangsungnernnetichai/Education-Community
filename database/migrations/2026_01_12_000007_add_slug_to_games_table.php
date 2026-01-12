<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Backfill slugs for existing rows.
        $existing = [];
        $games = DB::table('games')->select('id', 'name', 'slug')->orderBy('id')->get();

        foreach ($games as $g) {
            $current = is_string($g->slug) ? trim($g->slug) : '';
            if ($current !== '') {
                $existing[$current] = true;
                continue;
            }

            $base = Str::slug((string) $g->name);
            if ($base === '') {
                $base = 'game';
            }

            $slug = $base;
            $i = 2;
            while (isset($existing[$slug]) || DB::table('games')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i;
                $i++;
            }

            DB::table('games')->where('id', $g->id)->update(['slug' => $slug]);
            $existing[$slug] = true;
        }

        Schema::table('games', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
