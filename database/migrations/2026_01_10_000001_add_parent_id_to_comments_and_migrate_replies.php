<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (! Schema::hasColumn('comments', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('comments')
                    ->cascadeOnDelete();

                $table->index(['post_id', 'parent_id']);
            }
        });

        // Migrate existing replies into comments (as nested comments), then drop replies table.
        if (Schema::hasTable('replies')) {
            // Only migrate if there are rows and the expected columns exist.
            $replyColumns = Schema::getColumnListing('replies');
            $commentColumns = Schema::getColumnListing('comments');

            $hasExpectedReplyCols = in_array('comment_id', $replyColumns, true)
                && in_array('user_id', $replyColumns, true)
                && in_array('body', $replyColumns, true);

            $hasExpectedCommentCols = in_array('post_id', $commentColumns, true)
                && in_array('user_id', $commentColumns, true)
                && in_array('parent_id', $commentColumns, true)
                && in_array('body', $commentColumns, true);

            if ($hasExpectedReplyCols && $hasExpectedCommentCols) {
                DB::transaction(function () {
                    $replies = DB::table('replies')
                        ->join('comments', 'comments.id', '=', 'replies.comment_id')
                        ->select([
                            'comments.post_id as post_id',
                            'replies.user_id as user_id',
                            'replies.comment_id as parent_id',
                            'replies.body as body',
                            'replies.created_at as created_at',
                            'replies.updated_at as updated_at',
                        ])
                        ->get();

                    if ($replies->count() > 0) {
                        $payload = $replies->map(fn ($row) => [
                            'post_id' => $row->post_id,
                            'user_id' => $row->user_id,
                            'parent_id' => $row->parent_id,
                            'body' => $row->body,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ])->all();

                        DB::table('comments')->insert($payload);
                    }
                });
            }

            Schema::drop('replies');
        }
    }

    public function down(): void
    {
        // Recreate replies table (best-effort). Note: data cannot be losslessly restored.
        if (! Schema::hasTable('replies')) {
            Schema::create('replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->text('body');
                $table->timestamps();

                $table->index(['comment_id', 'user_id']);
            });
        }

        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }
        });
    }
};
